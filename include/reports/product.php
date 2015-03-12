<?php

/**
 * 逻辑区：数据源：产品报表
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package reports
 * @name product.php
 * @version 1.0
 */

class reports_unit_product
{
	
	private $channels = array(
		'buyers' => array(
			'name' => '购买人数',
			'table' => 'order',
			'in' => 'count(distinct userid) as RDATA',
			'dateliner' => 'paytime',
			'where' => array(
				'pay' => ORD_PAID_Yes,
				'status' => ORD_STA_Normal
			)
		),
		'sells' => array(
			'name' => '售出份数',
			'table' => 'order',
			'in' => 'sum(productnum) as RDATA',
			'dateliner' => 'paytime',
			'where' => array(
				'pay' => ORD_PAID_Yes,
				'status' => ORD_STA_Normal
			)
		),
		'assets' => array(
			'name' => '订单总额',
			'table' => 'order',
			'in' => 'sum(totalprice) as RDATA',
			'dateliner' => 'buytime',
			'where' => array(
				'status' => ORD_STA_Normal
			)
		),
		'moneys' => array(
			'name' => '支付总额',
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
			$pro = logic('product')->SrcOne($hoster);
			if ($pro)
			{
				return $pro['flag'];
			}
			else
			{
				return '已删除产品';
			}
		}
		else
		{
			return '所有产品';
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
		return logic('reports')->build_reports('product', $this->channels, $dateline, 'productid', is_null($hoster) ? $this->get_pids($dateline) : array($hoster));
	}
	
	private function get_pids($dateline)
	{
		$list = dbc(DBCMax)->select('order')->in('distinct productid as PID')->where('buytime >= '.$dateline.' and dateline < '.$dateline + 86400)->done();
		if ($list)
		{
			$pids = array();
			foreach ($list as $kv)
			{
				$pids[] = $kv['PID'];
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
		$order = dbc(DBCMax)->select('order')->where(is_null($hoster) ? array() : array('productid' => $hoster))->order('buytime.asc')->limit(1)->done();
		if ($order)
		{
			return $order['buytime'];
		}
		else
		{
			return time();
		}
	}
}

?>