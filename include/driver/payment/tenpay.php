<?php

/**
 * 支付方式：财付通
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package payment
 * @name tenpay.php
 * @version 1.1
 */

class tenpayPaymentDriver extends PaymentDriver
{
    private $is_notify;
    private static $apiLoaded = false;
    private function api($payment = false)
    {
        if (!self::$apiLoaded)
        {
            include DRIVER_PATH.'payment/tenpay.api.php';
            self::$apiLoaded = true;
        }
        $api = loadInstance('driver.payment.tenpay.api', 'exTenpayAPIDriver');
        $payment && $api->config($payment['config']);
        return $api;
    }
    
    public function CreateLink($payment, $parameter)
    {
                $parameter['name'] = preg_replace('/\&[a-z]{2,4}\;/i', '', $parameter['name']);
        $parameter['detail'] = str_replace(array('"',"'",'\\','&'), '', $parameter['name']);
                $this->api($payment);
                $this->INITParameter_all($payment, $parameter);
        return $this->__BuildForm($this->api()->hd('request')->getRequestURL());
    }
    private function INITParameter_all($payment, $parameter)
    {
								$this->api()->hd('request')->setParameter("partner", $payment['config']['bargainor']);
		$this->api()->hd('request')->setParameter("out_trade_no", $parameter['sign']);
		$this->api()->hd('request')->setParameter("total_fee", $parameter['price'] * 100);
		$this->api()->hd('request')->setParameter("return_url", $parameter['notify_url']);
		$this->api()->hd('request')->setParameter("notify_url", $parameter['notify_url']);
		$this->api()->hd('request')->setParameter("body", cut_str($parameter['detail'], 88));
		$this->api()->hd('request')->setParameter("bank_type", 'DEFAULT');
				$this->api()->hd('request')->setParameter("spbill_create_ip", $_SERVER['REMOTE_ADDR']);
		$this->api()->hd('request')->setParameter("fee_type", "1");
		$this->api()->hd('request')->setParameter("subject", $parameter['name']);
				$this->api()->hd('request')->setParameter("sign_type", "MD5");
		$this->api()->hd('request')->setParameter("service_version", "1.0");
		$this->api()->hd('request')->setParameter("input_charset", ini('settings.charset'));
		$this->api()->hd('request')->setParameter("sign_key_index", "1");
				$this->api()->hd('request')->setParameter("attach", "");
		$this->api()->hd('request')->setParameter("product_fee", "");
		$this->api()->hd('request')->setParameter("transport_fee", "0");
		$this->api()->hd('request')->setParameter("time_start", date("YmdHis"));
		$this->api()->hd('request')->setParameter("time_expire", "");
		$this->api()->hd('request')->setParameter("buyer_id", "");
		$this->api()->hd('request')->setParameter("goods_tag", "");
		$this->api()->hd('request')->setParameter("transport_desc","");
		$this->api()->hd('request')->setParameter("trans_type","1");
		$this->api()->hd('request')->setParameter("agentid","");
		$this->api()->hd('request')->setParameter("agent_type","");
		$this->api()->hd('request')->setParameter("seller_id","");
				if ($payment['config']['service'] == 'direct')
		{
			 $this->api()->hd('request')->setParameter("trade_mode", 1); 		}
		if ($payment['config']['service'] == 'medi')
		{
			 $this->api()->hd('request')->setParameter("trade_mode", 2); 		}
    }
    
    public function CreateConfirmLink($payment, $order)
    {
        return '?mod=buy&code=tradeconfirm&id='.$order['orderid'];
    }
    
    public function CallbackVerify($payment)
    {
    	$this->api($payment);
    	if ($this->__Is_Nofity())
    	{
			if ($this->api()->hd('response')->isTenpaySign())
			{
				$this->api()->hd('http')->setReqContent($this->api()->hd('request.notify')->getRequestURL());
				if ($this->api()->hd('http')->call())
				{
					$this->api()->hd('client')->setContent($this->api()->hd('http')->getResContent());
					if ($this->api()->hd('client')->isTenpaySign() && $this->api()->hd('client')->getParameter('retcode') == 0)
					{
						return $this->TradeStatusCode2String($this->api()->hd('response')->getParameter('trade_mode'), $this->api()->hd('response')->getParameter('trade_state'));
					}
					else
					{
						return 'VERIFY_FAILED';
					}
				}
			}
			else
			{
				return 'VERIFY_FAILED';
			}
    	}
    	else
    	{
    		if ($this->api()->hd('response')->isTenpaySign())
			{
				return $this->TradeStatusCode2String($this->api()->hd('response')->getParameter('trade_mode'), $this->api()->hd('response')->getParameter('trade_state'));
			}
			else
			{
				return 'VERIFY_FAILED';
			}
    	}
    }
    private function TradeStatusCode2String($mode, $staCode)
    {
    	$mode == 1 && $mode = 'direct';
    	$mode == 2 && $mode = 'medi';
    	if ($mode == 'direct')
    	{
			$map = array(
				'0' => 'TRADE_FINISHED',
				'-1' => 'VERIFY_FAILED'
			);
    	}
		if ($mode == 'medi')
		{
			$map = array(
				'0' => 'WAIT_SELLER_SEND_GOODS',
				'1' => 'WAIT_BUYER_PAY',
				'2' => 'WAIT_BUYER_PAY',
				'4' => 'WAIT_BUYER_CONFIRM_GOODS',
				'5' => 'TRADE_FINISHED',
				'-1' => 'VERIFY_FAILED'
			);
		}
		if ($map)
		{
			if (isset($map[$staCode]))
			{
				return $map[$staCode];
			}
			else
			{
				return $map['-1'];
			}
		}
		else
		{
			return 'VERIFY_FAILED';
		}
    }
    
    public function GetTradeData()
    {
        $trade = array();
        $trade['sign'] = $this->api()->hd('response')->getParameter('out_trade_no');
        $trade['trade_no'] = $this->api()->hd('response')->getParameter('transaction_id');
        $trade['price'] = (float)($this->api()->hd('response')->getParameter('total_fee') / 100);
        $trade['money'] = $trade['price'];
        $trade['status'] = $this->TradeStatusCode2String($this->api()->hd('response')->getParameter('trade_mode'), $this->api()->hd('response')->getParameter('trade_state'));
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
        if ($type == 'ticket')
        {
            $tradeStatus = $payment['config']['service'] == 'direct' ? 'TRADE_FINISHED' : 'WAIT_SELLER_SEND_GOODS';
            logic('callback')->Bridge($sign)->Processed($sign, $tradeStatus);
        }
        else
        {
            logic('callback')->Bridge($sign)->Processed($sign, 'WAIT_BUYER_CONFIRM_GOODS');
        }
        return;
    }
    private function __Is_Nofity()
    {
        if (is_null($this->is_notify))
        {
            if (count($_COOKIE) == 0)
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
    
    private function __BuildForm($url)
    {
		$sHtml = '<form id="pay_submit" name="tenpaysubmit" action="'.$this->api()->hd('request')->getGateUrl().'" method="post" target="_blank">';
		$params = $this->api()->hd('request')->getAllParameters();
		foreach ($params as $key => $val)
		{
			$sHtml.= '<input type="hidden" name="'.$key.'" value="'.$val.'"/>';
		}
		$sHtml .= '<input type="submit" value="财付通付款" class="formbutton formbutton_ask" onclick="javascript:$.hook.call(\'pay.button.click\');" >';
		$sHtml .= '</form>';
		return $sHtml;
    }
}

?>