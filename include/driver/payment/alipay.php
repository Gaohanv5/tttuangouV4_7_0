<?php

/**
 * 支付方式：支付宝
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package payment
 * @name alipay.php
 * @version 1.0
 */

class alipayPaymentDriver extends PaymentDriver
{

	private $Gateway_ssl = 'https://mapi.alipay.com/gateway.do?';

	private $Gateway_com = 'http://notify.alipay.com/trade/notify_query.do?';

	private $is_notify = null;

	public function CreateLink($payment, $parameter)
	{
		$parameter['name'] = preg_replace('/\&[a-z]{2,4}\;/i', '', $parameter['name']);
		$parameter['detail'] = str_replace(array('"',"'",'\\','&'), '', $parameter['detail']);

		$post = array(
			'service'           => $payment['config']['service'],
			'payment_type'      => '1',			//支付类型 //必填，不能修改
			'seller_email'		=> $payment['config']['account'],	//卖家支付宝帐户必填
			'partner'			=> $payment['config']['partner'],
			'return_url'		=> $parameter['notify_url'],	//页面跳转同步通知页面路径
			'notify_url'		=> $parameter['notify_url'],	//服务器异步通知页面路径
			'_input_charset'	=> ini('settings.charset'),
			'show_url'			=> $parameter['product_url'],
			'out_trade_no'		=> $parameter['sign'],		//商户订单号 通过支付页面的表单进行传递，注意要唯一！
			'subject'			=> $parameter['name'],		//订单名称
			'body'				=> '',				//订单描述 通过支付页面的表单进行传递
			'price'				=> $parameter['price'],
			'quantity'			=> 1,
			'logistics_fee'		=> '0.00',
			'logistics_type'	=> 'EXPRESS',
			'logistics_payment'	=> 'SELLER_PAY',
		);
		if ($payment['config']['service'] == 'create_partner_trade_by_buyer')
		{
			$parameter['addr_name'] || $parameter['addr_name'] = 'USER';
			$parameter['addr_address'] || $parameter['addr_address'] = 'ADDRESS';
			$parameter['addr_zip'] || $parameter['addr_zip'] = '000000';
			$parameter['addr_phone'] || $parameter['addr_phone'] = '13000000000';
			$post['receive_name']		= $parameter['addr_name'];
			$post['receive_address']	= $parameter['addr_address'];
			$post['receive_zip']		= $parameter['addr_zip'];
			$post['receive_phone']		= $parameter['addr_phone'];
		}
		$token = account('ulogin')->token();
		if ($token)
		{
			$post['token'] = $token;
		}
		$post['extend_param'] = 'isv^tt11';
		return $this->__BuildForm($payment, $post);
	}

	public function CreateConfirmLink($payment, $order)
	{
		if ($payment['config']['service'] == 'create_direct_pay_by_user' || $this->isDirectPay($payment, $order['orderid']))
		{
			return '?mod=buy&code=tradeconfirm&id='.$order['orderid'];
		}
		else
		{
			$paylog = logic('pay')->GetLog($order['orderid'], 0, '1', true);
			return 'http://lab.alipay.com/consume/record/buyerConfirmTrade.htm?tradeNo='.$paylog['trade_no'];
		}
	}
	#支付验证
	#返回支付状态
	public function CallbackVerify($payment)
	{
		if ($this->__Is_Nofity())
		{
			sleep(rand(1, 9));
			$trade_status = $this->__Notify_Verify($payment);
		}
		else
		{
			$trade_status = $this->__Return_Verify($payment);
		}
		return $this->__Trade_Status($trade_status);
	}

	public function GetTradeData()
	{
		$src = ($this->__Is_Nofity()) ? 'POST' : 'GET';
		$trade = array();
		$trade['sign'] = logic('safe')->Vars($src, 'out_trade_no', 'number');	//商户订单号
		$trade['trade_no'] = logic('safe')->Vars($src, 'trade_no', 'number');	 //支付宝交易号
		$trade['price'] = logic('safe')->Vars($src, 'total_fee', 'float');		//交易金额
		$trade['money'] = $trade['price'];
		$trade['status'] = $this->__Trade_Status(logic('safe')->Vars($src, 'trade_status', 'txt'));	//交易状态
		return $trade;
	}

	public function StatusProcesser($status)
	{
		if (!$this->__Is_Nofity())
		{
			return false;
		}
		if ($status != 'VERIFY_FAILED')
		{
			echo 'success';
		}
		else
		{
			echo 'failed';
		}
		return true;
	}

