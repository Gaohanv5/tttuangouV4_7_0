<?php

/**
 * 逻辑区：快递信息
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package logic
 * @name express.logic.php
 * @version 1.0
 */

class ExpressLogic
{

	function html( $data )
	{
		switch (mocod())
		{
			case 'buy.checkout':
				logic('buy_type')->Add(new BuyCheckoutM());
				logic('buy_type')->Add(new BuyCheckS());
				if(logic('buy_type')->CheckStuff($data) === false) return;
				include handler('template')->file('@html/express_selector');
				break;
			case 'buy.order':
				if ($data['product']['type'] != 'stuff') return;
				#虚拟订单的处理
				if($data['status'] == 200)
				{
					$mix = unserialize($data['extmsg_reply']);
					foreach($mix as $k => $v)
					{
						$EID[$v['expresstype']] =  $v['expresstype'];
					}
				}
				else
				{
					$EID[$data['expresstype']] = $data['expresstype'];
				}
				if(!$EID){
					$express_list = logic('express')->GetList($data['addressid'], $data['productid']);
					if($express_list){
						foreach($express_list as $key => $val){
							if($data['product']['weightsrc'] <= $val['firstunit']){
								$express_list[$key]['price'] = $val['firstprice'];
							}else{
								$express_list[$key]['price'] = $val['firstprice'] + ceil(($data['product']['weightsrc']-$val['firstunit'])/$val['continueunit'])*$val['continueprice'];
							}
							$express_list[$key]['unit1'] = ($val['firstunit'] >= 1000) ? 'kg' : 'g';
							$express_list[$key]['firstunit'] *= ($express_list[$key]['unit1'] == 'kg') ? 0.001 : 1;
							$express_list[$key]['unit2'] = ($val['continueunit'] >= 1000) ? 'kg' : 'g';
							$express_list[$key]['continueunit'] *= ($express_list[$key]['unit2'] == 'kg') ? 0.001 : 1;
						}
					}
					$ordertprice = $data['totalprice'];
				}
				include handler('template')->file('@html/express_displayer');
				break;
		}
	}

	function GetOne( $id, $aid = 0, $pid = false, $appuid = 0)
	{
		$elist = logic('express')->GetList($aid, $pid, $appuid);
		$found = null;
		foreach ($elist as $i => $one)
		{
			if ($id == $one['id'])
			{
				$found = $one;
				break;
			}
		}
		return $found;
	}

