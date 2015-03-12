<?php

/**
 * 逻辑区：统计管理
 * @copyright (C)2014 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package logic
 * @name salecount.logic.php
 * @version 1.0
 */

class SalecountLogic
{
		public function count_product($where = '1')
	{
		$sql = "SELECT o.productid as id,p.flag as productname,s.sellername,m.username,COUNT(*) as ordercount,SUM(o.productnum) as productcount,SUM(o.totalprice) as moneycount FROM ".table('order')." o LEFT JOIN ".table('product')." p ON o.productid = p.id LEFT JOIN ".table('seller')." s ON p.sellerid = s.id LEFT JOIN ".table('members')." m ON s.userid = m.uid WHERE o.totalprice > 0 AND o.pay = 1 AND ".$where." GROUP BY o.productid ORDER BY o.productid ASC";
		logic('isearcher')->Linker($sql);
		$sql = page_moyo($sql);
		$data = dbc(DBCMax)->query($sql)->done();
		if($data){
			foreach($data as $key => $val){				if(empty($val['productname'])){
					unset($data[$key]);
				}
			}
		}
		return $data;
	}

		public function count_payment($where = '1')
	{
		$sql = "SELECT o.paytype as id,p.code as paycode,p.name as payname,SUM(o.paymoney) as paymoneys FROM ".table('order')." o LEFT JOIN ".table('payment')." p ON o.paytype = p.id WHERE o.paymoney > 0 AND o.pay = 1 AND ".$where." GROUP BY o.paytype ORDER BY o.paytype ASC";
		logic('isearcher')->Linker($sql);
		$sql = page_moyo($sql);
		$data = dbc(DBCMax)->query($sql)->done();
		if($data){
			foreach($data as $key => $val){				if(empty($val['payname'])){
					unset($data[$key]);
				}
			}
		}
		return $data;
	}

		public function count_user($where = '1')
	{
		$sql = "SELECT o.userid as id,m.username,m.money as moneyless,SUM(o.money) AS rechargemoneys FROM ".table('recharge_order')." o LEFT JOIN ".table('members')." m ON o.userid = m.uid WHERE o.status = 1 GROUP BY o.userid ORDER BY SUM(o.money) DESC,o.userid ASC";
		logic('isearcher')->Linker($sql);
		$sql = page_moyo($sql);
		$recharge_data = dbc(DBCMax)->query($sql)->done();
		$sql = "SELECT o.uid as id,m.username,m.money as moneyless,SUM(o.money*o.mutis) AS trademoneys FROM ".table('ticket')." o LEFT JOIN ".table('members')." m ON o.uid = m.uid WHERE o.status = 1 AND ".$this->query_format_sql()." GROUP BY o.uid ORDER BY SUM(o.money*o.mutis) DESC,o.uid ASC";
		$sql = page_moyo($sql);
		$trade_data = dbc(DBCMax)->query($sql)->done();
		$sql = "SELECT o.userid as id,m.username,m.money as moneyless,SUM(o.totalprice) as paymoneys FROM ".table('order')." o LEFT JOIN ".table('members')." m ON o.userid = m.uid WHERE o.totalprice > 0 AND o.pay = 1 AND ".$where." GROUP BY o.userid ORDER BY SUM(o.totalprice) DESC,o.userid ASC";
		logic('isearcher')->Linker($sql);
		$sql = page_moyo($sql);
		$order_data = dbc(DBCMax)->query($sql)->done();
		if($order_data){
			if($trade_data){
				foreach($trade_data as $key => $val){
					$f = true;
					foreach($order_data as $k => $v){
						if($val['id'] == $v['id']){
							$order_data[$k]['trademoneys'] = $val['trademoneys'];
							$f = false;
						}
					}
					if($f){
						$order_data[] = $val;
					}
				}
			}
			if($recharge_data){
				foreach($recharge_data as $key => $val){
					$f = true;
					foreach($order_data as $k => $v){
						if($val['id'] == $v['id']){
							$order_data[$k]['rechargemoneys'] = $val['rechargemoneys'];
							$f = false;
						}
					}
					if($f){
						$order_data[] = $val;
					}
				}
			}
		}else{
			$order_data = $recharge_data;
		}
		if($order_data){
			foreach($order_data as $key => $val){
				if(empty($val['username'])){					unset($order_data[$key]);
					continue;
				}
				$order_data[$key]['paymoneys'] = $val['paymoneys'] ? $val['paymoneys'] : '0.00';
				$order_data[$key]['rechargemoneys'] = $val['rechargemoneys'] ? $val['rechargemoneys'] : '0.00';
				$order_data[$key]['trademoneys'] = $val['trademoneys'] ? $val['trademoneys'] : '0.00';
			}
		}
		return $order_data;
	}

