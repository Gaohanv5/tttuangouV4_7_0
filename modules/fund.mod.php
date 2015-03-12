<?php

/**
 * 模块：商家结算
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package module
 * @name fund.mod.php
 * @version 1.0
 */

class ModuleObject extends MasterObject
{
	private $uid = 0;
	private $sid = 0;
	
	private function iniz()
	{
		$this->uid = user()->get('id');
		if ($this->uid < 0)
		{
			$this->Messager('请先登录！', '?mod=account&code=login');
		}
		$this->sid = logic('seller')->U2SID($this->uid);
		if ($this->sid < 0)
		{
			$this->Messager('您无权进行该操作！', 0);
		}
	}
	function ModuleObject( $config )
	{
		$this->MasterObject($config);
		$this->iniz();
		$runCode = Load::moduleCode($this);
		$this->$runCode();
	}
	public function main()
	{
		$upcfg = ini('recharge');
		$bank = ini('bank');
		$fund_set = ini('fund');
		$seller_info = logic('seller')->GetOne(null,MEMBER_ID);
		$money = $seller_info['money'];
		$total_money = $seller_info['total_money'];
		$account_money = $seller_info['account_money'];
		$forbid_money = $seller_info['forbid_money'];
		$per_money = intval($fund_set['per']) > 0 ? intval($fund_set['per']) : 0;
		$min_money = intval($fund_set['least']) > 0 ? intval($fund_set['least']) : 0;
		if($per_money > 0){
			$max_money = floor($account_money/$per_money)*$per_money;
		}else{
			$max_money = $account_money;
		}
		$min_money = $min_money < $per_money ? $per_money : $min_money;
		$payaddress = $upcfg['payaddress'] ? $upcfg['payaddress'] : '请电话联系商家确认后再进行操作，否则钱财两空';
		include handler('template')->file('fund');
	}
	public function del()
	{
		$id = $this->__orderid();
		$order = logic('fund')->GetOne($id);
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
		logic('fund')->del($id);
		$this->Messager('取消成功！');
	}
	public function order()
	{
		$seller_info = logic('seller')->GetOne(null,MEMBER_ID);
		$money = $seller_info['money'];
		$total_money = $seller_info['total_money'];
		$account_money = $seller_info['account_money'];
		$forbid_money = $seller_info['forbid_money'];
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
		$list = logic('fund')->GetList($where);
		include handler('template')->file('fund_order');
	}
	public function money()
	{
		$seller_info = logic('seller')->GetOne(null,MEMBER_ID);
		$money = $seller_info['money'];
		$total_money = $seller_info['total_money'];
		$account_money = $seller_info['account_money'];
		$forbid_money = $seller_info['forbid_money'];
		$list = logic('rebate')->get_my_rebate_list();
		if($list){
			foreach($list as $key => $val){
				if($val['fund_money'] > 0){
					$list[$key]['salary_money'] = $val['fund_money'];
					$list[$key]['salary_pre'] = '按结算价';
				}else{
					$list[$key]['salary_money'] = number_format(($val['deal_money'] - $val['salary_money']),2);
					$list[$key]['salary_pre'] = $val['salary_pre'].'%';
				}
			}
		}
		include handler('template')->file('fund_money');
	}
	public function order_save()
	{
		$bank = ini('bank');
		$fund_set = ini('fund');
		$money = round((float)post('money'), 2);
		if (!$money || $money <= 0)
		{
			$this->Messager('结算金额无效,必须是一个有效数字！', -1);
		}
		$seller_info = logic('seller')->GetOne(null,MEMBER_ID);
		$account_money = $seller_info['account_money'];
		$per_money = intval($fund_set['per']) > 0 ? intval($fund_set['per']) : 0;
		$min_money = intval($fund_set['least']) > 0 ? intval($fund_set['least']) : 0;
		if($per_money > 0){
			$max_money = floor($account_money/$per_money)*$per_money;
		}else{
			$max_money = $account_money;
		}
		$min_money = $min_money < $per_money ? $per_money : $min_money;
		if ($money < $min_money)
		{
			$this->Messager('结算金额输入错误，结算金额必须大于或等于'.$min_money.'元！', -1);
		}
		if ($money > $max_money)
		{
			$this->Messager('结算金额过大，您的帐户最大可结算金额为'.$max_money.'元！', -1);
		}
		$money = $per_money > 0 ? floor($money/$per_money)*$per_money : $money;
		$paytype = post('paytype','txt');
		if (!in_array($paytype,array('alipay','money','bank')))
		{
			$this->Messager('您必须选择一种结算方式！', -1);
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
			'bankusername' => $bankusername,
			'sellerid' => $seller_info['id']
		);
		$order = logic('fund')->GetFree(MEMBER_ID,$data);
		$this->Messager('您的结算申请成功，请等待系统处理！', rewrite('?mod=fund&code=order'));
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