	function GetList($aid = 0, $pid = false, $appuid = 0)
	{
		$sql_allow = '0';
		if ($pid && meta('expresslist_of_'.$pid))
		{
			$allows = explode(',', meta('expresslist_of_'.$pid));
			$sql_allow = 'id IN (';
			foreach ($allows as $i => $alone)
			{
				if ($alone == '') continue;
				$sql_allow .= $alone.',';
			}
			$sql_allow = substr($sql_allow, 0, -1).')';
		}
		$result = array();
		$sql = 'SELECT * FROM '.table('express').' WHERE (enabled="true" OR '.$sql_allow.') AND regiond=0 ORDER BY `order` DESC';
		$rs = dbc()->Query($sql)->GetAll();
		$noIDs = '0,';
		foreach ($rs as $i => $one)
		{
			$noIDs .= $one['id'].',';
		}
		$noIDs = '('.substr($noIDs, 0, -1).')';
		$result = array_merge($result, $rs);
		$memberuid = $appuid ? 0 : user()->get('id');
		$address = logic('address')->GetOne($aid, $memberuid);
		list( , $province, $city, $country, ) = explode(',', $address['region_loc']);
		$allLikes = array(
			'[,'.$province.',]',
			'[,'.$province.','.$city.',]',
			'[,'.$province.','.$city.','.$country.',]'
			);
			$likes = '';
			foreach ($allLikes as $i => $eaLike)
			{
				$likes .= 'ea.region LIKE "%'.$eaLike.'%" OR ';
			}
			$likes = substr($likes, 0, -4);
			$sql = '
		SELECT
			e.*, ea.*, e.firstprice AS fp, e.continueprice AS cp, e.id as id
		FROM
			'.table('express_area').' ea
		RIGHT JOIN
			'.table('express').' e
		ON
			e.id = ea.parent
		WHERE
			enabled="true"
		AND
		(
			dpenable="true"
		OR
			'.$likes.'
		)
		AND
			ea.parent NOT IN '.$noIDs.'
		ORDER BY
			`order` DESC';
			$rs = dbc()->Query($sql)->GetAll();
			$filter = array();
			foreach ($rs as $i => $one)
			{
				$region = '<'.$one['region'].'>';
				if (
				!strpos($region, '[,'.$province.',]') &&
				!strpos($region, '[,'.$province.','.$city.',]') &&
				!strpos($region, '[,'.$province.','.$city.','.$country.',]')
				)
				{
					$rs[$i]['firstprice'] = $one['firstprice'] = $one['fp'];
					$rs[$i]['continueprice'] = $one['continueprice'] = $one['cp'];
				}
				else
				{
					foreach ($rs as $ii => $ione)
					{
						$i != $ii && $rs[$ii] = null;
					}
					break;
				}
				$cIndex = $one['firstprice'].'-'.$one['continueprice'].'-'.$one['fp'].'-'.$one['cp'];
				$cHash = md5($cIndex);
				$f = $filter[$cHash];
				if ($f)
				{
					$rs[$i] = null;
					continue;
				}
				$filter[$cHash] = $one;
			}
			foreach ($rs as $i => $one)
			{
				if (is_null($one)) unset($rs[$i]);
			}
			$result = array_merge($result, $rs);
			$return = array();
			foreach ($result as $i => $one)
			{
				$return[] = array(
				'id' => $one['id'],
				'name' => $one['name'],
				'firstunit' => $one['firstunit'],
				'firstprice' => $one['firstprice'],
				'continueunit' => $one['continueunit'],
				'continueprice' => $one['continueprice'],
				'detail' => $one['detail']
				);
			}
			if ($pid && meta('expresslist_of_'.$pid))
			{
				$allows = explode(',', meta('expresslist_of_'.$pid));
				foreach ($return as $i => $one)
				{
					if (!in_array($one['id'], $allows))
					{
						unset($return[$i]);
					}
				}
				sort($return);		}
				return $return;
	}
	function OrderToExpress($eid)
	{
		$sql = 'SELECT ec.name,ec.site FROM '.table('express_corp').' ec RIGHT JOIN '.table('express').' e ON ec.id = e.express WHERE e.id='.(int)$eid;
		$exp = dbc()->Query($sql)->GetRow();
		return $exp ? $exp : array();
	}

	function AdmOne($id)
	{
		$sql = 'SELECT * FROM '.table('express').' WHERE id='.(int)$id;
		$c = dbc()->Query($sql)->GetRow();
		$c['fuu'] = 'g';
		if ($c['firstunit'] >= 1000)
		{
			$c['firstunit'] *= 0.001;
			$c['fuu'] = 'kg';
		}
		$c['cuu'] = 'g';
		if ($c['continueunit'] >= 1000)
		{
			$c['continueunit'] *= 0.001;
			$c['cuu'] = 'kg';
		}
		$c['firstprice'] *= 1;
		$c['continueprice'] *= 1;

		$sql = 'SELECT * FROM '.table('express_area').' WHERE parent='.$c['id'];
		$regions = dbc()->Query($sql)->GetAll();
		foreach ($regions as $i => $one)
		{
			$regions[$i]['firstprice'] *= 1;
			$regions[$i]['continueprice'] *= 1;
			$alist = explode('][', $one['region']);
			$regionsName = array();
			foreach ($alist as $ix => $area)
			{
				$area = trim(preg_replace('/[\[\]]/', '', $area));
				if ($area == '') continue;
				$A = $this->AreaGet($area);
				$regionsName[$ix] = array(
					'name' => $A['name'],
					'loc' => $area
				);
			}
			$regions[$i]['regionName'] = $regionsName;
		}
		$c['regions'] = $regions;
		return $c;
	}

	function SrcOne($id)
	{
		$sql = 'SELECT * FROM '.table('express').' WHERE id='.(int)$id;
		return dbc(DBCMax)->query($sql)->limit(1)->done();
	}

