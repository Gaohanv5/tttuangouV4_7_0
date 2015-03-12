<?php

/**
 * 逻辑区：商家结算相关
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package logic
 * @name fund.logic.php
 * @version 1.2
 */

class FundLogic
{
	 
	 public function GetOne($id)
	 {
		$sql = '
		SELECT
			*
		FROM
			' . table('fund_order') .'
		WHERE
			orderid = ' . $id;
		$order = dbc()->Query($sql)->GetRow();
		return $order;
	 }
	 
	 public function GetList($where = '1')
	 {
	 	$sql = dbc(DBCMax)->select('fund_order')->where($where)->order('createtime.desc')->sql();
	 	logic('isearcher')->Linker($sql);
	 	$sql = page_moyo($sql);
	 	return dbc(DBCMax)->query($sql)->done();
	 }
	 
	 public function GetFree($uid,$data)
	 {
		$array = array(
			'orderid' => $this->__GetFreeID(),
			'userid' => $uid,
			'sellerid' => $data['sellerid'],
			'money' => $data['money'],
			'createtime' => time(),
			'status' => 'no',
			'paytype' => $data['paytype'],
			'alipay' => $data['alipay'],
			'bankname' => $data['bankname'],
			'bankcard' => $data['bankcard'],
			'bankusername' => $data['bankusername'],
			'from' => $data['from'] ? $data['from'] : 'ruser'
		);
		dbc(DBCMax)->insert('fund_order')->data($array)->done();
		$moneys = doubleval($array['money']);
		$sql ='UPDATE ' . table('seller').' SET account_money = account_money - ' . $moneys . ',forbid_money = forbid_money + ' . $moneys . ' WHERE userid = ' . $uid;
		$query = dbc(DBCMax)->query($sql)->done();
		return $array['orderid'];
	 }
	 
	public function Where($sql_limit)
	{
		$sql = '
		SELECT
			*
		FROM
			'.table('fund_order').'
		WHERE
			'.$sql_limit.'
		';
		return dbc()->Query($sql)->GetAll();
	}
	
	private function __GetFreeID()
	{
		$id = (date('Y', time())+2000) . date('mdHis', time()). str_pad(rand('1', '99999'), 5, '0', STR_PAD_LEFT);
		$sql = '
		SELECT
			*
		FROM
			' . table('fund_order') . '
		WHERE
			orderid = ' . $id;
		$order = dbc()->Query($sql)->GetRow();
		if ( empty($order) )
		{
			return $id;
		}
		else
		{
			return $this->__GetFreeID();
		}
	}
	
	public function del($id=0)
	{
		$order = $this->GetOne($id);
		if (!$order || $order['paytime'] > 0)
		{
			return ;
		}
		$moneys = doubleval($order['money']);
		$sql ='UPDATE ' . table('seller').' SET account_money = account_money + ' . $moneys . ',forbid_money = forbid_money - ' . $moneys . ' WHERE userid = ' . $order['userid'];
		$query = dbc(DBCMax)->query($sql)->done();
		return dbc(DBCMax)->delete('fund_order')->where('orderid='.$id.' AND paytime=0')->done();
	}
	
	public function MakeSuccessed($orderid)
	{
		$order = $this->GetOne($orderid);
		if (!$order || $order['paytime'] > 0)
		{
			return ;
		}
				$return = dbc(DBCMax)->update('fund_order')->data(array('paytime'=>time(),'status'=>'doing'))->where('orderid='.$orderid)->done();
		if($return){
			$this->Writelog($orderid);
		}
		return $return;
	}
	public function Getlog($orderid)
	{
		$sql = dbc(DBCMax)->select('fund_order_log')->where('orderid = '.$orderid)->order('id.asc')->sql();
	 	$sql = page_moyo($sql);
	 	return dbc(DBCMax)->query($sql)->done();
	}
	public function Writelog($orderid,$status='doing',$info='')
	{
		$status = in_array($status,array('doing','yes','error')) ? $status : 'doing';
		$status_str = array('doing'=>'开始受理','yes'=>'确认结算','error'=>'拒绝结算');
		$info = $info ? $info : '管理员后台操作';
		$array = array(
			'orderid' => $orderid,
			'userid' => MEMBER_ID,
			'username' => MEMBER_NAME,
			'createtime' => time(),
			'status' => $status_str[$status],
			'info' => $info
		);
		dbc()->SetTable(table('fund_order_log'));
		dbc()->Insert($array);
	}
	public function Orderdone($orderid,$status,$info)
	{
				$order = $this->GetOne($orderid);
		if (!$order || $order['status'] != 'doing')
		{
			$return = false;
		}else{
			$status = in_array($status,array('yes','error')) ? $status : 'error';
			$moneys = doubleval($order['money']);
			$return = dbc(DBCMax)->update('fund_order')->data(array('paytime'=>time(),'status'=>$status))->where('orderid='.$orderid)->done();
			if($status == 'error'){
				$sql ='UPDATE ' . table('seller').' SET account_money = account_money + ' . $moneys . ',forbid_money = forbid_money - ' . $moneys . ' WHERE userid = ' . $order['userid'];
			}else{
				$sql ='UPDATE ' . table('seller').' SET forbid_money = forbid_money - ' . $moneys . ' WHERE userid = ' . $order['userid'];
			}
			$return = dbc(DBCMax)->query($sql)->done();
			$this->Writelog($orderid,$status,$info);
		}
		return $return;
	}
}
?>