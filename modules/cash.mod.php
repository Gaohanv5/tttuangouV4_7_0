<?php

/**
 * 模块：用户提现
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package module
 * @name cash.mod.php
 * @version 1.0
 */

class ModuleObject extends MasterObject
{
	function ModuleObject( $config )
	{
		$this->MasterObject($config);
		if (MEMBER_ID < 1)
		{
			$this->Messager(__('请先登录！'), '?mod=account&code=login');
		}
		$runCode = Load::moduleCode($this);
		$this->$runCode();
	}
	public function main()
	{
		$upcfg = ini('recharge');
		$bank = ini('bank');
		$maxmoney = intval(user()->get('money'));
		$payaddress = $upcfg['payaddress'] ? $upcfg['payaddress'] : '请电话联系商家确认后再进行操作，否则钱财两空';
		include handler('template')->file('cash');
	}
	public function del()
	{
		$id = $this->__orderid();
		$order = logic('cash')->GetOne($id);
		if (!$order)
		{
			$this->Messager('订单编号无效！', -1);
		}
		if ($order['userid'] != MEMBER_ID)
		{
			$this->Messager('您无权操作此订单！', -1);
		}
		if ($order['paytime'] > 0)
		{
			$this->Messager('已经处理过的订单，无法删除！', -1);
		}
		logic('cash')->del($id);
		$this->Messager('删除成功！');
	}
	public function order()
	{
		$tabcssall = $tabcssno = $tabcssyes = $tabcssdoing = '3';
		$paystatus = get('pay');
		$where = ' userid = ' . MEMBER_ID;
		if ($paystatus)
		{
			if ($paystatus == 'no')
			{
				$where .= " AND status = 'no'";
				$tabcssno = '2';
			}
			elseif($paystatus == 'yes')
			{
				$where .= " AND status = 'yes'";
				$tabcssyes = '2';
			}
			elseif($paystatus == 'doing')
			{
				$where .= " AND status = 'doing'";
				$tabcssdoing = '2';
			}
			elseif($paystatus == 'error')
			{
				$where .= " AND status = 'error'";
				$tabcsserror = '2';
			}
			else
			{
				$tabcssall = '2';
			}
		}
		else
		{
			$tabcssall = '2';
		}
		$list = logic('cash')->GetList($where);
		include handler('template')->file('cash_order');
	}
	public function order_save()
	{
		$bank = ini('bank');
		$money = round((float)post('money'), 2);
		if (!$money || $money <= 0)
		{
			$this->Messager('提现金额无效！', -1);
		}
		$maxmoney = intval(user()->get('money'));
		if ($money > $maxmoney)
		{
			$this->Messager('提现金额过大，您的帐户余额只有'.$maxmoney.'元！', -1);
		}
		$paytype = post('paytype','txt');
		if (!in_array($paytype,array('alipay','money','bank')))
		{
			$this->Messager('您必须选择一种提现方式！', -1);
		}
		$alipay = post('alipaynumber','txt');
		$bankname = post('bankname','txt');
		$bankcard = post('banknumber','number');
		$bankusername = post('bankusername','txt');
		$aliusername = post('aliusername','txt');
		if($paytype == 'alipay'){
			if(empty($alipay)){
				$this->Messager('请输入您的支付宝帐号！', -1);
			}elseif(strlen($alipay) < 6){
				$this->Messager('您的支付宝帐号填写错误！', -1);
			}elseif(empty($aliusername)){
				$this->Messager('请输入收款人姓名！', -1);
			}elseif(strlen($aliusername) < 4 || strlen($aliusername) > 48){
				$this->Messager('收款人姓名填写错误！', -1);
			}
			$bankusername = $aliusername;
		}elseif($paytype == 'bank'){
			if(empty($bankname)){
				$this->Messager('请选择一个转帐银行！', -1);
			}elseif(!in_array($bankname,array_keys($bank))){
				$this->Messager('转帐银行错误！', -1);
			}elseif(empty($bankcard)){
				$this->Messager('请输入银行卡号！', -1);
			}elseif(strlen($bankcard) < 8 || strlen($bankcard) > 19 || !is_numeric($bankcard)){
				$this->Messager('您的银行卡号填写错误！', -1);
			}elseif(empty($bankusername)){
				$this->Messager('请输入开户人姓名！', -1);
			}elseif(strlen($bankusername) < 4 || strlen($bankusername) > 48){
				$this->Messager('开户人姓名填写错误！', -1);
			}
		}
		$data = array(
			'money' => $money,
			'paytype' => $paytype,
			'alipay' => $alipay,
			'bankname' => $bank[$bankname],
			'bankcard' => $bankcard,
			'bankusername' => $bankusername
		);
		$order = logic('cash')->GetFree(MEMBER_ID,$data);
		$this->Messager('您的提现申请成功，请等待系统处理！', rewrite('?mod=cash&code=order'));
	}
	private function __orderid()
	{
		$id = get('id', 'number');
		if (!$id || strlen($id) != 19)
		{
			$this->Messager('请输入正确的订单编号！', -1);
		}
		return $id;
	}
}
?>