	function SrcList()
	{
		$sql = 'SELECT * FROM '.table('express').' ORDER BY `ORDER` DESC';
		return dbc(DBCMax)->query($sql)->done();
	}

	public function CorpOne($id)
	{
		return dbc(DBCMax)->select('express_corp')->where('id='.(int)$id)->limit(1)->done();
	}

	function CorpList($enabled = 'true')
	{
		$sql_limit_enabled = 'enabled="'.$enabled.'"';
		if ($enabled == 'all')
		{
			$sql_limit_enabled = '1';
		}
		$sql = 'SELECT * FROM '.table('express_corp').' WHERE '.$sql_limit_enabled;
		return dbc(DBCMax)->query($sql)->done();
	}

	public function CorpDel($id)
	{
		$this->cdp()->Delete($id);
		return dbc(DBCMax)->delete('express_corp')->where('id='.(int)$id)->done();
	}

	function Del($id)
	{
		$id = (int) $id;
		dbc()->SetTable(table('express'));
		dbc()->Delete('', 'id='.$id);
		dbc()->SetTable(table('express_area'));
		dbc()->Delete('', 'parent='.$id);
	}

	function AreaGet($path)
	{
		$sql = 'SELECT * FROM '.table('regions').' WHERE path = "'.$path.'"';
		return dbc(DBCMax)->query($sql)->limit(1)->done();
	}

	function AreaDel($id)
	{
		dbc()->SetTable(table('express_area'));
		dbc()->Delete('', 'id='.(int)$id);
	}

	public function CID2Name($id)
	{
		$r = dbc(DBCMax)->select('express_corp')->in('name')->where('id='.(int)$id)->limit(1)->done();
		return $r['name'];
	}

	function orderExpressUpdate($orderid, $invoice)
	{
		$ary = array(
			'invoice'=>$invoice,
			'expresstime'=>time(),
			'status'=>4 		);
		$this->DatabaseHandler->SetTable(TABLE_PREFIX.'tttuangou_order');
		$this->DatabaseHandler->Update($ary, 'orderid='.$orderid);
		return true;
	}

	function orderWaitExpressCount($productid=0, $sql_search='')
	{
		if ($productid>0)
		{
			$limit = 'productid='.(int)$productid;
		}
		else
		{
			$limit = 'status=1';
		}
		$sql='select count(orderid) AS count from '.TABLE_PREFIX.'tttuangou_order where pay = 1 and addressid <> 0 and '.$limit.' '.$sql_search;
		$query = $this->DatabaseHandler->Query($sql);
		$orderCount=$query->GetRow();
		return $orderCount['count'];
	}

	function orderSentExpressCount($productid=0, $sql_search='')
	{
		if ($productid>0)
		{
			$limit = 'productid='.(int)$productid;
		}
		else
		{
			$limit = 'status IN(4,9)';
		}
		$sql='select count(orderid) AS count from '.TABLE_PREFIX.'tttuangou_order where pay = 1 and addressid <> 0 and '.$limit.' '.$sql_search;
		$query = $this->DatabaseHandler->Query($sql);
		$orderCount=$query->GetRow();
		return $orderCount['count'];
	}

	function orderWaitExpressList($productid=0, $page=0, $epage=20, $sql_search='')
	{
		$condition = '1';
		if ($productid > 0)
		{
			$condition = 'o.productid='.(int)$productid;
		}
		$limit = '';
		if ($page > 0)
		{
			$limit = ' LIMIT '.((int)$page-1)*$epage.','.$epage;
		}
		$sql='SELECT p.name,p.successnum,o.orderid,o.addressid,m.username,o.paytime FROM '.TABLE_PREFIX.'tttuangou_order o LEFT JOIN '.TABLE_PREFIX.'system_members m ON m.uid=o.userid LEFT JOIN '.TABLE_PREFIX.'tttuangou_product p ON p.id=o.productid WHERE p.type = "stuff" AND o.pay = 1 AND p.status IN(0,1,2) AND o.status = 1 AND '.$condition.' '.$sql_search.' ORDER BY o.paytime ASC'.$limit;
		return $this->DatabaseHandler->Query($sql)->GetAll();
	}

