<?php

/**
 * 模块：订单管理
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package module
 * @name order.mod.php
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
		$this->CheckAdminPrivs('ordermanage');
		header('Location: ?mod=order&code=vlist');
	}
	function vList()
	{
		$this->CheckAdminPrivs('ordermanage');
		if(isset($_GET['ordsta'])){
			$ordSTA = get('ordsta', 'int');
		}else{
			$ordSTA = ORD_STA_ANY;
		}
		$ordPROC = get('ordproc', 'string');
		if ($ordPROC == '__PAY_YET__') {
			$ordPROC = 'pay > 0 and paytime > 0';
		}elseif($ordPROC == 'WAIT_BUYER_PAY'){
			$ordPROC = 'pay = 0 and paytime = 0';
		}else{
			$ordPROC = $ordPROC ? ('process="'.$ordPROC.'"') : '1';
		}
		if(MEMBER_ROLE_TYPE == 'seller'){
			$pids = logic('product')->GetUserSellerProduct(MEMBER_ID);
			$asql = 0;
			if($pids){
				$asql = implode(',',$pids);
			}
			$ordPROC .=  ' AND productid IN('.$asql.')';
		}
		$list = logic('order')->GetList(0, $ordSTA, ORD_PAID_ANY, $ordPROC);
		$batchURL = str_replace('code=vlist', 'code=batch', page_moyo_request_uri());
		include handler('template')->file('@admin/order_list');
	}
	function Process()
	{
		$this->CheckAdminPrivs('ordermanage');
		$referrer = get('referrer', 'txt');
		$id = get('id', 'number');
		$order = logic('order')->GetOne($id);
		if (!$order)
		{
			$this->Messager(__('找不到相关订单！'), '?mod=order');
		}
		if(MEMBER_ROLE_TYPE == 'seller'){
			$pids = logic('product')->GetUserSellerProduct(MEMBER_ID);
			if(!in_array($order['productid'],$pids)){
				$this->Messager(__('您不可操作该订单！'), '?mod=order');
			}
		}
		$user = user($order['userid'])->get();
		$payment = logic('pay')->SrcOne($order['paytype']);
		$paylog = logic('pay')->GetLog($order['orderid'], $order['userid']);
		$coupons = logic('coupon')->SrcList($order['userid'], $order['orderid'], TICK_STA_ANY);
		$express = logic('express')->SrcOne($order['expresstype']);
		$address = logic('address')->GetOne($order['addressid']);
		$clog = logic('order')->clog($order['orderid'])->vlist();
		include handler('template')->file('@admin/order_process');
	}
	function Batch()
	{
		$this->CheckAdminPrivs('ordermanage');
		$searchWhere = get('ssrc') ? ini('isearcher.map.'.get('ssrc').'.name') : '任意';
		$searchValue = get('sstr') ? get('sstr') : '任意';
		$ordSTA = get('ordsta', 'number');
		is_numeric($ordSTA) || $ordSTA = ORD_STA_ANY;
		$searchSTA = logic('order')->STA_Name($ordSTA);
		$ordPROC = get('ordproc', 'string');
		$ordSPROC = $ordPROC ? $ordPROC : '*';
		$ordPROC = $ordPROC ? ('process="'.$ordPROC.'"') : '1';
		$searchPROC = logic('order')->PROC_Name($ordSPROC);
				$_GET[EXPORT_GENEALL_FLAG] = EXPORT_GENEALL_VALUE;
		$_GET['code'] = 'vlist';
				if(MEMBER_ROLE_TYPE == 'seller'){
			$pids = logic('product')->GetUserSellerProduct(MEMBER_ID);
			$asql = 0;
			if($pids){
				$asql = implode(',',$pids);
			}
			$ordPROC .=  ' AND productid IN('.$asql.')';
		}
		$list = logic('order')->GetList(0, $ordSTA, ORD_PAID_ANY, $ordPROC);
		$allCount = $list ? count($list) : 0;
				$_GET['code'] = 'batch';
				$ccURL = str_replace('code=batch', 'code=batch&op=done', page_moyo_request_uri());
		include handler('template')->file('@admin/order_process_batch');
	}
	function Batch_done()
	{
		$this->CheckAdminPrivs('ordermanage','ajax');
		$action = get('action');
		in_array($action, array('refund', 'confirm', 'cancel', 'afservice', 'ends', 'delete')) || exit('false');
		$ordSTA = get('ordsta', 'number');
		is_numeric($ordSTA) || $ordSTA = ORD_STA_ANY;
		$ordPROC = get('ordproc', 'string');
		$ordPROC = $ordPROC ? ('process="'.$ordPROC.'"') : '1';
				$_GET[EXPORT_GENEALL_FLAG] = EXPORT_GENEALL_VALUE;
		$_GET['code'] = 'vlist';
				if(MEMBER_ROLE_TYPE == 'seller'){
			$pids = logic('product')->GetUserSellerProduct(MEMBER_ID);
			$asql = 0;
			if($pids){
				$asql = implode(',',$pids);
			}
			$ordPROC .=  ' AND productid IN('.$asql.')';
		}
		$list = logic('order')->GetList(0, $ordSTA, ORD_PAID_ANY, $ordPROC);
		if ($list)
		{
			foreach ($list as $i => $one)
			{
				$_GET['oid'] = $one['orderid'];
				$this->$action(false);
			}
		}
		exit('ok');
	}
	function Remark()
	{
		$this->CheckAdminPrivs('ordermanage','ajax');
		$id = get('oid', 'number');
		$text = get('text', 'txt');
		if($this->doforbidden($id)){
			exit('forbidden');
		}
		logic('order')->Update($id, array('remark'=>$text));
		exit('ok');
	}
	function Extmsg_reply()
	{
		$this->CheckAdminPrivs('ordermanage','ajax');
		$id = get('oid', 'number');
		$text = get('text', 'txt');
		if($this->doforbidden($id)){
			exit('forbidden');
		}
		logic('order')->Update($id, array('extmsg_reply'=>$text));
		exit('ok');
	}
	function Refund($exit = true)
	{
		$this->CheckAdminPrivs('ordermanage','ajax');
		$id = get('oid', 'number');
		$remark = '[退款] '.get('mark', 'txt');
		$rfm = get('refundMoney', 'float');
		$rfm = round($rfm, 2);
		if($this->doforbidden($id)){
			exit('forbidden');
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
		if($id && $order) {
			if(ORD_STA_Refund == $order['status']) {
				exit('该订单已经退款成功，请勿重复操作');
			}
			if($order['product']['type'] == 'ticket') {
				$coupo = logic('coupon')->SrcList($order['userid'], $id);
				if($order['productnum'] != count($coupo) && $coupo[0]['mutis'] == 1){
					$order['totalprice'] = round(count($coupo)*$order['productprice'], 2);
				}
			}
			if($rfm > $order['totalprice']) {
				exit('退款金额不能大于'.$order['totalprice']);
			}

			logic('order')->clog($id)->add('refund', $remark);
			logic('order')->Refund($id, $rfm);
		}
		$exit && exit('ok');
	}
	
	#确认付款动作
	function Confirm($exit = true)
	{
		$this->CheckAdminPrivs('ordermanage','ajax');
		$id = get('oid', 'number');
		if($this->doforbidden($id)){
			exit('forbidden');
		}
		$r = logic('order')->Confirm($id);
		$remark = '[确认付款] '.get('mark', 'txt').$r;
		logic('order')->clog($id)->add('confirm', $remark);
		$exit && exit('ok');
	}
	function Cancel($exit = true)
	{
		$this->CheckAdminPrivs('ordermanage','ajax');
		$id = get('oid', 'number');
		$remark = '[取消订单] '.get('mark', 'txt');
		$rfm = get('refundMoney', 'float');
		if($this->doforbidden($id)){
			exit('forbidden');
		}
		if (is_numeric($rfm))
		{
			$remark .= '；退款金额：'.$rfm;
		}
		else
		{
			$rfm = null;
		}
		logic('order')->clog($id)->add('cancel', $remark);
		logic('order')->Cancel($id, $rfm);
		$exit && exit('ok');
	}
	
	#售后服务动作
	function AfService($exit = true)
	{
		$this->CheckAdminPrivs('ordermanage','ajax');
		$id = get('oid', 'number');
		$mark = get('mark', 'txt');
		$remark = '[售后] '.$mark;
		if($this->doforbidden($id)){
			exit('forbidden');
		}
		logic('order')->clog($id)->add('afservice', $remark);
		$order = logic('order')->SrcOne($id);
		logic('notify')->Call($order['userid'], 'admin.mod.order.AfService', array('orderid'=>$id,'remark'=>$mark));
		$exit && exit('ok');
	}
	function Ends($exit = true)
	{
		$this->CheckAdminPrivs('ordermanage','ajax');
		$id = get('oid', 'number');
		$mark = get('mark', 'txt');
		$remark = '[结单] '.$mark;
		$order = logic('order')->GetOne($id);
		if(false == $order) {
						exit('error');
		}
		if($this->doforbidden($id)){
			exit('forbidden');
		}
		logic('order')->clog($id)->add('ends', $remark, null, 'TRADE_FINISHED');
		logic('order')->Update($id, array('process'=>'TRADE_FINISHED'));
		if('stuff' == $order['product']['type']) {
	        	        logic('rebate')->Add_Rebate_For_Item($order);
	        	    }
		$exit && exit('ok');
	}
	function Delete($exit = true)
	{
		$this->CheckAdminPrivs('orderdelete','ajax');
		$id = get('oid', 'number');
		if($this->doforbidden($id)){
			exit('forbidden');
		}
		logic('order')->Delete($id);
		$exit && exit('ok');
	}
	function Reset($exit = true)
	{
		$this->CheckAdminPrivs('ordermanage','ajax');
		$id = get('oid', 'number');
		$mark = get('mark', 'txt');
		$remark = '[重启订单] '.$mark;
		if($this->doforbidden($id)){
			exit('forbidden');
		}
		logic('order')->clog($id)->add('reset', $remark);
		logic('order')->Update($id, array('status'=>ORD_STA_Normal));
		$exit && exit('ok');
	}
	private function doforbidden($orderid){
		$return = false;
		if(MEMBER_ROLE_TYPE == 'seller'){
			$oinfo = dbc(DBCMax)->query('select productid from '.table('order')." where orderid='".$orderid."'")->limit(1)->done();
			$pids = logic('product')->GetUserSellerProduct(MEMBER_ID);
			if(!in_array($oinfo['productid'],$pids)){
				$return = true;
			}
		}
		return $return;
	}
}
?>