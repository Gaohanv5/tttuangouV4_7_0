<?php

/**
 * 逻辑区：配送管理
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package logic
 * @name delivery.logic.php
 * @version 1.0
 */

class DeliveryLogic
{
	
	public function GetList($alsend = '', $extend = '1')
	{
		$sql_order = 'o.paytime DESC';
		if ($alsend == 'WAIT_SELLER_SEND_GOODS')
		{
			$sql_order = 'o.paytime ASC';
		}
		$sql = 'SELECT o.*, p.flag
		FROM
			'.table('order').' o
		LEFT JOIN
			'.table('product').' p
		ON
			o.productid = p.id
		WHERE
			p.type="stuff"
		AND
			o.status='.ORD_STA_Normal.'
		AND
			'.$extend.'
		ORDER BY
			'.$sql_order;
		logic('isearcher')->Linker($sql);
				$sql = page_moyo($sql);
		$result = dbc(DBCMax)->query($sql)->done();
		if($result){		foreach ($result as $i => $order)
		{
			$product = array('flag'=>$result[$i]['flag']);
			unset($result[$i]['flag']);
			$result[$i]['product'] = $product;
			$result[$i]['express'] = logic('express')->SrcOne($order['expresstype']);
			$result[$i]['address'] = logic('address')->GetOne($order['addressid']);
			$result[$i]['attrs'] = logic('attrs')->snapshot($order['orderid'], $order['productid']);
		}
		}
		return $result;
	}
	
	public function Invoice($oid, $invoice)
	{
		$order = logic('order')->GetOne($oid);
		$ups = array();
		$order['invoice'] = $invoice;
		  $ups['invoice'] = $invoice;
		$order['expresstime'] || $ups['expresstime'] = time();
		logic('order')->Update($oid, $ups);
				$order['process'] == 'WAIT_SELLER_SEND_GOODS' && logic('pay')->SendGoods($order, true);
		return true;
	}
	
	public function Count($pid, $alsend)
	{
		$sql_limit_status = '1';
		if ($alsend == DELIV_SEND_Yes)
		{
			$sql_limit_status = 'process IN("WAIT_BUYER_CONFIRM_GOODS","TRADE_FINISH")';
		}
		elseif ($alsend == DELIV_SEND_No)
		{
			$sql_limit_status = 'process="WAIT_SELLER_SEND_GOODS"';
		}
		elseif ($alsend == DELIV_SEND_OK)
		{
			$sql_limit_status = 'process="TRADE_FINISHED"';
		}
		$r = dbc(DBCMax)->select('order')->in('COUNT(1) AS devCNT')->where('productid='.$pid.' AND status='.ORD_STA_Normal.' AND '.$sql_limit_status)->limit(1)->done();
		return $r['devCNT'] ? $r['devCNT'] : 0;
	}
}
?>