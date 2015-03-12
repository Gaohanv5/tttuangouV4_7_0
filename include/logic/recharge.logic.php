<?php

/**
 * 逻辑区：充值相关
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package logic
 * @name recharge.logic.php
 * @version 1.2
 */

class RechargeLogic
{
	 
	 public function GetOne($id)
	 {
	 	$id = (is_numeric($id) ? $id : 0);
		$sql = '
		SELECT
			*
		FROM
			' . table('recharge_order') .'
		WHERE
			orderid = ' . $id;
		$order = dbc()->Query($sql)->GetRow();
		return $order;
	 }
	 
	 public function GetList($where = '1')
	 {
	 	$sql = dbc(DBCMax)->select('recharge_order')->where($where)->order('createtime.desc')->sql();
	 	logic('isearcher')->Linker($sql);
	 	$sql = page_moyo($sql);
	 	return dbc(DBCMax)->query($sql)->done();
	 }
	 
	 public function GetFree($money,$type=0)
	 {
		$uid = user()->get('id');
				$order = $this->Where('money='.$money.' AND userid='.$uid.' AND status='.RECHARGE_STA_Blank);
		if ($order)
		{
			$order = $order[0];
		}
		else
		{
			$order = $this->__CreateNew($uid,$money,$type);
		}
		return $order;
	 }
	 
	public function Where($sql_limit)
	{
		$sql = '
		SELECT
			*
		FROM
			'.table('recharge_order').'
		WHERE
			'.$sql_limit.'
		';
		return dbc()->Query($sql)->GetAll();
	}
	
	private function __CreateNew($uid,$money,$type=0)
	{
		$array = array(
			'orderid' => $this->__GetFreeID(),
			'userid' => $uid,
			'ptype' => $type,
			'money' => $money,
			'createtime' => time(),
			'status' => 255
		);
		dbc()->SetTable(table('recharge_order'));
		dbc()->Insert($array);
		return $array;
	}
	
	private function __GetFreeID()
	{
		$id = (date('Y', time())+1000) . date('md', time()) . str_pad(rand('1', '99999'), 5, '0', STR_PAD_LEFT);
		$sql = '
		SELECT
			*
		FROM
			' . table('recharge_order') . '
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
	
	public function Update($id, $array)
	{
		dbc()->SetTable(table('recharge_order'));
		return dbc()->Update($array, 'orderid = '.$id);
	}
	
	public function ccOrder($orderid)
	{
		$order = $this->GetOne($orderid);
		$order['paytype'] = $order['payment'];
		$order['product']['type'] = 'ticket';
		return $order;
	}
	
	public function MakeSuccessed($orderid)
	{
		$order = $this->GetOne($orderid);
		if (!$order || $order['paytime'] > 0)
		{
			return;
		}
				dbc(DBCMax)->update('recharge_order')->data(array('paytime'=>time(),'status'=>RECHARGE_STA_Normal))->where('orderid='.$orderid)->done();
				$log = array(
			'name' => '账户充值',
			'intro' => '充值流水号：'.$orderid
		);
		logic('me')->money()->add($order['money'], $order['userid'], $log);
				$upcfg = ini('recharge');
		$payinfo = logic('pay')->GetOne($order['payment']);
		if($payinfo['code'] != 'recharge' && intval($upcfg['percentage']) > 0){
			$add_money = round((intval($order['money']) * intval($upcfg['percentage'])/100),2);
			logic('me')->money()->add($add_money, $order['userid'], array('name' => '充值返现','intro' => '充值流水号：'.$orderid));
			dbc(DBCMax)->update('recharge_order')->data(array('add_money'=>$add_money))->where('orderid='.$orderid)->done();
		}
	}
	
	public function Clean()
	{
				$timeOld = time() - 86400;
		return dbc(DBCMax)->delete('recharge_order')->where('status='.RECHARGE_STA_Blank.' AND paytime=0 AND createtime<='.$timeOld)->done();
	}
	
	public function del($id=0)
	{
		return dbc(DBCMax)->delete('recharge_order')->where('orderid='.$id.' AND paytime=0')->done();
	}
	
	public function card()
	{
		return loadInstance('logic.recharge.card', 'card_RechargeLogic');
	}
	
	public function forder()
	{
		return loadInstance('logic.recharge.forder', 'forder_RechargeLogic');
	}
}


class card_RechargeLogic
{
	public function ifo($no)
	{
		return dbc(DBCMax)->select('recharge_card')->where('number='.$no)->limit(1)->done();
	}
	public function MakeUsed($number, $password)
	{
		return dbc(DBCMax)->update('recharge_card')->data(array('usetime'=>time(),'uid'=>user()->get('id')))->where(array('number'=>$number,'password'=>$password))->done();
	}
	
