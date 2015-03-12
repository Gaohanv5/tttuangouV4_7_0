<?php

/**
 * 支付方式：货到付款
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package payment
 * @name cod.php
 * @version 1.0
 */

class codPaymentDriver extends PaymentDriver
{
	public function CreateLink($payment, $parameter)
	{
		$html =  '<form action="?mod=callback&pid='.$payment['id'].'" method="post">';
		$html .= '<input type="hidden" name="sign" value="'.$parameter['sign'].'" />';
		$html .= '<p>请确认以上订单信息</p>';
		$html .= '<input type="submit" value=" 下 单 " />';
		$html .= '</form>';
		return $html;
	}
	public function CreateConfirmLink($payment, $order)
	{
		return '?mod=buy&code=tradeconfirm&id='.$order['orderid'];
	}
	public function CallbackVerify($payment,$sign='')
	{
				if (user()->get('id') < 1)
		{
			return 'VERIFY_FAILED';
		}
		$trade = $this->GetTradeData($sign);
		if ($trade['__order__']['paytype'] != $payment['id'])
		{
			return 'VERIFY_FAILED';
		}
		return $trade['status'];
	}
	public function GetTradeData($sign='')
	{
	    if($sign == '')
		  $sign = post('sign', 'number');
		$order = logic('callback')->Bridge($sign)->SrcOne($sign);
		$trade = array();
		$trade['sign'] = $sign;
		$trade['trade_no'] = time();
		$trade['price'] = $order['paymoney'];
		$trade['money'] = 0;
		$trade['nmadd'] = true;
		$trade['nmpay'] = true;
		$trade['status'] = 'WAIT_SELLER_SEND_GOODS';
		$trade['__order__'] = $order;
		return $trade;
	}
	public function StatusProcesser($status)
	{
		return false;
	}
	public function GoodSender($payment, $express, $sign, $type)
	{
		logic('callback')->Bridge($sign)->Processed($sign, 'WAIT_BUYER_CONFIRM_GOODS');
	}
}

?>