	function orderSentExpressList($productid=0, $page=0, $epage=20, $sql_search='')
	{
		$condition = '1';
		if ($productid > 0)
		{
			$condition = 'o.productid='.(int)$productid;
		}
		$limit = '';
		if ($page > 0)
		{
			$limit = ' LIMIT '.((int)$page-1)*$epage.','.$epage;
		}
		$sql='SELECT p.name,p.successnum,o.orderid,o.addressid,m.username,o.expresstime FROM '.TABLE_PREFIX.'tttuangou_order o LEFT JOIN '.TABLE_PREFIX.'system_members m ON m.uid=o.userid LEFT JOIN '.TABLE_PREFIX.'tttuangou_product p ON p.id=o.productid WHERE p.type = "stuff" AND o.pay = 1 AND p.status IN(0,1,2) AND o.status IN(4,9) AND '.$condition.' '.$sql_search.' ORDER BY o.expresstime DESC'.$limit;
		return $this->DatabaseHandler->Query($sql)->GetAll();
	}

	function orderExpressConfirm($oid)
	{
		$ary = array(
			'status'=>9
		);
		$this->DatabaseHandler->SetTable(TABLE_PREFIX.'tttuangou_order');
		return $this->DatabaseHandler->Update($ary, 'orderid='.(int)$oid.' AND userid='.MEMBER_ID);
	}

	function Accessed($class, &$data)
	{
		if ($class == 'order.save')
		{
			$id = post('express_id', 'int');
			if (!$id || $data['addressid'] == 0)
			{
				$data['expressprice'] = 9999;
				return;
			}
			$data['expresstype'] = $id;
			$express = $this->GetOne($id, $data['addressid'], $data['productid'], $data['userid']);
			if (!$express)
			{
				$data['expressprice'] = 9999;
				return;
			}
			$product = logic('product')->BuysCheck($data['productid']);
			$allWeight = $data['productnum'] * $product['weightsrc'];
			$price = $express['firstprice'];
			if ($allWeight > $express['firstunit'])
			{
				$lessWeight = $allWeight - $express['firstunit'];
				if ($express['continueunit'] <= 0)
				{
					$express['continueunit'] = 1;
				}
				$price += ceil($lessWeight / $express['continueunit']) * $express['continueprice'];
			}
			$data['expressprice'] = $price;
		}
		elseif ($class == 'order.show')
		{
			if ($data['product']['type'] == 'ticket') return;
			$data['price_of_total'] += $data['expressprice'];
		}
	}


	function virtual_order_expressprice_fix($virtual_orderid, $fix_suborders = false) {
		if(false == logic('order')->is_virtual_order($virtual_orderid)) {
			return false;
		}
		$vorder = logic('order')->SrcOne($virtual_orderid);
		if(false == $vorder) {
			return false;
		}
		$price = $vorder['expressprice'];
		$orders = logic('order')->getAllOrdersByOrderid($virtual_orderid);//获取虚拟订单id下的所有订单 
		if(is_array($orders) && count($orders) > 1 && $vorder['expresstype']) {
			$express = $this->GetOne($vorder['expresstype'], $vorder['addressid'], $vorder['productid'], $vorder['userid']);
			if(false == $express) {
				return false;
			}
			$allWeight = 0;
			$expresscount = 0;
			$eorders = array();
			foreach($orders as $order) {
				if($order['expresstype'] == $vorder['expresstype'] && $vorder['addressid'] == $order['addressid']) {
					$expresscount++;
					$product = logic('product')->BuysCheck($order['productid']);
					$allWeight = $allWeight + ($order['productnum'] * $product['weightsrc']);
					$eorders[] = $order;
				}
			}
			if($expresscount > 1) {
				$price = $express['firstprice'];
				if ($allWeight > $express['firstunit']) {
					$lessWeight = $allWeight - $express['firstunit'];
					if ($express['continueunit'] <= 0) {
						$express['continueunit'] = 1;
					}
					$price += ceil($lessWeight / $express['continueunit']) * $express['continueprice'];
				}

				if($price != $vorder['expressprice']) {
					$price = round($price, 2);
					logic('order')->Update($vorder['orderid'], array('expressprice'=>$price, 'totalprice'=>round($vorder['totalprice'] + $price - $vorder['expressprice'], 2)));
				}


				if($fix_suborders) {
					$price_avg = round($price / $expresscount, 2);
					foreach($eorders as $order) {
						if($price_avg != $order['expressprice']) {
							$val = $price_avg - $order['expressprice'];
							$data = array('expressprice'=>$price_avg);
							$totalprice = round($order['totalprice'] + $val, 2);
							if($totalprice != $order['totalprice']) {
								$data['totalprice'] = $totalprice;
							}
							$paymoney = round($order['paymoney'] + $val, 2);
							if($paymoney != $order['paymoney']) {
								$data['paymoney'] = $paymoney;
							}
							logic('order')->Update($order['orderid'], $data);
						}
					}
				}
			}
		}
		return $price;
	}


