<?php

/**
 * 逻辑区：数据源：用户相关
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package reports
 * @name user.php
 * @version 1.0
 */

class reports_unit_user
{
	
	private $channels = array(
		'expenses' => array(
			'name' => '支付金额',
			'table' => 'order',
			'in' => 'sum(paymoney) as RDATA',
			'dateliner' => 'paytime',
			'where' => array(
				'pay' => ORD_PAID_Yes,
				'status' => ORD_STA_Normal
			)
		),
		'recharges' => array(
			'name' => '充值金额',
			'table' => 'recharge_order',
			'in' => 'sum(money) as RDATA',
			'dateliner' => 'paytime',
			'where' => array(
				'status' => RECHARGE_STA_Normal
			)
		)
	);
	
	public function get_hoster_name($hoster)
	{
		if ($hoster)
		{
			$pro = user($hoster)->get('name');
			if ($pro)
			{
				return $pro;
			}
			else
			{
				return '已删除用户';
			}
		}
		else
		{
			return '所有用户';
		}
	}
	
	public function get_channel_name($channel)
	{
		return isset($this->channels[$channel]['name']) ? $this->channels[$channel]['name'] : '未知频道';
	}
	
	public function get_datelines($hoster)
	{
		return logic('reports')->build_datelines($this->get_first_user_time($hoster));
	}
	
	public function generate($dateline, $hoster = null)
	{
		return logic('reports')->build_reports('user', $this->channels, $dateline, 'userid', array($hoster));
	}
	
	private function get_first_user_time($hoster)
	{
		$order = dbc(DBCMax)->select('order')->where(array('userid' => $hoster))->order('paytime.asc')->limit(1)->done();
		$recharge = dbc(DBCMax)->select('recharge_order')->where(array('userid' => $hoster))->order('paytime.asc')->limit(1)->done();
		$order || $order = array('paytime' => time() + 7052991);
		$recharge || $recharge = array('paytime' => time() + 7052991);
		if (($order['paytime'] == $recharge['paytime']) && $order['paytime'] > time())
		{
			return 0;
		}
		else
		{
			return min(array($order['paytime'], $recharge['paytime']));
		}
	}
}

?>