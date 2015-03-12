<?php

/**
 * 逻辑区：数据回调相关
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package logic
 * @name callback.logic.php
 * @version 1.1
 */

class CallbackLogic
{


	public function Parser($trade)
	{
		$data = $this->Trade2SID($trade);
		return loadInstance('logic.callback.parser.'.$data['sid'], $data['class_Parser']);
	}

	public function Bridge($trade)
	{
		$data = $this->Trade2SID($trade);
		return loadInstance('logic.callback.bridge.'.$data['sid'], $data['class_Bridge']);
	}

	private function Trade2SID($trade)
	{
		$sign = is_numeric($trade) ? $trade : $trade['sign'];
		$idx = substr($sign, 0, 1);
		if ($idx == 2)
		{
			return array(
				'sid' => 'order',
				'class_Parser' => 'Order_Parser_CallbackLogic',
				'class_Bridge' => 'Order_Bridge_CallbackLogic'
				);
		}
		elseif ($idx == 3)
		{
			return array(
				'sid' => 'recharge',
				'class_Parser' => 'Recharge_Parser_CallbackLogic',
				'class_Bridge' => 'Recharge_Bridge_CallbackLogic'
				);
		}
		else
		{
			return array(
				'sid' => 'default',
				'class_Parser' => 'Default_Parser_CallbackLogic',
				'class_Bridge' => 'Default_Bridge_CallbackLogic'
				);
		}
	}



	protected $master = null;

	public function MasterIframe($object)
	{
		$this->master = $object;
	}
}



class Order_Parser_CallbackLogic extends CallbackLogic
{

	private $message = true;


	public function Virtual_Order_Process($payment = false, $trade = array(), $status = '') {
		$this->message = false;
		if($payment && $trade && $status) {
			$orders = logic('order')->getAllOrdersByOrderid($trade['sign']);
			if(is_array($orders) && count($orders)) {
				logic('order')->virtual_order_data_fix($trade['sign']);
				foreach($orders as $order) {
					 
					$method = 'Parse_' . $status;
					if(method_exists($this, $method)) {
						$this->$method($payment, $order);
					} else {
						exit('method error');
					}
				}
			}
		}
		$this->master->Messager(__('本次交易已经完成！'), '?mod=me&code=order');
	}



	function Parse_VERIFY_FAILED($payment = false, $order = array())
	{
		if (logic('pay')->Process($payment, 'VERIFY_FAILED')) return;
		$this->message && $this->master->Messager(__('支付验证失败！'), '?mod=me&code=order');
	}

	function Parse_WAIT_BUYER_PAY($payment = false, $order = array())
	{
		if(logic('pay')->Process($payment, 'WAIT_BUYER_PAY')) return;
		$this->message && $this->master->Messager(__('订单已经生成，请尽快付款！'), '?mod=me&code=order');
	}

	function Parse_WAIT_SELLER_SEND_GOODS($payment = false,$order = array())
	{
		$trade = logic('pay')->TradeData($payment,$order);
		$order = logic('order')->_TMP_Payed($trade, $payment);
		$error = false;
		isset($order['false']) && $error = $order['false'];
		if (!$error)
		{
			if ($order['product']['type'] == 'ticket')
			{
				logic('pay')->vSendGoods($order);
				if (true == ini('payment.trade.sendgoodsfirst'))
				{
					logic('order')->MakeSuccessed($trade['sign']);
					logic('order')->Processed($trade['sign'], 'WAIT_BUYER_CONFIRM_GOODS');
					logic('order')->clog($trade['sign'])->sys(TUANGOU_STR . '券先行发送给用户，等待用户确认收货', 'WAIT_BUYER_CONFIRM_GOODS');
				}
				else
				{
					notify($order['userid'], 'user.pay.confirm', $trade);
				}
			}
			else
			{
				$isCOD = $trade['nmpay'];
				$isCOD && logic('order')->Update($trade['sign'], array('pay' => ORD_PAID_Yes, 'process' => '__PAY_YET__'));
				logic('order')->MakeSuccessed($trade['sign']);
				$isCOD && logic('order')->Update($trade['sign'], array('pay' => ORD_PAID_No, 'process' => 'WAIT_SELLER_SEND_GOODS'));
			}
		}
		else
		{
			if ($order['_tmpd_']['paid'])
			{
				logic('order')->Update($trade['sign'], array('status'=>ORD_STA_Overdue,'process'=>'__PAY_YET__'));
				logic('order')->clog($trade['sign'])->sys('交易失败（订单已变更为过期）：'.$error, '__PAY_YET__');
				if ($order['_tmpd_']['money'] > 0 && $trade['money'] > 0)
				{
					logic('me')->money()->less($trade['price'], $order['userid'], array(
						'name' => __('无效订单处理'),
						'intro' => sprintf(__('订单号：%s<br/>交易单号：%s<br/><b>担保交易接口支付，资金未生效，请到支付平台申请退款</b>'), $order['orderid'], $trade['trade_no'])
					));
				}
			}
		}
		if(logic('pay')->Process($payment, 'WAIT_SELLER_SEND_GOODS')) return;
		$this->message && $this->master->Messager($error?$error:__('订单已经提交，我们会尽快为您准备发货！'), '?mod=me&code=order');
		return $error;
	}