	public function cdp()
	{
		return loadInstance('logic.express.cdp.service', 'Express_Corp_Delivery_Print');
	}
}

/**
 * 快递公司运单打印功能
 * @author Moyo <dev@uuland.org>
 * @version 1.0
 * @time 2011-10-20 14:21:00
 */
class Express_Corp_Delivery_Print
{

	public function supportLables()
	{
		return array(
			'site_name' => '网站 - 名称',
			'site_url' => '网站 - 网址',
			'sender_province' => '寄件人 - 省份',
			'sender_city' => '寄件人 - 城市',
			'sender_country' => '寄件人 - 区/县',
			'sender_address' => '寄件人 - 街道地址',
			'sender_address_all' => '寄件人 - 详细地址',
			'sender_phone' => '寄件人 - 联系电话',
			'sender_name' => '寄件人 - 姓名',
			'sender_zip' => '寄件人 - 邮编',
			'receiver_province' => '收件人 - 省份',
			'receiver_city' => '收件人 - 城市',
			'receiver_country' => '收件人 - 区/县',
			'receiver_address' => '收件人 - 街道地址',
			'receiver_address_all' => '收件人 - 详细地址',
			'receiver_phone' => '收件人 - 联系电话',
			'receiver_name' => '收件人 - 姓名',
			'receiver_zip' => '收件人 - 邮编',
			'order_id' => '订单 - 订单号',
			'order_remark' => '订单 - 备注',
			'order_invoice' => '订单 - 快递单号',
			'time_year' => '当日日期 - 年',
			'time_month' => '当日日期 - 月',
			'time_day' => '当日日期 - 日',
			'order_time_create' => '时间 - 下单时间',
			'time_print' => '时间 - 打印时间',
			'char_pigeon' => '符号 - √',
		);
	}

	public function CreatePrinterConfig($oid, $senderID)
	{
		$order = logic('order')->SrcOne($oid);
		$express = logic('express')->SrcOne($order['expresstype']);
		$corp = logic('express')->CorpOne($express['express']);
		$sender = logic('address')->GetOne($senderID);
		$receiver = logic('address')->GetOne($order['addressid']);
		$cdp = logic('express')->cdp()->GetOne($corp['id']);
		if (!$cdp) return array('__error__' => true, 'corpID' => $corp['id']);
		$lables = $this->supportLables();
		$lables['site_name'] = ini('settings.site_name');
		$lables['site_url'] = ini('settings.site_url');
		$lables['sender_province'] = $sender['loc_province'];
		$lables['sender_city'] = $sender['loc_city'];
		$lables['sender_country'] = $sender['loc_country'];
		$lables['sender_address'] = $sender['address'];
		$lables['sender_address_all'] = $sender['loc_province'].' '.$sender['loc_city'].' '.$sender['loc_country'].' '.$sender['address'];
		$lables['sender_phone'] = $sender['phone'];
		$lables['sender_name'] = $sender['name'];
		$lables['sender_zip'] = $sender['zip'];
		$lables['receiver_province'] = $receiver['loc_province'];
		$lables['receiver_city'] = $receiver['loc_city'];
		$lables['receiver_country'] = $receiver['loc_country'];
		$lables['receiver_address'] = $receiver['address'];
		$lables['receiver_address_all'] = $receiver['loc_province'].' '.$receiver['loc_city'].' '.$receiver['loc_country'].' '.$receiver['address'];
		$lables['receiver_phone'] = $receiver['phone'];
		$lables['receiver_name'] = $receiver['name'];
		$lables['receiver_zip'] = $receiver['zip'];
		$lables['order_id'] = $order['orderid'];
		$lables['order_remark'] = $order['remark'];
		$lables['order_invoice'] = $order['invoice'];
		$lables['order_time_create'] = date('Y-m-d H:i:s', $order['buytime']);
		$lables['time_year'] = date('Y');
		$lables['time_month'] = date('m');
		$lables['time_day'] = date('d');
		$lables['time_print'] = date('Y-m-d H:i:s', time());
		$lables['char_pigeon'] = '√';
		$cfgString = $this->ReplacePrinterConfig($cdp['config'], $lables);
		return array('cdp' => $cdp, 'config' => $cfgString);
	}