	public function GetList($used = -1)
	{
		$used < 0 && $sql_used = '1';
		$used > 0 && $sql_used = 'usetime > 0';
		$used == 0 && $sql_used = 'usetime = 0';
		$sql = 'SELECT * FROM '.table('recharge_card').' WHERE '.$sql_used.' ORDER BY id DESC';
		logic('isearcher')->Linker($sql);
		$sql = page_moyo($sql);
		$query = dbc()->Query($sql);
		return $query ? $query->GetAll() : array();
	}
	public function Generate($price = 10, $nums = 1)
	{
		$price = (float)$price;
		$nums = (int)$nums;
		if ($price <= 0 || $nums <= 0) return;
		for ($i=0; $i < $nums; $i++)
		{
			dbc(DBCMax)->insert('recharge_card')->data(array(
				'number' => $this->__random_num(12),
				'password' => $this->__random_num(6),
				'price' => $price
			))->done();
		}
	}
	public function Delete($id)
	{
		return dbc(DBCMax)->delete('recharge_card')->where('id='.$id)->done();
	}
	
	public function __random_num($length = 12)
	{
		$length = (int)$length;
		$loops = ceil($length / 3);
		$string = '';
		for ( $i=0; $i<$loops; $i++ )
		{
			$string .= (string)mt_rand(100, 999);
		}
		$string = substr($string, 0, $length);
		return $string;
	}
}


class forder_RechargeLogic
{
	
	public function paid($trade, $order = array(), $money = 0.00, $dsp = true)
	{
		if (is_numeric($trade))
		{
						$rcgOrder = logic('recharge')->GetOne($trade);
			if ((int)$rcgOrder['status'] > 0 && $rcgOrder['paytime'] > 0)
			{
				return true;
			}
			else
			{
				return false;
			}
		}
				$money = (float)$money;
		if ($money <= 0)
		{
						return $this->result(true, $money);
		}
		$userID = $order['userid'];
		$rcgID = $order['orderid'];
		$rcgOrder = logic('recharge')->GetOne($rcgID);
		if ((int)$rcgOrder['status'] > 0 && $rcgOrder['paytime'] > 0)
		{
			return $this->result(false, $money);
		}
		else
		{
			if ((int)$rcgOrder['status'] == 0)
			{
				$roid = dbc(DBCMax)->insert('recharge_order')->data(array(
					'orderid' => $rcgID,
					'userid' => $userID,
					'money' => $money,
					'createtime' => time(),
					'payment' => $order['paytype'],
					'paytime' => time(),
					'status' => RECHARGE_STA_Normal
				))->done();
				if (false === $roid)
				{
					return $this->result(false, $money);
				}
				$extendMsg = '';
				if (isset($trade['money_reason']) && strlen($trade['money_reason']) > 0)
				{
					$dsp = true;
					$extendMsg = '（'.$trade['money_reason'].'）';
				}
				logic('me')->money()->add($money, $userID, $dsp ? array(
					'name' => __('账户充值'),
					'intro' => sprintf(__('订单号：%s<br/>交易单号：%s ; %s'), $rcgID, $trade['trade_no'], $extendMsg)
				) : array());
			}
			return $this->result(true, $money);
		}
		return $this->result(false, $money);
	}
	
	private function result($paid, $money)
	{
		return array(
			'paid' => $paid,
			'money' => round($money, 2)
		);
	}
}

?>