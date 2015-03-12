<?php

/**
 * 逻辑区：代金券 二次开发
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package logic
 * @name card.logic.php
 * @version 1.0
 */

class CardLogic
{


	function html( $data )
	{
		switch (mocod())
		{
			case 'buy.order':
				
				handler('template')->load('@html/card_selector');
				break;
/*			case 'buy.order':
				if ( $data['product']['type'] != 'stuff' ) return;
				$addressID = $data['addressid'];
				include handler('template')->file('@html/address_displayer');
				break;*/
		}
	}
	
	function GetList( $uid = 0 )
	{
		$sql_limit_user = '1';
		$now = time();
		if ( $uid > 0 )
		{
			$sql_limit_user = 'user_id = ' . $uid .' AND begin_time < '.$now.' AND end_time > '.$now. ' AND consume = "N"';
		}
		elseif ($uid == -1)
		{
			$sql_limit_user = 'user_id = 0 AND begin_time < '.$now.' AND end_time > '.$now. ' AND consume = "N"';
		}
		$sql = 'SELECT * FROM ' . table('daijinquan') . ' WHERE ' . $sql_limit_user . ' ORDER BY id';
		return dbc(DBCMax)->query($sql)->done();
	}
	
	//检查代金券状态
	function BuysCheck( $card_ids, $uid = 0, &$data )
	{
		
		if ( !is_array($card_ids) ) return;
		$uid = $uid ? $uid : user()->get('id');
		$price = 0;
	    foreach ( $card_ids as $i => $card_id )
		{
			$card_id = (int) $card_id;
			if (!$card_id){
				$data['cardprice'] = 0;
				return array('false' => __('请选择你要使用的代金券！'));
			}
			$now = time();
			$sql = 'SELECT * FROM ' . table('daijinquan') . ' WHERE id = ' . $card_id 
				.' AND user_id = ' . $uid .' AND begin_time < '.$now.' AND end_time > '.$now. ' AND consume = "N"';
			$card = dbc(DBCMax)->query($sql)->limit(1)->done();
			if (!$card['id']){
				$data['cardprice'] = 0;
				return array('false' => __('没有找到相应的代金券！'));
			}
			$price += $card['credit'];
			fb($sql);
		}
		fb($price);
		$data['cardprice'] = $price;
	}
	//更新虚拟订单中totalprice 金额为减去代金券的金额
	function update_order_cardprice($virtual_orderid) {
		if(false == logic('order')->is_virtual_order($virtual_orderid)) {
			return false;
		}
		$vorder = logic('order')->SrcOne($virtual_orderid);
		if(false == $vorder) {
			return false;
		}
		$card_extmsg = $vorder['card_extmsg'];
		$totalprice = $vorder['cardprice'];
		$orders = logic('order')->getAllOrdersByOrderid($virtual_orderid);//获取虚拟订单id下的所有订单 
		if(is_array($orders) && count($orders) > 1) {
			$count = count($orders);
			foreach($orders as $order) {
				if($order['cardprice'] != $vorder['cardprice'] || $vorder['card_extmsg'] != $order['card_extmsg']) {
					return false;
				}
				$data['cardprice'] = round($totalprice/$count,2);
				logic('order')->Update($order['orderid'], $data);
			}
		}
		return $totalprice;
	}
	//获取代金券的总额
	function GetcardPrice($card_extmsg) {
		$card_ids = unserialize($card_extmsg);
		if ( !is_array($card_ids) ) return;
		$uid = $uid ? $uid : user()->get('id');
		$price = 0;
		foreach ( $card_ids as $i => $card_id )
		{
			$card_id = (int) $card_id;
			if (!$card_id){
				return ;
			}
			$now = time();
			$sql = 'SELECT * FROM ' . table('daijinquan') . ' WHERE id = ' . $card_id 
				.' AND user_id = ' . $uid .' AND begin_time < '.$now.' AND end_time > '.$now. ' AND consume = "N"';
			$card = dbc(DBCMax)->query($sql)->limit(1)->done();
			if (!$card['id']){
				return;
			}
			$price += $card['credit'];
		}
		return $price;
	}
	//更新代金券为已使用
	public function Processed($orderid,$process)
	{
	    $order = logic('order')->SrcOne($orderid);
	    $cardids = @unserialize($order['card_extmsg']);
	    if($cardids === false){
	        return;
	    }else{
	    	$card['user_id'] = $order['userid'];
	    	$card['seller_id'] = $order['productid'];
	    	$card['order_id'] = $order['orderid'];
	    	$card['consume'] = $process;
		    foreach($cardids as $cardid)
		    {
		        $this->Update($cardid, $card);
		    }
	    } 
	    
	}
	
	public function Update($id, $array)
	{
		if(isset($array['consume'])) {
			$array['consume_time'] = time();
		}
		dbc()->SetTable(table('daijinquan'));
		dbc()->Update($array, 'id = '.$id);
	}
	
	private function _Processed($cardid, $process)
	{	
		$this->Update($cardid, array('consume' => $process));
	}
	
}

?>