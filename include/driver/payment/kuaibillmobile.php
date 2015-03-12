<?php

/**
 * 支付方式：快钱移动快捷支付
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package payment
 * @name kuaibillmobile.php
 * @version 1.0
 */

class kuaibillmobilePaymentDriver extends PaymentDriver
{
    
    public function inner_disabled()
    {
        return WEB_BASE_ENV_DFS::$APPNAME == 'index';
    }
    
    public function CreateLink($payment, $parameter)
    {
        return $this->rsa_sign($payment,$parameter);
    }

    
    public function CreateConfirmLink($payment, $order)
    {
        return '?mod=buy&code=tradeconfirm&id='.$order['orderid'];
    }
    
    public function CallbackVerify($payment)
    {
		$order = logic('order')->GetOne($_POST['externalTraceNo']);
		if($order && $order['paytype'] == $payment['id']){
			if($_POST['processFlag'] == '0' && $_POST['responseCode'] == '00' && $this->rsa_verify()){
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
    }
    
    public function GetTradeData()
    {
        $order = logic('order')->GetOne($_POST['externalTraceNo']);
		$trade = array();
        $trade['sign'] = $_POST['externalTraceNo'];
        $trade['trade_no'] = $_POST['RRN'];
        $trade['price'] = $_POST['amt'];
        $trade['money'] = $trade['price'];
        $trade['status'] = ($order['product']['type'] == 'ticket') ? 'TRADE_FINISHED' : 'WAIT_SELLER_SEND_GOODS';
		$trade['uid'] = $order['userid'];
        return $trade;
    }
    
    public function StatusProcesser($status)
    {
		if ($status != 'VERIFY_FAILED')
        {
            echo '0';
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
            logic('callback')->Bridge($sign)->Processed($sign, 'TRADE_FINISHED');
        }
        else
        {
            logic('callback')->Bridge($sign)->Processed($sign, 'WAIT_BUYER_CONFIRM_GOODS');
        }
    }

    
    private function rsa_sign($payment,$parameter)
    {
		$query_arr = array(
			'mebCode' => $payment['config']['mebcode'],
			'merchantId' => $payment['config']['merchantid'],
			'partnerUserId' => $parameter['userid']
		);
		$order_arr = array(
			'orderId' => $parameter['sign'],
			'amt' => $parameter['price']*100,
			'merchantName' => ENC_IS_GBK ? ENC_G2U($parameter['seller']) : $parameter['seller'],
			'productName' => ENC_IS_GBK ? ENC_G2U($parameter['name']) : $parameter['name'],
			'unitPrice' => $parameter['perprice']*100,
			'total' => $parameter['total'],
			'merchantOrderTime' => date('YmdHis',time())
		);

		$query_sign = $this->__create_linkstring($query_arr);
		$order_sign = $this->__create_linkstring($order_arr);

		$certdata = $this->getRSAfile('pem');
        $rsa_handler = openssl_get_privatekey($certdata);
        openssl_sign($order_sign, $orderSign, $rsa_handler);
		openssl_sign($query_sign, $querySign, $rsa_handler);
        openssl_free_key($rsa_handler);

		$order_arr['merchantName'] = $parameter['seller'];
		$order_arr['productName'] = $parameter['name'];
		$order_arr['mebCode'] = $payment['config']['mebcode'];
		$order_arr['merchantId'] = $payment['config']['merchantid'];
		$order_arr['partnerUserId'] = $parameter['userid'];
		$order_arr['orderSign'] = base64_encode($orderSign);
		$order_arr['querySign'] = base64_encode($querySign);
		return $order_arr;
    }

    
    private function rsa_verify()
    {
        $string = '';
		$loops = array('processFlag','txnType','orgTxnType','amt','externalTraceNo','orgExternalTraceNo','terminalOperId','authCode','RRN','txnTime','shortPAN','responseCode','cardType','issuerId');
        foreach ($loops as $key){
			if(isset($_POST[$key]) && $_POST[$key] != ''){
				$string .= ENC_IS_GBK ? $_POST[$key] : ENC_U2G($_POST[$key]);
            }
        }
		$sign = $_POST['signature'];
		$_POST['string'] = $string;		$certdata = $this->getRSAfile('cer');
        $rsa_handler = openssl_get_publickey($certdata);
        $result = (bool)openssl_verify($string, base64_decode($sign), $rsa_handler);
        openssl_free_key($rsa_handler);
		$this->__create_logfile($result);
        return $result;
    }

		private function getRSAfile($type='pem')
	{
		$payment = logic('pay')->GetOne('kuaibillmobile');
		$fp = fopen($payment['config'][$type.'file'], "r");
		$certdata = fread($fp, 8192);
		fclose($fp);
		unset($payment);
		return $certdata;
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

		private function __create_logfile($result){
		$filename = DATA_PATH.'kuaibillpaylog/'.date('YmdHis').'.txt';
		$output = date('Y-m-d H:i:s')."\r\n".'result:'.$result."\r\n";
		unset($_POST['mod']);
		$post = var_export($_POST, true);
		$output .= $post."\r\n";
		file_put_contents($filename, $output);
	}
}
?>