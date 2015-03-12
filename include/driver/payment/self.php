<?php

/**
 * 支付方式：余额付款
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package payment
 * @name self.php
 * @version 1.1
 */

class selfPaymentDriver extends PaymentDriver
{
	public function CreateLink($payment, $parameter)
	{
		$market_account = dbc(DBCMax)->select('members')->where(array('uid'=>MEMBER_ID))->limit(1)->done();		
		if($market_account['email2']){
			$url  	 =  rewrite('?mod=me&code=setting');
			$market_note = '&nbsp;&nbsp;<a href="'.$url.'" target="_blank">点此修改登录密码</a>';
		}

		$html =  '<form action="?mod=callback&pid='.$payment['id'].'" method="post">';
		$html .= '<input type="hidden" name="sign" value="'.$parameter['sign'].'" /><br/>';
		$html .= '请输入您的登录密码：<input type="password" name="password" class="input_h"/>';
		$html .= '<input type="submit" value=" 提交 " class="btn btn-primary" style="float:none;" />'.$market_note;
		$html .= '</form>';
		return $html;
	}
	public function CreateConfirmLink($payment, $order)
	{
		return '?mod=buy&code=tradeconfirm&id='.$order['orderid'];
	}
	
	#支付验证
	#返回支付状态
	public function CallbackVerify($payment)
	{
		#第一步，是否登录
		$user = user()->get();
		if ($user['id'] < 1)
		{
			return 'VERIFY_FAILED';
		}
		$password = post('password', 'txt');
		#第二步，支付密码是否正确
		if (account()->password($password, $user) != $user['password'])
		{
			return 'VERIFY_FAILED';
		}
		#第三步，支付方式是否跟订单中支付方式一致
		$trade = $this->GetTradeData();
		if ($trade['__order__']['paytype'] != $payment['id'])
		{
            return 'VERIFY_FAILED';
		}
		#第四步，校验一下用户帐户余额是否大于要支付的订单金额
		if(false == logic('me')->money()->check($user['id'], $trade['price'])) {
			return 'VERIFY_FAILED';
		}
		return $trade['status'];
	}
	
	#根据订单号取出product(并且支付状态做修改)，如不存在该product，返回空数组
	public function GetTradeData($sign = '')
	{
		if($sign == '')
			$sign = post('sign', 'number');
			
		$order = logic('callback')->Bridge($sign)->SrcOne($sign);
		$order && $product = logic('product')->SrcOne($order['productid']);
		$trade = array();
        $trade['sign'] = $sign;
        $trade['trade_no'] = time();
        $trade['price'] = $order['paymoney'];
        $trade['money'] = 0;
        $trade['nmadd'] = true;
        $trade['status'] = ($product['type'] == 'ticket') ? 'TRADE_FINISHED' : 'WAIT_SELLER_SEND_GOODS';
        $trade['__order__'] = $order;
        return $trade;
	}
	public function StatusProcesser($status)
	{
		return false;
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
}

?>