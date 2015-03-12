<?php

/**
 * 模块：商家结算管理
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package module
 * @name fund.mod.php
 * @version 1.0
 */

class ModuleObject extends MasterObject
{
    public function ModuleObject( $config )
    {
        $this->MasterObject($config);
        $runCode = Load::moduleCode($this);
        $this->$runCode();
    }

	function main()
	{
		$this->CheckAdminPrivs('fundorder');
		$sid = get('sid', 'int');
		$seller_info = logic('seller')->GetOne($sid);
		if(!$seller_info){
			$this->Messager('错误的操作，系统不支持！', -1);
		}
		$bank = ini('bank');
		$upcfg = ini('recharge');
		$payaddress = $upcfg['payaddress'] ? $upcfg['payaddress'] : '请电话联系商家确认后再进行操作，否则钱财两空';
		$max_money = $seller_info['account_money'] ? $seller_info['account_money'] : 0;
		include handler('template')->file('@admin/fund');
	}

	function order_done()
	{
		$sid = post('sid','int');
		$seller_info = logic('seller')->GetOne($sid);
		if(!$seller_info){
			$this->Messager('错误的操作，系统不支持！', -1);
		}
		$bank = ini('bank');
		$money = round((float)post('money'), 2);
		if (!$money || $money <= 0)
		{
			$this->Messager('结算金额无效,必须是一个有效数字！', -1);
		}
		$account_money = $seller_info['account_money'];
		if ($money > $account_money)
		{
			$this->Messager('结算金额过大，您的帐户最大可结算金额为'.$account_money.'元！', -1);
		}
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
			'sellerid' => $seller_info['id'],
			'from' => 'admin'
		);
		$orderid = logic('fund')->GetFree($seller_info['userid'],$data);
		if($orderid){
			logic('fund')->MakeSuccessed($orderid);
			$return = logic('fund')->Orderdone($orderid,'yes','');
		}
		if($orderid && $return){
			$this->Messager('操作成功！');
		}else{
			$this->Messager('操作失败！');
		}
	}

    function order()
    {
        $this->CheckAdminPrivs('fundorder');
		$orderid = get('orderid', 'number');
		if($orderid)
		{
			$order = logic('fund')->GetOne($orderid);
			if($order){
				if($order['status'] =='doing'){
					$action = "?mod=fund&code=order&op=save";
				}
				$order_log = logic('fund')->Getlog($orderid);
			}else{
				$this->Messager('操作错误！');
			}
		}
		else
		{
			$paystatus = get('paystatus');
			if (in_array($paystatus,array('no','yes','doing','error')))
			{
				$where = "status = '{$paystatus}'";
			}
			else
			{
				$where = '1';
			}
			$list = logic('fund')->GetList($where);
		}
        include handler('template')->file('@admin/fund_order');
    }
	public function order_save(){
		$this->CheckAdminPrivs('fundorder');
		$orderid = post('orderid', 'number');
		$status = post('status', 'txt');
		$info = strip_tags(post('info', 'txt'));
		if(!in_array($status,array('yes','error'))){
			$this->Messager('操作失败，请选择一个操作结果！');
		}
		$return = logic('fund')->Orderdone($orderid,$status,$info);
		if($return){
			$this->Messager('操作成功！');
		}else{
			$this->Messager('操作失败！');
		}
	}
	
    public function order_confirm()
    {
        $this->CheckAdminPrivs('fundorder','ajax');
		$orderid = get('orderid', 'number');
        if ($orderid)
        {
            $r = logic('fund')->MakeSuccessed($orderid);
            if(false === X_IS_AJAX) {
            	$this->Messager(null, 'admin.php?mod=fund&code=order&orderid=' . $orderid);
            }
			if($r){exit('ok');}else{exit($r);}
        }
        else
        {
            exit('结算记录流水号不正确');
        }
    }
	function Config()
    {
        $this->CheckAdminPrivs('fundset');
		$upcfg = ini('fund');
        include handler('template')->file('@admin/fund_config');
    }
    function Config_save()
    {
        $this->CheckAdminPrivs('fundset');
		$least = post('least', 'int');
		$per = post('per', 'int');
		$least = intval(max(0,$least));
		$per = intval(max(0,$per));
		$least = $least < $per ? $per : $least;
		$upcfg = array(
			'least' => $least,
            'per' => $per
        );
        ini('fund', $upcfg);
        $this->Messager('保存成功！');
    }
	function money_save()
	{
		$this->CheckAdminPrivs('fundorder','ajax');
		$id = get('id','int');
		$money = get('money','float');
		$moneyz = get('moneyz','float');
		if(!is_numeric($_GET['moneyz']) || $moneyz < 0 || !is_numeric($_GET['money']) || $money < 0){
			exit('金额输入错误，修改失败！');
		}
		$data = array('account_money' => $money,'total_money' => $moneyz);
		dbc()->SetTable(table('seller'));
		$r = dbc()->Update($data,'id='.intval($id));
		exit($r ? (string)$r : '修改失败！');
	}
	function iphonesave()
    {
		$this->CheckAdminPrivs('appmanage');
		$url = post('url', 'txt');
		$from = post('from', 'txt');
		$from = $from == 'app' ? '?mod=app' : '?mod=api&code=release';
		if($url && false === strpos($url,'https://itunes.apple.com/cn/app/')){
			$this->Messager('下载地址填写错误！',$from);
		}
		$cfg = array(
			'url' => $url
        );
        ini('iphone', $cfg);
        $this->Messager('保存成功！',$from);
    }
	function moneyupdate()
	{
		$this->CheckAdminPrivs('seller');
		$sql = dbc(DBCMax)->select('seller')->in('id')->order('id.asc')->sql();
	 	$sids = dbc(DBCMax)->query($sql)->done();
		if($sids){
			foreach($sids as $sid){
				$sql = dbc(DBCMax)->select('product')->in('id')->where(array('sellerid'=>$sid['id'],'saveHandler'=>'normal'))->order('id.asc')->sql();
				$pids = dbc(DBCMax)->query($sql)->done();
				if($pids){
					$c_pid = array();
					foreach($pids as $pid){
						$c_pid[] = $pid['id'];
					}
					$sql = dbc(DBCMax)->select('order')->in('SUM(totalprice-expressprice) AS money,count(*) AS ordernum')->where('paytime > 0 AND productid IN('.implode(',',$c_pid).')')->sql();
					$orders = dbc(DBCMax)->query($sql)->done();
					$money = $orders[0]['money'];
					$successnum = $orders[0]['ordernum'];
					$productnum = count($pids);
				}else{
					$money = $successnum = $productnum = 0;
				}
				dbc(DBCMax)->update('seller')->data(array('money'=>$money,'successnum'=>$successnum,'productnum'=>$productnum))->where(array('id' => $sid['id']))->done();
			}
		}
		$this->Messager('正在更新，请稍后......','?mod=tttuangou&code=mainseller');
	}
}

?>