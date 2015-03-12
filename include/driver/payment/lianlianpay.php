<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name lianlianpay.php
 * @date 2014-10-30 10:42:13
 */
 



class lianlianpayPaymentDriver extends PaymentDriver
{
	private $lpreturn;
	
	private $is_notify = null;
	
    public function CreateLink($payment, $parameter)
    {
    	
		if (!class_exists('LLpaySubmit')){
			include DRIVER_PATH.'payment/lianlianpay/llpay_submit.class.php';
		}

		$is_wap = in_array(WEB_BASE_ENV_DFS::$APPNAME, array('wap', '3g', 'api'));
		if($is_wap) {
			$payment['config']['version'] = '1.2';
			$payment['config']['app_request'] = '3';
		}
				$lianlianpay = new LLpaySubmit($payment['config']);
		$data = $this->getdata($payment, $parameter);
		if($is_wap) {
						unset($data['version'], $data['userreq_ip']);
			$lianlianpay->llpay_gateway_new = 'https:/' . '/yintong.com.cn/llpayh5/payment.htm';
		}
		if('api' == WEB_BASE_ENV_DFS::$APPNAME) {
			unset($data['app_request'], $data['timestamp'], $data['user_id'], $data['url_return']);
			return $lianlianpay->buildRequestPara($data);
		} else {
			if(true === ENC_IS_GBK) {
				header('Content-Type: text/html; charset=utf-8');
				echo '<meta http-equiv="content-type" content="text/html; charset=UTF-8">';
				exit($lianlianpay->buildRequestForm($data, "post", ENC_G2U("确认连连支付付款")));
			} else {
				return $lianlianpay->buildRequestForm($data, "post", "确认连连支付付款");
			}
		}
    }

    
    public function CreateConfirmLink($payment, $order)
    {
        return '?mod=buy&code=tradeconfirm&id='.$order['orderid'];
    }
    
    public function CallbackVerify($payment)
    {
		unset($_POST['mod']);
		if(true === DEBUG_LIANLIANPAY || 'POST' == $_SERVER['REQUEST_METHOD'] || $_POST){
			if (!class_exists('LLpayNotify')){
				include DRIVER_PATH.'payment/lianlianpay/llpay_notify.class.php';
				include DRIVER_PATH.'payment/lianlianpay/llpay_cls_json.php';
			}
			$lianlianpay = new LLpayNotify($payment['config']);
			if(true === $this->__Is_Nofity()) {
				$return = $lianlianpay->verifyReturn();
			} else {
								sleep(rand(1, 3));
				$return = $lianlianpay->verifyNotify();
			}
			$this->lpreturn = $lianlianpay->notifyResp;
			if(true === $return && 'SUCCESS' == $this->lpreturn['result_pay']) {
				$order = logic('order')->GetOne($this->lpreturn['no_order']);
				if($order && $order['paytype'] == $payment['id']){
					if($order['product']['type'] == 'ticket'){
						return 'TRADE_FINISHED';
					}else{
						return 'WAIT_SELLER_SEND_GOODS';
					}
				}else{
					return 'VERIFY_FAILED';
				}
			}else{
				return 'VERIFY_FAILED';
			}
		}else{
			return 'VERIFY_FAILED';
		}
    }
    
    public function GetTradeData()
    {
        $trade = array();
		$return = $this->lpreturn;
		if($return && is_array($return)){
			$order = logic('order')->GetOne($return['no_order']);
			$trade['sign'] = $return['no_order'];
			$trade['trade_no'] = $return['oid_paybill'];
			$trade['price'] = $return['money_order'];
			$trade['money'] = $trade['price'];
			$trade['status'] = ($order['product']['type'] == 'ticket') ? 'TRADE_FINISHED' : 'WAIT_SELLER_SEND_GOODS';
			$trade['uid'] = $order['userid'];
		}
		return $trade;
    }
    
    public function StatusProcesser($status)
    {
    	if(true === $this->__Is_Nofity()) {
    		return false;
    	}
		if ($status != 'VERIFY_FAILED')
        {
            echo '{"ret_code":"0000","ret_msg":"success"}';
        }
        else
        {
            echo '{"ret_code":"9999","ret_msg":"failed"}';
        }
        return true;
    }
    
    public function GoodSender($payment, $express, $sign, $type)
    {
        if ($type == 'ticket')
        {
            logic('callback')->Bridge($sign)->Processed($sign, 'TRADE_FINISHED');
        }
        else
        {
            logic('callback')->Bridge($sign)->Processed($sign, 'WAIT_BUYER_CONFIRM_GOODS');
        }
    }

	private function getdata($payment, $parameter) {
		
		$parameter['name'] = trim($parameter['name']);
		$data = array(
			'version' => $payment['config']['version'],
			'oid_partner' => $payment['config']['oid_partner'],
			'user_id' => $parameter['userid'],
			'timestamp' => local_date('YmdHis', time()),
			'sign_type' => $payment['config']['sign_type'],
			'busi_partner' => $payment['config']['busi_partner'],
			'no_order' => $parameter['sign'],
			'dt_order' => local_date('YmdHis', time()),
			'name_goods' => (true === ENC_IS_GBK ? array_iconv('gbk', 'utf-8', $parameter['name']) : $parameter['name']),
									'money_order' => $parameter['price'],
			'notify_url' => $parameter['notify_url'],

			'acct_name' => '',
						'id_no' => '',
			'valid_order' => $payment['config']['valid_order'],
			'userreq_ip' => client_ip(),
			'url_return' => $parameter['notify_url'],
					);
		if($payment['config']['app_request']) {
			$data['app_request'] = $payment['config']['app_request'];
		}
		return $data;
	}
	
	private function __Is_Nofity()
	{
		if (is_null($this->is_notify))
		{
			if (post('result_pay'))
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
}
?>