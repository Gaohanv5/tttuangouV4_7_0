<?php

/**
 * 模块：退款申请管理
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package module
 * @name refund.mod.php
 * @version 1.0
 */

class ModuleObject extends MasterObject
{
	function ModuleObject( $config )
	{
		$this->MasterObject($config);
		$runCode = Load::moduleCode($this);
		$this->$runCode();
	}

	function Main()
	{
		$this->CheckAdminPrivs('refund');
		$status = get('status', 'int');
		$list = logic('refund')->GetList($status);
		include handler('template')->file('@admin/refund_list');
	}

	function Process()
	{
		$this->CheckAdminPrivs('refund');
		$id = get('id', 'number');
		$order = logic('order')->GetOne($id);
		if (!$order)
		{
			$this->Messager(__('找不到相关订单！'), '?mod=order');
		}
		$user 	 = user($order['userid'])->get();
		$payment = logic('pay')->SrcOne($order['paytype']);
		$paylog  = logic('pay')->GetLog($order['orderid'], $order['userid']);
		$coupons = logic('coupon')->SrcList($order['userid'], $order['orderid'], TICK_STA_ANY);
		$express = logic('express')->SrcOne($order['expresstype']);
		$address = logic('address')->GetOne($order['addressid']);
		$refund  = logic('refund')->GetOne($order['orderid']);
		$order['ypaymoney'] = ($order['totalprice'] > $order['paymoney']) ? number_format(($order['totalprice'] - $order['paymoney']),2) : 0;
		$order['tpaymoney'] = $order['totalprice'];
		if($order['product']['type'] == 'ticket'){
			$coupo = logic('coupon')->SrcList($order['userid'], $id);
			if($order['productnum'] != count($coupo) && $coupo[0]['mutis'] == 1){
				$order['tpaymoney'] = count($coupo)*$order['productprice'];
				$order['tmsg'] = array(
					'money' => $order['paymoney'],
					'tnum' => $order['productnum'],
					'num' => $order['productnum']-count($coupo)
				);
			}
		}
		include handler('template')->file('@admin/refund_process');
	}
		function apply()
	{
		$this->CheckAdminPrivs('refund');
		$id = post('oid', 'number');
		$rfm = post('money', 'float');
		$reason = post('reason', 'string');
		$rfm = round($rfm, 2);
		if ($rfm <= 0) {
			$this->Messager('退款金额输入错误');
		}

		if (is_numeric($rfm))
		{
			$remark .= '；退款金额：'.$rfm;
		}
		else
		{
			$rfm = null;
		}
		$order = logic('order')->GetOne($id);
		if($id && $order){
			if(ORD_STA_Refund == $order['status']) {
				$this->Messager('该订单已经退款成功，请勿重复操作');
			}
			if($order['product']['type'] == 'ticket'){
				$coupo = logic('coupon')->SrcList($order['userid'], $id);
				if($order['productnum'] != count($coupo) && $coupo[0]['mutis'] == 1){
					$order['totalprice'] = round(count($coupo)*$order['productprice'], 2);
				}
			}
			if($rfm > $order['totalprice']){
				$this->Messager('退款金额不能大于'.$order['totalprice']);
			}

			$ret = logic('refund')->check($id);
			if($ret) {
				$__msgs = array(
					-1 => '该订单的退款申请记录不存在，请检查',
					-2 => '该订单已经同意退款，请勿重复操作',
					-3 => '该订单已经拒绝退款，请勿重复操作',
					-4 => '该订单已经不存在了，请检查',
					-5 => '该订单不允许退款，请检查',
					-6 => '该订单已经退款成功，请勿重复操作',
				);
				$this->Messager($__msgs[$ret]);
			}

			logic('refund')->agree($id, $order['userid'], $rfm, $reason);
			logic('order')->clog($id)->add('refund', $remark);
			logic('order')->Refund($id, $rfm);
			
			if(false !== ($cash = logic('refund')->cash($id))) {
				$this->Messager('同意原路退款成功!现在为您跳转到提现操作页面', 'admin.php?mod=cash&code=order&orderid=' . $cash['orderid']);
			} else {
				$this->Messager('同意退款成功!', 'admin.php?mod=refund');
			}
		}else{
			$this->Messager('操作错误');
		}
	}

		function refuse()
	{
		$this->CheckAdminPrivs('refund');
		$id = post('oid', 'number');
		$reason = post('reason', 'string');
		if (empty($reason)) {
			$this->Messager('请填写拒绝原因');
		}

		$order = logic('order')->GetOne($id);
		if($id && $order){
			$ret = logic('refund')->check($id);
			if($ret) {
				$__msgs = array(
					-1 => '该订单的退款申请记录不存在，请检查',
					-2 => '该订单已经同意退款，请勿重复操作',
					-3 => '该订单已经拒绝退款，请勿重复操作',
					-4 => '该订单已经不存在了，请检查',
					-5 => '该订单不允许退款，请检查',
					-6 => '该订单已经退款成功，请勿重复操作',
				);
				$this->Messager($__msgs[$ret]);
			}

			logic('refund')->refuse($id, $order['userid'], $reason);
			$this->Messager('拒绝退款成功!', 'admin.php?mod=refund');
		}else{
			$this->Messager('操作错误');
		}
	}
}


?>