		public function count_fund($where = '1')
	{
		$sql = "SELECT o.productid as id,p.flag as productname,p.nowprice as productprice,p.fundprice,p.type,s.sellername,s.profit_pre,m.username,COUNT(*) as ordercount,SUM(o.productnum) as productcount,SUM(o.totalprice) as moneycount,SUM(o.expressprice) as expressprices,SUM(o.totalprice-o.expressprice-o.productnum*o.productprice) as arrtsmoney FROM ".table('order')." o LEFT JOIN ".table('product')." p ON o.productid = p.id LEFT JOIN ".table('seller')." s ON p.sellerid = s.id LEFT JOIN ".table('members')." m ON s.userid = m.uid WHERE o.totalprice > 0 AND o.pay = 1 AND ".$where." GROUP BY o.productid ORDER BY o.productid ASC";		
		logic('isearcher')->Linker($sql);
		$sql = page_moyo($sql);
		$fund_data = dbc(DBCMax)->query($sql)->done();
		if($fund_data){
			foreach($fund_data as $key => $val){
				if(empty($val['productname'])){					unset($fund_data[$key]);
					continue;
				}
				$fund_data[$key]['fundprice'] = $val['fundprice'] < 0 ? '--' : $val['fundprice'];
				$fund_data[$key]['profit_pre'] = $val['profit_pre'] > 0 ? round($val['profit_pre']).'%' : '--';
				$orderids = $this->get_query_order($val['id']);
				if($val['type'] == 'stuff'){
					$sq = "SELECT SUM(productnum) as wnum FROM ".table('order')." WHERE orderid IN(".implode(',',$orderids).") AND process = 'TRADE_FINISHED'";
					$dat = dbc(DBCMax)->query($sq)->limit(1)->done();
					$fund_data[$key]['ticketused'] = $dat['wnum'] ? $dat['wnum'] : '0';
					
				}elseif($val['type'] == 'ticket'){
					$where_usetime = $this->query_format_sql('t.usetime', 'fund', 0);

					

										$ticketids = array();
					$used_orderids = array();
					$fund_data[$key]['ticketused'] = 0;
					$fund_data[$key]['expendcount'] = 0;
					$sq = "SELECT `ticketid`, `orderid`, `mutis`, (`mutis` * `money`) as `mutis_money` FROM ".table('ticket')." t WHERE t.`productid`='{$val['id']}' AND t.status = '".TICK_STA_Used."' AND " . $where_usetime;
					$dat = dbc(DBCMax)->query($sq)->done();
					if($dat) {
						foreach($dat as $row) {
							$ticketids[] = $row['ticketid'];
							$used_orderids[] = $row['orderid'];
							$fund_data[$key]['ticketused'] += $row['mutis']; 
							$fund_data[$key]['expendcount'] += $row['mutis_money'];
						}
						
					}
					$orderids = $used_orderids;
										
				}else{
					$fund_data[$key]['waitsendgoods'] = '--';
					$fund_data[$key]['ticketused'] = '--';
					$fund_data[$key]['ticketunused'] = '--';
					$fund_data[$key]['ticketfailed'] = '--';
					$fund_data[$key]['ticketoverdue'] = '--';
				}
								if('ticket' == $val['type']) {
					$sqa = "SELECT deal_money,fund_money,salary_money FROM ".table('rebate_log')." WHERE `ticketid` IN('".implode("','",$ticketids)."') AND `type` = 'master'";
				} else {
					$sqa = "SELECT deal_money,fund_money,salary_money FROM ".table('rebate_log')." WHERE orderid IN('".implode("','",$orderids)."') AND `type` = 'master'";
				}
				$dat = dbc(DBCMax)->query($sqa)->done();
				$refundmoneys = $rebatmoneys = 0;
				if($dat){
					foreach($dat as $v){
						if($v['fund_money'] > 0){
							$refundmoneys += $v['fund_money'];
							$rebatmoneys = $rebatmoneys + ($v['deal_money']-$v['fund_money']);
						}elseif($v['salary_money'] > 0){
							$refundmoneys = $refundmoneys + ($v['deal_money']-$v['salary_money']);
							$rebatmoneys += $v['salary_money'];
						} else {
							$refundmoneys += $v['deal_money'];
						}
					}
				}
				$fund_data[$key]['refundmoneys'] = $refundmoneys; 				$fund_data[$key]['rebatmoneys'] = $rebatmoneys; 
								if('stuff' == $val['type']) {
					$fund_data[$key]['expendcount'] = $refundmoneys + $rebatmoneys;
				}
			}
		}
		return $fund_data;
	}