	public function GoodSender($payment, $express, $sign, $type)
	{
		if ($payment['config']['service'] == 'create_direct_pay_by_user' || $this->isDirectPay($payment, $sign))
		{
			if ($type == 'ticket')
			{
				logic('callback')->Bridge($sign)->Processed($sign, 'TRADE_FINISHED');
			}
			else
			{
				logic('callback')->Bridge($sign)->Processed($sign, 'WAIT_BUYER_CONFIRM_GOODS');
			}
			return;
		}
		$post = array(
						'service'           => 'send_goods_confirm_by_platform',
			'partner'           => $payment['config']['partner'],
			'_input_charset'    => ini('settings.charset'),
						'trade_no'			=> $express['trade_no'],
			'logistics_name'    => $express['name'],
			'invoice_no'		=> $express['invoice'],
			'transport_type'    => 'EXPRESS',
		);
		$url = $this->__BuildURL($payment, $post);

		$this->__SrvGET($url);
		return;
	}

	private function __Trade_Status($trade_status)
	{
		return ($trade_status == 'TRADE_SUCCESS') ? 'TRADE_FINISHED' : $trade_status;
	}

	private function isDirectPay($payment, $sign)
	{
		$directPay = false;
		if ($payment['config']['service'] == 'trade_create_by_buyer')
		{
			$order = logic('order')->SrcOne($sign);
			$paylog = logic('pay')->GetLog($order['orderid'], $order['userid']);
			$directPay = (count($paylog) == 3 && $paylog[0]['status'] == 'TRADE_FINISHED');
		}
		return $directPay;
	}

	private function __Is_Nofity()
	{
		if (is_null($this->is_notify))
		{
			if (post('trade_status'))
			{
				$this->is_notify = true;
			}
			else
			{
				$this->is_notify = false;
			}
		}
		return $this->is_notify;
	}

	private function __BuildForm($payment, $parameter)
	{
		$sign = $this->__CreateSign($payment, $parameter);
		$url = $this->Gateway_ssl.'_input_charset='.$parameter['_input_charset'];
		$sHtml = '<form id="pay_submit" name="alipaysubmit" action="'.$url.'" method="post" target="_blank">';
		foreach ($parameter as $key => $val)
		{
			$sHtml.= '<input type="hidden" name="'.$key.'" value="'.$val.'"/>';
		}
		$sHtml .= '<input type="hidden" name="sign" value="'.$sign.'"/>';
		$sHtml .= '<input type="hidden" name="sign_type" value="MD5"/>';
		$sHtml .= '<input type="submit" value="支付宝付款" class="formbutton formbutton_ask" onclick="javascript:$.hook.call(\'pay.button.click\');" >';
		$sHtml .= '</form>';
		return $sHtml;
	}

	private function __BuildURL($payment, $parameter)
	{
		$sign = $this->__CreateSign($payment, $parameter);
		$parameter = $this->__arg_sort($parameter);
		$arg = $this->__create_linkstring_urlencode($parameter);
		$url = $this->Gateway_ssl.$arg.'&sign='.$sign.'&sign_type='.'MD5';
		return $url;
	}

	private function __Return_Verify($payment)
	{
		if($payment['config']['ssl'] == 'true')
		{
			$url = $this->Gateway_ssl
			.'service=notify_verify'
			.'&partner='.$payment['config']['partner']
			.'&notify_id='.get('notify_id', 'txt');
		}
		else
		{
			$url = $this->Gateway_com
			.'partner='.$payment['config']['partner']
			.'&notify_id='.get('notify_id', 'txt');
		}

		$result = $this->__Verify($url);

		$parameter = $this->__para_filter($_GET);
		$parameter = $this->__arg_sort($parameter);
		$sign  = $this->__CreateSign($payment, $parameter);

		if (preg_match('/true$/i', $result) && $sign == get('sign', 'txt'))
		{
			return get('trade_status', 'txt');
		}
		else
		{
			return 'VERIFY_FAILED';
		}
	}

