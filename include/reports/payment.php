<?php

/**
 * 逻辑区：数据源：支付接口
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package reports
 * @name payment.php
 * @version 1.0
 */

class reports_unit_payment
{
	
	private $channels = array(
		'moneys' => array(
			'name' => '资金流入',
			'table' => 'order',
			'in' => 'sum(paymoney) as RDATA',
			'dateliner' => 'paytime',
			'where' => array(
				'pay' => ORD_PAID_Yes,
				'status' => ORD_STA_Normal
			)
		)
	);
	
	public function get_hoster_name($hoster)
	{
		if ($hoster)
		{
			$pro = logic('pay')->SrcOne($hoster);
			if ($pro)
			{
				return $pro['name'];
			}
			else
			{
				return '已删除支付网关';
			}
		}
		else
		{
			return '所有支付网关';
		}
	}
	
	public function get_channel_name($channel)
	{
		return isset($this->channels[$channel]['name']) ? $this->channels[$channel]['name'] : '未知频道';
	}
	
	public function get_datelines($hoster = null)
	{
		return logic('reports')->build_datelines($this->get_first_order_time($hoster));
	}
	
	public function generate($dateline, $hoster = null)
	{
		return logic('reports')->build_reports('payment', $this->channels, $dateline, 'paytype', is_null($hoster) ? $this->get_payments() : array($hoster), array($this, 'fix_data'));
	}
	
	public function fix_data($dateline, $payment_id, &$data)
	{
				$moneys_data = dbc(DBCMax)
		->select('recharge_order')
		->in('sum(money) as RDATA')
		->where(array('payment' => $payment_id))
		->where('paytime >= '.$dateline.' and paytime < '.($dateline + 86400))
		->where(array('status' => RECHARGE_STA_Normal))
		->limit(1)
		->done();

		if ($moneys_data)
		{
			$mdata = (float)$moneys_data['RDATA'];
			if ($mdata > 0)
			{
				$data += $mdata;
			}
		}
	}
	
	private function get_payments()
	{
		$list = dbc(DBCMax)->select('payment')->in('id')->where(array('enabled' => 'true'))->done();
		if ($list)
		{
			$pids = array();
			foreach ($list as $kv)
			{
				$pids[] = $kv['id'];
			}
			return $pids;
		}
		else
		{
			return false;
		}
	}
	
	private function get_first_order_time($hoster)
	{
		$order = dbc(DBCMax)->select('order')->where(is_null($hoster) ? array() : array('paytype' => $hoster))->order('paytime.asc')->limit(1)->done();
		if ($order)
		{
			return $order['paytime'];
		}
		else
		{
			return time();
		}
	}
}

?>