	private function get_query_order($product_id)
	{
		$orderids = array();
		$sql = "SELECT o.orderid FROM ".table('order')." o WHERE o.productid ='".$product_id."' AND o.totalprice > 0 AND o.pay = 1 AND ".$this->query_format_sql('o.paytime','fund',0);
		$orders = dbc(DBCMax)->query($sql)->done();
		if($orders){
			foreach($orders as $val){
				$orderids[] = $val['orderid'];
			}
		}
		return $orderids;
	}

	private function query_format_sql($field = 'o.usetime',$mcode = 'user',$ms = 1)
	{
		$begin = $_GET['iscp_tvbegin_salecount_'.$mcode];
		$finish = $_GET['iscp_tvfinish_salecount_'.$mcode];
		if(in_array($field, array('o.usetime', 't.usetime'))){
			$ts_begin = $begin ? date('Ymd',strtotime($begin)) : 0;
						$ts_finish = 0;
			if($finish) {
				$finish_time = strtotime($finish);
				$ts_finish = date('Ymd', mktime(24, 0, 0, date('m', $finish_time), date('d', $finish_time), date('Y', $finish_time)));
			}
		}else{
			$ts_begin = $begin ? strtotime($begin) : 0;
			$ts_finish = $finish ? (strtotime($finish) + 86399) : 0;
		}
		$ts[] = $ts_begin ? $field.' >= '.$ts_begin : $field.' >0 ';
		$ts_finish && $ts[] = $field.' <= '.$ts_finish;
		if($_GET['ssrc'] && $_GET['sstr'] && $ms){
			if($_GET['ssrc'] == 'uid'){
				$ts[] = 'm.uid = '.(int)$_GET['sstr'];
			}elseif($_GET['ssrc'] == 'username'){
				$ts[] = 'm.username LIKE "%'.$_GET['sstr'].'%"';
			}elseif($_GET['ssrc'] =='seller_name'){
				$ts[] = 's.sellername LIKE "%'.$_GET['sstr'].'%"';
			}elseif($_GET['ssrc'] =='seller_user_name'){
				$ts[] = 'm.username LIKE "%'.$_GET['sstr'].'%"';
			}
		}
		$sql_where = implode(' AND ', $ts);
		return $sql_where;
	}
}
?>