	function Parse_WAIT_BUYER_CONFIRM_GOODS($payment = false,$order = array())
	{
		$trade = logic('pay')->TradeData($payment,$order);
		if (logic('recharge')->forder()->paid($trade['sign']))
		{
			logic('order')->Processed($trade['sign'], 'WAIT_BUYER_CONFIRM_GOODS');
			logic('order')->clog($trade['sign'])->sys('支付平台通知：等待买家确认收货', 'WAIT_BUYER_CONFIRM_GOODS');
		}
		if(logic('pay')->Process($payment, 'WAIT_BUYER_CONFIRM_GOODS')) return;
		$this->message && $this->master->Messager(__('我们已经发货完毕，请收到货物后尽快确认收货！'), '?mod=me&code=order');
	}

	function Parse_TRADE_FINISHED($payment = false,$order = array())
	{
		$trade = logic('pay')->TradeData($payment,$order);
		//付款
		$order = logic('order')->_TMP_Payed($trade);
		$error = false;
		isset($order['false']) && $error = $order['false'];
		if (!$error)
		{	//等待买家确认收货
			if ($order['process'] == 'WAIT_BUYER_CONFIRM_GOODS')
			{	//更改订单状态为__PAY_YET__,已付款
				logic('order')->Processed($trade['sign'], '__PAY_YET__');
				logic('order')->clog($trade['sign'])->sys('系统内部交易处理变更：用户已付款', '__PAY_YET__');
				$order['product']['type'] == 'stuff' && $ups2TRADE_FINISHED = true;
			}
			//对产品的状态维护
			$order = logic('order')->MakeSuccessed($trade['sign']);
			//更改订单状态为交易完成
			if ($ups2TRADE_FINISHED)
			{
				logic('order')->Processed($trade['sign'], 'TRADE_FINISHED');
				logic('order')->clog($trade['sign'])->sys('系统内部交易处理变更：交易已完成', 'TRADE_FINISHED');

				logic('rebate')->Add_Rebate_For_Item($order);
			}
			//使用代金券状态修改
			$order = logic('card')->Processed($trade['sign'],'Y');
			//to do ...
			
		}
		else
		{
			if ($order['_tmpd_']['paid'])
			{
				logic('order')->Update($trade['sign'], array('status'=>ORD_STA_Overdue,'process'=>'__PAY_YET__'));
				logic('order')->clog($trade['sign'])->sys('交易失败（订单已变更为过期）：'.$error, '__PAY_YET__');
			}
		}
		if(logic('pay')->Process($payment, 'TRADE_FINISHED')) return;
		$this->message && $this->master->Messager($error?$error:__('本次交易已经完成！'), '?mod=me&code=order');
	}
}


class Order_Bridge_CallbackLogic extends CallbackLogic
{

	public function SrcOne($orderid)
	{
		return logic('order')->SrcOne($orderid);
	}

	public function Processed($sign, $process)
	{
		return logic('order')->Processed($sign, $process);
	}
}

//充值
class Recharge_Parser_CallbackLogic extends CallbackLogic
{

	function Parse_VERIFY_FAILED($payment = false)
	{
		if (logic('pay')->Process($payment, 'VERIFY_FAILED')) return;
		$this->master->Messager(__('支付验证失败！'), '?mod=me&code=bill');
	}

	function Parse_WAIT_BUYER_PAY($payment = false)
	{
		if(logic('pay')->Process($payment, 'WAIT_BUYER_PAY')) return;
		$this->master->Messager(__('订单已经生成，请尽快付款！'), '?mod=me&code=bill');
	}

	function Parse_WAIT_SELLER_SEND_GOODS($payment = false)
	{
		$trade = logic('pay')->TradeData($payment);
		$order = logic('recharge')->ccOrder($trade['sign']);
		logic('pay')->vSendGoods($order);
		if(logic('pay')->Process($payment, 'WAIT_SELLER_SEND_GOODS')) return;
		$confirmLinker = logic('pay')->ConfirmLinker($order);
		$this->master->Messager(__('由于您使用了担保交易接口，请您先去确认收货，随后系统会自动为您充值！'), $confirmLinker, 5);
	}

	function Parse_WAIT_BUYER_CONFIRM_GOODS($payment = false)
	{
		$trade = logic('pay')->TradeData($payment);
		$order = logic('recharge')->ccOrder($trade['sign']);
		if(logic('pay')->Process($payment, 'WAIT_BUYER_CONFIRM_GOODS')) return;
		$confirmLinker = logic('pay')->ConfirmLinker($order);
		$this->master->Messager(__('由于您使用了担保交易接口，请您先去确认收货，随后系统会自动为您充值！'), $confirmLinker, 5);
	}

	function Parse_TRADE_FINISHED($payment = false)
	{
		$trade = logic('pay')->TradeData($payment);
		logic('recharge')->MakeSuccessed($trade['sign']);
		if(logic('pay')->Process($payment, 'TRADE_FINISHED')) return;
		$this->master->Messager(__('您已充值成功！'), '?mod=me&code=bill');
	}
}


class Recharge_Bridge_CallbackLogic extends CallbackLogic
{

	public function SrcOne($orderid)
	{
		return logic('recharge')->ccOrder($orderid);
	}

	public function Processed($sign, $process)
	{

	}
}


class Default_Parser_Callbacklogic extends CallbackLogic
{

}

class Default_Bridge_CallbackLogic extends CallbackLogic
{
	public function SrcOne($sign)
	{
		return array();
	}
}
?>