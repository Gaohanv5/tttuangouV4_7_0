<?php

/**
 * 支付方式：易宝一键支付
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package payment
 * @name yeepay.php
 * @version 1.0
 */
class yeepayPaymentDriver extends PaymentDriver
{
	private $ypreturn;
	
    public function CreateLink($payment, $parameter)
    {
		if (!class_exists('yeepayMPay')){
			include DRIVER_PATH.'payment/yeepay/yeepayMPay.php';
		}
		$yeepay = new yeepayMPay($payment);
		$data = $this->getdata($payment, $parameter);
		if($payment['site'] == 'pc_web'){
			$request = $yeepay->pcWebPay($data);
			$back_url = $this->build_web_url($request);
		}elseif($payment['site'] == 'mobile_debit'){
			$back_url = $yeepay->debitWebPay($data);
		}elseif($payment['site'] == 'mobile_credit'){
			$back_url = $yeepay->creditWebPay($data);
		}
		return $back_url;
    }

    
    public function CreateConfirmLink($payment, $order)
    {
        return '?mod=buy&code=tradeconfirm&id='.$order['orderid'];
    }
    
    public function CallbackVerify($payment)
    {
		unset($_POST['mod']);
		if($_POST){
			if (!class_exists('yeepayMPay')){
				include DRIVER_PATH.'payment/yeepay/yeepayMPay.php';
			}
			$yeepay = new yeepayMPay($payment);
			$this->ypreturn = $return = $yeepay->callback($_POST['data'], $_POST['encryptkey']);
			if(is_array($return) && $return['status'] == 1){
				$order = logic('order')->GetOne($return['orderid']);
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
		$return = $this->ypreturn;
		if($return && is_array($return)){
			$order = logic('order')->GetOne($return['orderid']);
			$trade['sign'] = $return['orderid'];
			$trade['trade_no'] = $return['yborderid'];
			$trade['price'] = round($return['amount']/100,2);
			$trade['money'] = $trade['price'];
			$trade['status'] = ($order['product']['type'] == 'ticket') ? 'TRADE_FINISHED' : 'WAIT_SELLER_SEND_GOODS';
			$trade['uid'] = $return['identityid'];
		}
		return $trade;
    }
    
    public function StatusProcesser($status)
    {
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
            logic('callback')->Bridge($sign)->Processed($sign, 'TRADE_FINISHED');
        }
        else
        {
            logic('callback')->Bridge($sign)->Processed($sign, 'WAIT_BUYER_CONFIRM_GOODS');
        }
    }

	private function getdata($payment, $parameter){
		$data = array(
			'orderid'	=>	$parameter['sign'],
			'amount'	=>	$parameter['price'] * 100,
			'productcatalog'=>	$payment['config']['productcatalog'],
			'productname'	=>	ENC_IS_GBK ? ENC_G2U($parameter['name']) : $parameter['name'],
			'identityid'	=>	$parameter['userid'],
			'backurl'	=>	$parameter['notify_url'],
			'fbackurl'	=>	ini('settings.site_url').'/yeepay.html'
		);
		return $data;
	}

	private function build_web_url($request){
		$return_url .= '<form id="pay_submit" name="chinabanksubmit" action="'.$request['url'].'" method=post target="_blank">';
		$return_url .= "<input type=hidden name='merchantaccount' value='".$request['merchantaccount']."'>";
		$return_url .= "<input type=hidden name='data' value='".$request['data']."'>";
		$return_url .= "<input type=hidden name='encryptkey' value='".$request['encryptkey']."'>";
		$return_url .= '<input type=submit value="易宝付款" class="formbutton formbutton_ask" onclick="javascript:$.hook.call(\'pay.button.click\');">';
		$return_url .= "</form>";
		return $return_url;
	}
}
?>