	private function __Notify_Verify($payment)
	{
		if($payment['config']['ssl'] == 'true')
		{
			$url = $this->Gateway_ssl
			.'service=notify_verify'
			.'&partner='.$payment['config']['partner']
			.'&notify_id='.post('notify_id', 'txt');
		}
		else
		{
			$url = $this->Gateway_com
			.'partner='.$payment['config']['partner']
			.'&notify_id='.post('notify_id', 'txt');
		}

		$result = $this->__Verify($url);

		$parameter = $this->__para_filter($_POST);
		$parameter = $this->__arg_sort($parameter);
		$sign = $this->__CreateSign($payment, $parameter);

		if (preg_match('/true$/i', $result) && $sign == post('sign', 'txt'))
		{
			return post('trade_status', 'txt');
		}
		else
		{
			return 'VERIFY_FAILED';
		}
	}

	private function __Verify($url, $time_out = '6')
	{
		$urlArr     = parse_url($url);
		$errNo      = '';
		$errStr     = '';
		$transPorts = '';
		if($urlArr['scheme'] == 'https')
		{
			$transPorts = 'ssl://';
			$urlArr['port'] = '443';
		}
		else
		{
			$transPorts = 'tcp://';
			$urlArr['port'] = '80';
		}
		$fp = msockopen($transPorts . $urlArr['host'], $urlArr['port'], $errNo, $errStr, $time_out);
		if(!$fp)
		{
			zlog('error')->found('error.msockopen');
			die("ERROR: $errNo - $errStr<br />\n");
		}
		else
		{
			fputs($fp, "POST ".$urlArr["path"]." HTTP/1.1\r\n");
			fputs($fp, "Host: ".$urlArr["host"]."\r\n");
			fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
			fputs($fp, "Content-length: ".strlen($urlArr["query"])."\r\n");
			fputs($fp, "Connection: close\r\n\r\n");
			fputs($fp, $urlArr["query"] . "\r\n\r\n");
			while(!feof($fp))
			{
				$info[]=@fgets($fp, 1024);
			}
			fclose($fp);
			$info = implode(",",$info);
			return $info;
		}
	}

	private function __SrvGET($url, $time_out = '6')
	{
		$urlArr     = parse_url($url);
		$errNo      = '';
		$errStr     = '';
		$transPorts = '';
		if($urlArr['scheme'] == 'https')
		{
			$transPorts = 'ssl://';
			$urlArr['port'] = '443';
		}
		else
		{
			$transPorts = 'tcp://';
			$urlArr['port'] = '80';
		}
		$fp = msockopen($transPorts . $urlArr['host'], $urlArr['port'], $errNo, $errStr, $time_out);
		if(!$fp)
		{
			zlog('error')->found('error.msockopen');
			die("ERROR: $errNo - $errStr<br />\n");
		}
		else
		{
			fputs($fp, "GET ".$urlArr["path"]."?".$urlArr["query"]." HTTP/1.1\r\n");
			fputs($fp, "Host: ".$urlArr["host"]."\r\n");
			fputs($fp, "Connection: close\r\n\r\n");
			while(!feof($fp))
			{
				$info[]=@fgets($fp, 1024);
			}
			fclose($fp);
			$info = implode(",",$info);
			return $info;
		}
	}

	private function __CreateSign($payment, $parameter)
	{
		$parameter = $this->__para_filter($parameter);
		$parameter = $this->__arg_sort($parameter);
		$string = $this->__create_linkstring($parameter);
		$string .= $payment['config']['key'];
		$string = md5($string);
		return $string;
	}
	private function __create_linkstring($array)
	{
		$arg  = '';
		foreach ($array as $key => $val)
		{
			$arg .= $key.'='.$val.'&';
		}
		$arg = substr($arg, 0, count($arg)-2);
		return $arg;
	}
	private function __create_linkstring_urlencode($array)
	{
		$arg  = '';
		foreach ($array as $key => $val)
		{
			if ($key != 'service' && $key != '_input_charset')
			{
				$arg .= $key.'='.urlencode($val).'&';
			}
			else
			{
				$arg .= $key.'='.$val.'&';
			}
		}
		$arg = substr($arg, 0, count($arg)-2);
		return $arg;
	}
	private function __arg_sort($array)
	{
		ksort($array);
		reset($array);
		return $array;
	}
	private function __para_filter($parameter)
	{
		$ignores = array(
			'sign' => 1,
			'sign_type' => 1,
			'mod' => 1,
			'pid' => 1
		);
		$para = array();
		foreach ($parameter as $key => $val)
		{
			if(isset($ignores[$key]) || $val == '')
			{
				continue;
			}
			else
			{
				$para[$key] = $val;
			}
		}
		return $para;
	}
}

?>