<?php

 /**
 * 逻辑区：支付管理
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package logic
 * @name pay.logic.php
 * @version 1.1
 */

class PayLogic
{
	
	public function html( $data )
	{
		switch (mocod())
		{
			case 'buy.order':
				$pay_money = $data['price_of_total'];
				#多产品产品类型的确定，从而确定支付方式
				$product_type = isset($data['product']['typealis']) ? $data['product']['typealis'] : $data['product']['type'];
				$product_id = $data['product']['id'];
				include handler('template')->file('@html/pay_selector');
				break;
			case 'recharge.order':
				$pay_money = $data['money'];
				$product_type = 'recharge';
				include handler('template')->file('@html/pay_selector');
				break;
		}
	}
	
	public function plugin_has_ext_html($code)
	{
		return method_exists(logic('pay')->apiz($code), 'inner_ext_html');
	}
	
	public function plugin_ouput_ext_html($code)
	{
		return logic('pay')->apiz($code)->inner_ext_html();
	}
	
	public function GetOne($choose)
	{
		$sql_where = '0';
		if ( is_numeric($choose) )
		{
			$sql_where = 'id = '.$choose;
		}
		else
		{
			$sql_where = 'code = "'.$choose.'"';
		}
		$sql = 'SELECT *
		FROM
			' . table('payment') .'
		WHERE
			' . $sql_where;
		return $this->__parse_payment(dbc(DBCMax)->query($sql)->limit(1)->done());
	}
	
	public function GetList()
	{
		$sql_filter = '1';
				$lsrc = handler('cookie')->GetVar('loginSource');
		if ($lsrc && $lsrc == 'alipay')
		{
			$sql_filter = 'code NOT IN("tenpay", "bank")';
		}
				$sql = 'SELECT *
		FROM
			' . table('payment') . '
		WHERE
			enabled = "true"
		AND
			' . $sql_filter . '
		ORDER BY
			`order`
		ASC';
		return $this->__parse_sys_order($this->__parse_payment(dbc(DBCMax)->query($sql)->done()));
	}
	
	private function __parse_sys_order($payments)
	{
		$has_sys_order = false;
		$pool_sys_order = array();
		if($payments){
		foreach ($payments as $i => $payment)
		{
			$apiz = logic('pay')->apiz($payment['code']);
            if (method_exists($apiz, 'inner_disabled') && $apiz->inner_disabled())
            {
				unset($payments[$i]);
            }
            else
            {
                if (method_exists($apiz, 'inner_sys_order'))
                {
                    $has_sys_order = true;
                    $pool_sys_order[$i] = $apiz->inner_sys_order();
                }
                else
                {
                    $pool_sys_order[$i] = $payment['order'];
                }
            }
		}
		}
		if ($has_sys_order)
		{
			$payments_ro = array();
			asort($pool_sys_order);
			foreach ($pool_sys_order as $i => $order)
			{
				$payments_ro[] = $payments[$i];
			}
			$payments = $payments_ro;
		}
		return $payments;
	}
	
	private function __parse_payment($data)
	{
		if ( ! $data ) return false;
		if ( is_array($data[0]) )
		{
			$return = array();
			foreach ( $data as $i => $one )
			{
				$result = $this->__parse_payment($one);
				if ($result)
				{
					$return[] = $result;
				}
			}
			return $return;
		}
		$data['config'] = ($data['config'] == '') ? array() : unserialize($data['config']);
		return $data;
	}
	
	public function SrcOne($choose)
	{
		$sql_where = '0';
		if ( is_numeric($choose) )
		{
			$sql_where = 'id = '.$choose;
		}
		else
		{
			$sql_where = 'code = "'.$choose.'"';
		}
		$sql = 'SELECT * FROM ' . table('payment') .' WHERE ' . $sql_where;
		return dbc(DBCMax)->query($sql)->limit(1)->done();
	}
	
	public function SrcList()
	{
		$sql = 'SELECT *
		FROM
			' . table('payment') . '
		ORDER BY
			`order`
		ASC';
		return dbc(DBCMax)->query($sql)->done();
	}
	
	public function Update($data, $where)
	{
		dbc()->SetTable(table('payment'));
		dbc()->Update($data, $where);
	}
	
	public function OrderPaidSQL()
	{
				$cod = $this->SrcOne('cod');
		$sql = '(pay = '.ORD_PAID_Yes.' OR (paytype='.$cod['id'].' AND `process`="WAIT_SELLER_SEND_GOODS"))';
		return $sql;
	}
	
	public function apiz($code)
	{
		if (is_numeric($code))
		{
			$payment = $this->SrcOne($code);
			$code = $payment['code'];
		}
		$SID = 'payment.driver.api.'.$code;
		$api = moSpace($SID);
		if (!$api)
		{
		    $storage = driver('payment')->load($code);
		    $api = moSpace($SID, $storage);
		}
		return $api;
	}
	
	public function Linker($payment, $parameter)
	{
		if(empty($parameter['sign']) || $parameter['price'] < 0) {
			return false;
		}
		
		$linker = $this->apiz($payment['code'])->CreateLink($payment, $parameter);
		$log_data = array(
			'type' => $payment['id'],
			'sign' => $parameter['sign'],
			'money' => $parameter['price'],
			'uid' => $parameter['userid']		);
		$this->__LogCreate($log_data) && logic('order')->Processed($parameter['sign'], 'WAIT_BUYER_PAY');
		return $linker;
	}
	