	private function ReplacePrinterConfig($string, $data)
	{
		foreach ($data as $key => $val)
		{
			$regxF = '/t_'.$key.',(.*?),(\d+),(\d+),(\d+),(\d+),b_'.$key.'/i';
			preg_match_all($regxF, $string, $mchs);
			if (empty($mchs[0])) continue;
			$pString = $mchs[0][0];
			$pReplace = str_replace($mchs[1][0], $val, $mchs[0][0]);
			$string = str_replace($pString, $pReplace, $string);
		}
		return $string;
	}

	public function Update($cid, $data)
	{
		$cid = (int) $cid;
		$aff = dbc(DBCMax)->update('express_cdp')->where('cid='.$cid)->data(array_merge($data, array('upstime' => time())))->done();
		$aff || $aff = dbc(DBCMax)->insert('express_cdp')->data(array_merge($data, array('cid' => $cid, 'upstime' => time())))->done();
		return $aff;
	}

	public function Delete($cid)
	{
		return dbc(DBCMax)->delete('express_cdp')->where('cid='.(int)$cid)->done();
	}

	public function GetOne($cid)
	{
		$r = dbc(DBCMax)->select('express_cdp')->where('cid='.(int)$cid)->limit(1)->done();
		$r['bgid'] && $r['background'] = imager($r['bgid']);
		return $r;
	}

	public function AddressList()
	{
		return logic('address')->GetList(-1);
	}

	public function Printed($sign, $sender = false)
	{
		$printLOG = dbc(DBCMax)->select('express_printer_log')->where('sign='.(float)$sign)->limit(1)->done();
		if (!$sender)
		{
			return $printLOG['sign'] ? true : false;
		}
		$order = logic('order')->SrcOne($sign);
		$express = logic('express')->SrcOne($order['expresstype']);
		$corp = logic('express')->CorpOne($express['express']);
		$corpID = $corp['id'];
		if ($printLOG['sign'])
		{
			$r = dbc(DBCMax)->update('express_printer_log')->where('sign='.(float)$sign)->data(array('corp'=>$corpID, 'sender'=>$sender, 'upstime'=>time()))->done();
		}
		else
		{
			$r = dbc(DBCMax)->insert('express_printer_log')->data(array('sign'=>(float)$sign, 'corp'=>$corpID, 'sender'=>$sender, 'upstime'=>time()))->done();
		}
		return $r;
	}

	public function PrintedCount($corpID)
	{
		$r = dbc(DBCMax)->select('express_printer_log')->in('COUNT(1) aS CCNT')->where('corp='.(int)$corpID)->limit(1)->done();
		return $r['CCNT'] ? $r['CCNT'] : 0;
	}

	public function hasPrinterTemplate($corpID)
	{
		$r = dbc(DBCMax)->select('express_cdp')->in('COUNT(1) aS CCNT')->where('cid='.(int)$corpID)->limit(1)->done();
		return $r['CCNT'] ? true : false;
	}

	public function sync()
	{
		include_once LOGIC_PATH.'express.cdp.sync.php';
		return loadInstance('logic.express.cdp.sync', 'Express_Corp_Delivery_Sync');
	}
}

?>