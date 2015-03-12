<?php

/**
 * 逻辑区：退款相关
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package logic
 * @name refund.logic.php
 * @version 1.0
 */

class RefundLogic
{
	public function GetList($process = 0, $uid = 0, $orderid = 0)
	{
										if ($process > 0) {
			$sql_where .= ' process = '.$process;
		}else{
			$sql_where = '1';
		}
						
		$sql = 'SELECT * FROM '.table('refund').' WHERE ' .$sql_where. ' ORDER by `dateline` desc';
		$sql = page_moyo($sql);
		$result = dbc(DBCMax)->query($sql)->done();
		return $result;
	}

	public function GetOne( $orderid = 0)
	{
		return dbc(DBCMax)->select('refund')->where(array('orderid'=>$orderid))->limit(1)->done();
	}
	
	public function save($orderid, $money, $reason ,$uid=0, $cash_type = '', $cash_data = array())
	{
		$uid = $uid ? $uid : MEMBER_ID;				$order = logic('order')->GetOne($orderid, $uid);
		if (empty($order) || $order['pay'] == 0) {
			return -2;
		}
				if ($this->GetOne($orderid)){
			return -1;
		}
				if($order['product']['type'] == 'ticket'){
			$coupons = logic('coupon')->SrcList($uid, $order_id);
			if(count($coupons) === 0){				return -3;
			}
			if($order['productnum'] != count($coupons) && $coupons[0]['mutis'] == 1){
				$order['totalprice'] = count($coupons)*$order['productprice'];
			}
		}
		$money = max(0,min($money,$order['totalprice']));
		$cash_type = (in_array($cash_type, array('alipay')) && $cash_data) ? $cash_type : '';
		return dbc(DBCMax)->insert('refund')->data(array('uid'=>$uid,'orderid'=>$orderid,'demand_money'=>$money,'demand_reason'=>$reason,'dateline'=>time(),'process'=>1, 'cash_type'=>$cash_type, 'cash_data'=>serialize($cash_data)))->done();
	}

	
	public function agree($orderid, $uid, $money, $reason='')
	{
				$ret = $this->check($orderid);
		if($ret) {
			return $ret;
		}

				dbc(DBCMax)->update('order')->data(array('comment' => '0'))->where(array('orderid' => $orderid,'comment' => '1'))->done();
		return dbc(DBCMax)->update('refund')
		->data(array('process'=>2,'op_uid'=>MEMBER_ID,'op_money'=>$money,'op_reason'=>$reason,'op_dateline'=>time()))
		->where(array('uid'=>$uid,'orderid'=>$orderid))
		->done();
	}

	
	public function refuse($orderid, $uid, $reason)
	{
				$ret = $this->check($orderid);
		if($ret) {
			return $ret;
		}

		return dbc(DBCMax)->update('refund')
		->data(array('process'=>3,'op_uid'=>MEMBER_ID,'op_money'=>0,'op_reason'=>$reason,'op_dateline'=>time()))
		->where(array('uid'=>$uid,'orderid'=>$orderid))
		->done();
	}

		public function getuid($appcode, $token)
	{
		$session = dbc(DBCMax)->select('api_session')->where(array('appcode' => $appcode, 'token' => $token))->limit(1)->done();
		return $session ? $session['user_id'] : 0;
	}

	
	public function check($orderid) {
		$info = $this->GetOne($orderid);
				if(empty($info)) {
			return -1;
		}
				if(in_array($info['process'], array(2, 3))) {
			return -$info['process'];
		}
		$order = logic('order')->GetOne($orderid);
				if(empty($order)) {
			return -4;
		}
				if(ORD_PAID_Yes != $order['pay'] || false == in_array($order['status'], array(ORD_STA_Normal, ORD_STA_Failed))) {
			return -5;
		}
				if(ORD_STA_Refund == $order['status']) {
			return -6;
		}
		return 0;
	}

	public function cash($orderid) {
		$refund = $this->GetOne($orderid);		
		if($refund && in_array($refund['process'], array(2)) && $refund['cash_type'] && $refund['cash_data']) {
			$cash_data = unserialize($refund['cash_data']);
			if($cash_data && $cash_data['alipaynumber'] && $cash_data['aliusername']) {
				$cash = logic('cash')->GetFree($refund['uid'], array(
					'money' => $refund['op_money'],
					'paytype' => $refund['cash_type'],
					'alipay' => $cash_data['alipaynumber'],
					'bankname' => '',
					'bankcard' => '',
					'bankusername' => $cash_data['aliusername'],
				));
				logic('cash')->MakeSuccessed($cash['orderid']);
				return $cash;
			}
		}
		return false;
	}

	public function can_cash($orderid) {
		$order = logic('order')->GetOne($orderid);
		if($order) {
						$pid = $order['paytype'];
			$payment = logic('pay')->GetOne($pid);
			if($payment && in_array($payment['code'], array('alipay', 'alipaymobile'))) {
				return true;
			}
		}
		return false;
	}

}
?>