	public function ConfirmLinker($order)
	{
		$payment = $this->GetOne($order['paytype']);
		return $this->apiz($payment['code'])->CreateConfirmLink($payment, $order);
	}
	
	public function Verify($payment)
	{
		
		$status = $this->apiz($payment['code'])->CallbackVerify($payment);//调用include/driver/payment/对应的类
		if ($status != 'VERIFY_FAILED')
		{
			$trade = $this->TradeData($payment);
			$trade['uid'] = $trade['uid'] ? $trade['uid'] : 0;
			$this->__LogUpdate($trade['sign'], $trade['trade_no'], $status, $trade['uid']);
		}
		return $status;
	}
	
	public function TradeData($payment,$order = array())
	{
		if($order) {
			return $this->VirtualOrderTradeData($order, $payment);
		} else {
			return $this->apiz($payment['code'])->GetTradeData();
		}
	}
	
	public function VirtualOrderTradeData($order = array(), $payment = false) {
		if(false == $payment) {
			return false;
		}
		$otrade = $this->TradeData($payment);
		if(false == $otrade || false == logic('order')->is_virtual_order($otrade['sign'])) {
			return false;
		}
		if(false == $order) {
			return false;
		}
		

		return array(
			'sign'=>$order['orderid'],
			'trade_no'=>$otrade['trade_no'],
			'price'=>$order['price'],
			'status'=>$otrade['status'], 			
			'uid'=>$order['userid'],		
		);
	}
	
	public function Process($payment, $status)
	{
		return $this->apiz($payment['code'])->StatusProcesser($status);
	}
	
	public function vSendGoods($order)
	{
		return $this->SendGoods($order, false, false);
	}
	
	public function SendGoods($order, $ignore_trade_no = false, $notify_service = true)
	{
		$paylog = $this->GetLog($order['orderid'], 0, '1', true);
		$trade_no = $paylog['trade_no'];
		if (!$ignore_trade_no && !is_numeric($trade_no))
		{
						return;
		}
		if ($order['product']['type'] == 'ticket')
		{
			$name = __('虚拟'. TUANGOU_STR);
			$invoice = sprintf(__('订单号：%s'), $order['orderid']);
		}
		else
		{
			$expressChoose = logic('express')->SrcOne($order['expresstype']);
			$name = $expressChoose['name'];
			$invoice = $order['invoice'];
		}
		$express = array(
			'trade_no' => $trade_no,
			'name' => $name,
			'invoice' => $invoice
		);
		$payment = $this->GetOne($order['paytype']);
		$this->apiz($payment['code'])->GoodSender($payment, $express, $order['orderid'], $order['product']['type']);
		if ($notify_service)
		{
			logic('notify')->Call($order['userid'], 'logic.pay.SendGoods', $express);
		}
	}
	
	public function TD2UID($payment,$sign='')
	{
		$trade = logic('pay')->TradeData($payment,$sign);
		$order = logic('order')->SrcOne($sign);
		$uid = $order['userid'];
		return $uid;
	}
	
	public function GetLog($sign, $uid = 0, $where = '1', $getOne = false)
	{
		$sql_limit_user = '1';
		if ($uid > 0)
		{
			$sql_limit_user = 'uid = '.$uid;
		}
		$sql = 'SELECT *
		FROM
			'.table('paylog').'
		WHERE
			sign = "'.$sign.'"
		AND
			'.$sql_limit_user.'
		AND
			'.$where.'
		ORDER BY
			id
		DESC';
		if ($getOne)
		{
			return dbc(DBCMax)->query($sql)->limit(1)->done();
		}
		else
		{
			return dbc(DBCMax)->query($sql)->done();
		}
	}
	
	public function __LogCreate($data)
	{
		$data['uid'] = ($data['uid'] ? $data['uid'] : user()->get('id'));
		$log = $this->GetLog($data['sign'], $data['uid']);
		if (!$log)
		{
			$data['time'] = time();
			$data['trade_no'] = '__NULL__';
			$data['status'] = '__CREATE__';
			dbc()->SetTable(table('paylog'));
			return dbc()->Insert($data);
		}
		return false;
	}
	
	private function __LogUpdate($sign, $trade_no, $status, $uid=0)
	{
		if (trim($sign) == '' || trim($status) == '') return;
		$uid = $uid ? $uid : user()->get('id');
		$log = $this->GetLog($sign, $uid, 'status="'.$status.'"', true);
		if ($log) return;
		$log = $this->GetLog($sign, $uid, '1', true);
		unset($log['id']);
		$log['time'] = time();
		$log['trade_no'] = $trade_no;
		$log['status'] = $status;
		dbc()->SetTable(table('paylog'));
		dbc()->Insert($log);
		
		logic('payfrom')->log($sign);
	}
	
	public function misc()
	{
		return loadInstance('logic.pay.misc', 'PayLogic_Misc');
	}
}

/**
 * 扩展功能函数
 * @author Moyo <dev@uuland.org>
 */
class PayLogic_Misc
{
	public function ID2Name($flag)
	{
		$payment = logic('pay')->SrcOne($flag);
		if ($payment)
		{
			return $payment['name'];
		}
		else
		{
			return __('未识别');
		}
	}
	public function TradeNO($oid)
	{
		$log = logic('pay')->GetLog($oid, 0, '1', true);
		if ($log['trade_no'] == '__NULL__')
		{
			return __('还未支付');
		}
		else
		{
			return $log['trade_no'];
		}
	}
}
?>