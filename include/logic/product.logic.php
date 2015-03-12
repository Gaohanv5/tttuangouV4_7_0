<?php

/**
 * 逻辑区：产品相关
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package logic
 * @name product.logic.php
 * @version 1.1
 */

class ProductLogic
{

	public function display($category = '1')
	{	
		//var_dump($_GET);exit;	区分调用首页默认显示列表和点击分类列表模板
		if(count($_GET)==1 && $_GET['mod'] == 'index'){
			define('INDEX_DEFAULT', TRUE);// html/igos/meituan/default.html
		}else{
			define('INDEX_DEFAULT', false);// html/igos/meituan/index.html
		}
		$productID = get('view', 'int');//获取变量view的值,_GET _POST 获取
		if ( false == $productID )
		{
			$mutiView = true;
			if (ini('ui.igos.pager'))//读取配置文件setting/ui.php  里的一个名为$config["igos"]["pager"] 数组的值
			{
			}
			else
			{
				$_GET[EXPORT_GENEALL_FLAG] = EXPORT_GENEALL_VALUE;
			}
			$product = logic('product')->GetList(logic('misc')->City('id'), PRO_ACV_Yes, $category);
				
		}
		else	//商品详情
		{
			$mutiView = false;
			$product = logic('product')->GetOne($productID);

			logic('recent_view')->add($productID);
		}
		$usePager = get('page', 'int') > 0 ? true : false;
		if ( !$usePager && false == $product )
		{
			return false;
		}
		if ( !$usePager && $mutiView && count($product) == 1 )
		{
			$mutiView = false;
			$product = $product[0];
			$product['product_link'] = $this->get_link_product($product['linkid']);
			$_GET['view'] = $product['id'];
		}
		else if (ini('ui.igos.dsper') && $mutiView && count($product) > 1)
		{
			logic('product')->reSort($product);
		}
		$file = $mutiView ? 'home' : 'detail';
		if(INDEX_DEFAULT === true && $mutiView && false == ini('ui.igos.oldindex')){
			$city_id = logic('misc')->City('id');
			$cache_id = 'default.catalog.procount.' . $city_id;
			$cache_product = fcache($cache_id, 1800);
			if($cache_product){
				$product = $cache_product;
			}else{
				$catalogs = logic('catalog')->Navigate(6);
				if($catalogs){
					foreach($catalogs as $key => $val){
						$val['navcate'] = logic('catalog')->Filter($val['flag'], 'product');
						$catalogs[$key]['product'] = logic('product')->GetList($city_id, PRO_ACV_Yes, $val['navcate']);
						if(!$catalogs[$key]['product']){
							unset($catalogs[$key]);
						}
					}
					$product = $catalogs;
				}
				fcache($cache_id, $product);
			}
		}
		return array(
			'mutiView' => $mutiView,
			'file' => $file,
			'product' => $product
		);
	}
	function GetNewList($limit=2, $ishot = false)
	{
		$cid = logic('misc')->City('id');
		$now = time();
		$where = '(display = '.PRO_DSP_Global.' OR (display = '.PRO_DSP_City.' AND city = '.$cid.'))
			AND begintime < '.$now.' AND overtime > '.$now.'
			AND saveHandler = "normal"' .
		($ishot ? ' AND hotenabled = "true" ' : '');
		$order = ($ishot ? ' `order` DESC, ' : '');
		$sql = 'SELECT `id`,`name`,`flag`,`price`,`nowprice`,`img`, `linkid`,`virtualnum`,(`virtualnum`+`sells_count`) AS `sells_count`,`begintime`,`intro`,`type`,`overtime`,`maxnum`,`successnum`,`is_countdown`
		FROM '.table('product').' WHERE '.$where.' ORDER BY '.$order.' `id` DESC LIMIT '.$limit;
		$product = dbc(DBCMax)->query($sql)->done();
		if($product) {
			foreach($product as $key => $val){
				$imgs = $val['img'] != '' ? explode(',', $val['img']) : null;
				$product[$key]['img'] = ($imgs && $imgs[0]) ? $imgs[0] : 0;
				$product[$key]['pic'] = imager($product[$key]['img'],IMG_Normal);
			}
			$product = $this->__parse_result($product);
		}
		return $product;
	}

	function GetOne( $id, $cached = true )
	{
		$ckey = 'product.getone.'.$id;
		$list = $cached ? cached($ckey) : false;
		if ($list) return $list;
		$sql = 'SELECT p.*,s.sellername,s.sellerphone,s.selleraddress,s.sellerurl,s.sellermap,s.trade_time
		FROM
			' . table('product') . ' p
		LEFT JOIN ' . table('seller') . ' s
		ON
			(p.sellerid = s.id)
		WHERE
			p.id = ' . (int)$id;
		$data = $this->__parse_result( dbc(DBCMax)->query($sql)->limit(1)->done() );

		if( $data ){
			if( $data['begintime'] > time() ){
				$lasttime = $data['begintime'] - time();
				if( $lasttime > 86400 ){
					$data['begin_date'] = date('Y-m-d H:i:s',$data['begintime']);
				}else{
					$data['limit_time'] = $lasttime;
				}
			}
			if($data['is_countdown'] == 1){
				logic('order')->FreeCountDownOrder($id);
			}
			$data['product_link'] = $this->get_link_product($data['linkid']);
		}
		return cached($ckey, $data);
	}

	public function GetFirst()
	{
		$list = $this->GetList(logic('misc')->City('id'), PRO_ACV_Yes);
		return $list[0];
	}

	public function get_list_ext_sql($sql = null)
	{
		static $ssql = '';
		if (is_null($sql))
		{
			$sql = $ssql;
			$ssql = '';
			return $sql;
		}
		else
		{
			return $ssql = $sql;
		}
	}

	function GetOwnerList( $sellerID = 0, $limit = null, $parse = false ){
		$sql_limit = '';
		if( !empty($limit) && (int)$limit>0 ){
			$sql_limit = ' LIMIT '.(int)$limit;
		}
		$sql = "SELECT `id`,`name`,`price`,`nowprice`,`img`,`virtualnum`,(`virtualnum`+`sells_count`) AS `sells_count`,`begintime`,`intro`,`type`,`overtime`,`maxnum`,`successnum`,`is_countdown`
		FROM `".table('product')."` WHERE `display`>0 AND `saveHandler`='normal' AND `sellerid`='".$sellerID."' AND `overtime`>=".time()." ORDER BY `overtime` ASC ".$sql_limit;
		$data = dbc(DBCMax)->query($sql)->done();
		if($data){
			foreach($data as &$v) {
				$v['imgs'] = $img = explode(',',$v['img']);
				$v['pic'] = imager($img[0],IMG_Normal);
			}
			if($parse) {
				$data = $this->__parse_result($data);
			}
		}
		return $data;
	}

	function GetList( $cid = -1, $actived = null, $extend = '1' )
	{
		$cid = (int) $cid;
		$sql_limit_city = '1';
		if ( $cid > 0 )
		{
			$sql_limit_city = '(p.display = '.PRO_DSP_Global.' OR (p.display = '.PRO_DSP_City.' AND p.city = ' . $cid . ') )';
		}
		$sql_limit_actived = '1';
		$now = time();
		if ( !is_null($actived) )
		{
			if ($actived === PRO_ACV_Yes)
			{
				$sql_limit_actived = 'p.begintime < ' . $now . ' AND p.overtime > ' . $now;
			}
			else
			{
				$sql_limit_actived = 'p.overtime < ' . $now;
			}
		}
		$sql = 'SELECT p.*,s.sellername,s.sellerphone,s.selleraddress,s.sellerurl,s.sellermap,p.totalnum'.$this->get_list_ext_sql();
		$sql .= ' FROM ' . table('product') . ' p LEFT JOIN ' . table('seller') . ' s ON (p.sellerid=s.id) WHERE ';
		$sql .= $sql_limit_actived . ' AND ' . $sql_limit_city . ' AND ' . $extend;
		$sql .= INDEX_DEFAULT === true ? '' : (' AND ' . logic('city')->place_sql_filter() . ' AND ' . logic('product_tag')->product_sql_filter() . ' AND ' . logic('isearcher')->product_sql_filter());
		$sql .= ' AND p.saveHandler = "normal" ORDER BY ';
		$sql .= INDEX_DEFAULT === true ? 'p.order DESC, p.id DESC' : logic('sort')->product_sql_filter();
		if(INDEX_DEFAULT === true && false == ini('ui.igos.oldindex')){
			$sql .=  ' LIMIT 6 ';
		}else{
			logic('isearcher')->Linker($sql);
			$sql = page_moyo($sql);
		}
		//debug($sql);
		$result = dbc(DBCMax)->query($sql)->done();
		return $this->__parse_result($result);
	}

	function GetOtherList( $city_id, $category, $selfid, $type = 0)
	{
		$now = time();
		$city_id = (int) $city_id;
		$catasql = $type ? ' AND category = '.(int) $category : '';
		$selfid = (int) $selfid;
		$sql_where = '(display = '.PRO_DSP_Global.' OR (display = '.PRO_DSP_City.' AND city = '.$city_id.'))'.$catasql.' AND begintime < '.$now.' AND overtime > '.$now.' AND id != '.$selfid.' AND saveHandler = "normal"';
		$result = dbc(DBCMax)->select('product')->where($sql_where)->order('id.desc')->limit(5)->done();
		if ( count($result) < 5 && $type)
		{
			$sql_where = '(display = '.PRO_DSP_Global.' OR (display = '.PRO_DSP_City.' AND city = '.$city_id.')) AND begintime < '.$now.' AND overtime > '.$now.' AND id != '.$selfid.' AND saveHandler = "normal"';
			$result = dbc(DBCMax)->select('product')->where($sql_where)->order('id.desc')->limit(5)->done();
		}
		return $this->__parse_result($result);
	}

	public function SrcOne($id)
	{
		return dbc(DBCMax)->select('product')->where('id='.(int)$id)->limit(1)->done();
	}

	public function Where($sql_limit)
	{
		$sql = 'SELECT * FROM '.table('product').' WHERE '.$sql_limit;
		return dbc(DBCMax)->query($sql)->done();
	}

	public function Update($id, $array)
	{
		$id = (int) $id;
		zlog('product')->update($id, $array);
		dbc()->SetTable(table('product'));
		if(isset($array['@extra'])) unset($array['@extra']);
		dbc()->Update($array, 'id = '.$id);
		fcache('default.catalog.procount', 0);
	}

	public function Update_direct($id, $array)
	{
		dbc()->SetTable(table('product'));
		if(isset($array['@extra'])) unset($array['@extra']);
		dbc()->Update($array, 'id = '.(int)$id);
		fcache('default.catalog.procount', 0);
	}

	public function Delete($id)
	{
		$id = (int) $id;
		$p = $this->SrcOne($id);
		zlog('product')->delete($id, $p);
		$imgs = explode(',', $p['img']);
		foreach ($imgs as $i => $iid)
		{
			logic('upload')->Delete($iid);
		}
		dbc(DBCMax)->delete('product')->where('id='.$id)->done();
		$sqls = array(
						'DELETE FROM '.table('finder').' WHERE productid='.$id,
						'DELETE FROM '.table('ticket').' WHERE productid='.$id,
						'DELETE FROM '.table('favorite').' WHERE pid='.$id,
		);
		$orderList = logic('order')->Where('productid='.$id);
		foreach ($orderList as $i => $order)
		{
			$oid = $order['orderid'];
			$sqls[] = 'DELETE FROM '.table('order').' WHERE orderid='.$oid;
			$sqls[] = 'DELETE FROM '.table('order_clog').' WHERE sign='.$oid;
			$sqls[] = 'DELETE FROM '.table('paylog').' WHERE sign='.$oid;
		}
		foreach ($sqls as $i => $sql)
		{
			dbc(DBCMax)->query($sql)->done();
		}
		logic('seller')->product_del($p['sellerid']);
		fcache('default.catalog.procount', 0);
		return true;
	}

	public function Publish($data)
	{
		logic('seller')->product_add($data['sellerid']);
		dbc()->SetTable(table('product'));
		$id = dbc()->Insert($data);
		zlog('product')->publish($id, $data);
		fcache('default.catalog.procount', 0);
		return $id;
	}

	function MoneySaves()
	{
		$now = time();
		$sql = 'SELECT SUM((price-nowprice)*(virtualnum+totalnum)) AS saves
		FROM
			' . table('product') . '
		WHERE
			overtime < ' . $now . '
		AND
			status = 2';
		$result = dbc(DBCMax)->query($sql)->limit(1)->done();
		return $result['saves'];
	}

	function SellsCount( $id )
	{
		$sql = 'SELECT SUM(productnum) AS sums
		FROM
			' . table('order') . '
		WHERE
			productid=' . intval($id) . '
		AND
			'.logic('pay')->OrderPaidSQL().'
		AND
			status = '.ORD_STA_Normal;
		$result = dbc(DBCMax)->query($sql)->limit(1)->done();
		return (int)$result['sums'];
	}

	function BuyersCount( $id )
	{
		$sql = 'SELECT COUNT(1) AS cnts
		FROM
			' . table('order') . '
		WHERE
			productid = ' . intval($id) . '
		AND
			'.logic('pay')->OrderPaidSQL().'
		AND
			status = '.ORD_STA_Normal;
		$result = dbc(DBCMax)->query($sql)->limit(1)->done();
		return (int)$result['cnts'];
	}

	function Surplus( $maxnum, $sells )
	{
		$surplusnum = $maxnum - $sells;
		return $surplusnum;
	}
	//检查购买产品状态
	function BuysCheck( $id, $checkIfBuyed = true, $curBuys = false, $ord_is_Paid = null, $uid = 0, $amount=0 )
	{
		$id = (int) $id;
		if (!$id) return array('false' => __('请选择你要购买的产品！'));
		$sql = 'SELECT *
		FROM
			' . table('product') . '
		WHERE
			id = ' . $id;
		$product = dbc(DBCMax)->query($sql)->limit(1)->done();
		if (!$product['id']) return array('false' => __('没有找到相应的产品！'));
		$now = time();
		if ( $product['begintime'] > $now ) return array('false' => __(TUANGOU_STR . '还没有开始哦！'));
		if ( $product['overtime'] < $now ) return array('false' => __(TUANGOU_STR . '已经结束了哦！'));
		if ( $product['maxnum'] > 0 ) 		{
			$surplus = $this->Surplus($product['maxnum'], $this->SellsCount($id));
			if ($curBuys && $ord_is_Paid === ORD_PAID_Yes)
			{
				$surplus += $curBuys;
			}
			if($surplus < 1) return array('false' => __('该产品已经卖完了！下次请赶早'));
			if($curBuys && $curBuys > $surplus) return array('false' => __('该产品库存已经不足，请重新下单购买！'));
		}
		if ( $checkIfBuyed && $product['multibuy'] == 'false' )
		{
			$buid = $uid ? $uid : user()->get('id');
			if ( $this->AlreadyBuyed($id, $buid) ) return array('false' => __('您已经购买过此产品了哦！'));
		}
		if(0 === $amount) {
			$amount = $curBuys;
		}
		if(false !== $amount) {
			if($amount < 1 || $amount < $product['oncemin']) {
				return array('false' => __('您一次不能购买这么多！'));
			}
			if($product['oncemax'] > 0  && $amount > $product['oncemax']) {
				return array('false' => __('您一次不能购买这么多！'));
			}
		}
		return $this->__parse_result($product);
	}

	function AlreadyBuyed( $id, $uid, $comment = 0)
	{
		$sql = '
		SELECT
			orderid
		FROM
			' . table('order') . '
		WHERE
			productid = ' . (int)$id . '
		AND
			userid= ' . (int)$uid . '
		AND
			pay=1';
		if($comment){
			$sql .= ' AND comment = 1 ';
		}
		$result = dbc()->Query($sql)->GetRow();
		return $result ? true : false;
	}

	function Maintain($pid = false)
	{
		logic('product')->UpdateSTATUS();
		if ($pid)
		{
			$product = logic('product')->GetOne($pid, false);
			if ($product['succ_remain'] <= 0)
			{
				logic('order')->findSuccess($pid);
			}
			$sellsCount = $this->SellsCount($pid);
			if (!$product['is_countdown'] && $sellsCount)			{
				$this->Update_direct($pid, array('sells_count' => $sellsCount));
			}
		}
	}

	function UpdateSTATUS()
	{
		$now = time();
		$sqls = array(
						'UPDATE '.table('product').' SET status='.PRO_STA_Failed.' WHERE successnum>(virtualnum+totalnum) AND overtime<'.$now.' AND begintime<'.$now,
						'UPDATE '.table('product').' SET status='.PRO_STA_Finish.' WHERE successnum<=(virtualnum+totalnum) AND overtime<'.$now.' AND begintime<'.$now,
						'UPDATE '.table('product').' SET status='.PRO_STA_Normal.' WHERE successnum>(virtualnum+totalnum) AND overtime>'.$now.' AND begintime<'.$now,
						'UPDATE '.table('product').' SET status='.PRO_STA_Success.' WHERE successnum<=(virtualnum+totalnum) AND overtime>'.$now.' AND begintime<'.$now,
		);
		$r = 0;
		foreach ($sqls as $i => $sql)
		{
			$r += dbc(DBCMax)->query($sql)->done();
		}
		$r && zlog('product')->maintain($r);
	}

	private function __parse_result( $product )
	{
		if ( ! $product ) return false;
		if ( is_array($product[0]) )
		{
			$returns = array();
			foreach ( $product as $i => $one )
			{
				$returns[] = $this->__parse_result($one);
			}
			return $returns;
		}
		$product['price'] *= 1;
		$product['nowprice'] *= 1;
		if ( $product['nowprice'] > 0 )
		{
			$product['discount'] = round(10 / ($product['price'] / $product['nowprice']), 1);
		}
		else
		{
			$product['discount'] = 0;
		}
		if ( $product['discount'] <= 0 ) $product['discount'] = 0;
		$product['time_remain'] = $product['overtime'] - time();
		$product['succ_total'] = $product['successnum'];
		if ($product['type'] == 'prize')
		{
			$product['succ_real'] = logic('prize')->sigCount('pid='.$product['id']);
		}
		else
		{
			$product['succ_real'] = $this->BuyersCount($product['id']);
		}
		$product['succ_buyers'] = $product['succ_real'] + $product['virtualnum'];
		$product['succ_remain'] = $product['succ_total'] - $product['succ_buyers'];
		if ($product['type'] == 'prize')
		{
			$product['sells_real'] = $product['succ_real'];
		}
		else
		{
			$product['sells_real'] = $this->SellsCount($product['id']);
		}
		$product['sells_count'] = ($product['is_countdown'] ? (int)$product['sells_count'] : $product['sells_real']) + $product['virtualnum'];
		if ($product['oncemin'] <= 0)
		{
			$product['oncemin'] = 1;
		}
		if ( $product['maxnum'] > 0 )
		{
			$product['surplus'] = $this->Surplus($product['maxnum'], $product['sells_real']);
		}
		else
		{
			$product['surplus'] = 9999;
		}

		$product['imgs'] = ($product['img'] != '') ? explode(',', $product['img']) : null;
		$product['img'] = $product['imgs'][0];
		$product['sellermap'] = explode(',', $product['sellermap']);
		if ($product['type'] == 'stuff')
		{
			$product['weightsrc'] = $product['weight'];
			$product['weightunit'] = ($product['weight'] >= 1000) ? 'kg' : 'g';
			$product['weight'] *= ($product['weightunit'] == 'kg') ? 0.001 : 1;
		}
		$this->PresellParser($product);
		$product['tags'] = logic('product_tag')->get_list($product['id']);
		return $product;
	}

	public function AVParser(&$product)
	{
		if ( ! $product ) return false;
		if ( is_array($product[0]) )
		{
			$returns = array();
			foreach ( $product as $i => &$one )
			{
				$this->AVParser($one);
			}
			return;
		}
		$base = 'productid='.$product['id'];
		$STA_Normal = 'status='.ORD_STA_Normal;
		$product['mny_all'] = (float)logic('order')->Summary($base.' AND '.$STA_Normal);
		$product['mny_paid'] = (float)logic('order')->Summary($base.' AND pay='.ORD_PAID_Yes.' AND '.$STA_Normal);
		$product['mny_waited'] = (float)logic('order')->Summary($base.' AND pay='.ORD_PAID_No.' AND '.$STA_Normal);
		$product['mny_refund'] = (int)logic('order')->Summary($base.' AND status='.ORD_STA_Refund);
	}

	private function PresellParser(&$product)
	{
		if (isset($product['id']) && $product['id'])
		{
			$ptext = meta('p_presell_text_'.$product['id']);
			if ($ptext)
			{
				$pprice = meta('p_presell_price_full_'.$product['id']);
				$product['presell'] = array(
					'text' => $ptext,
					'price_full' => $pprice
				);
			}
		}
	}

	public function PresellSubmit($id)
	{
		if (post('presell_is'))
		{
			meta('p_presell_text_'.$id, post('presell_text'));
			meta('p_presell_price_full_'.$id, post('presell_price'));
		}
		else
		{
			meta('p_presell_text_'.$id, null);
			meta('p_presell_price_full_'.$id, null);
		}
	}

	public function STA_Name($STA_Code)
	{
		$STA_NAME_MAP = array(
		PRO_STA_Failed => '已结束，'. TUANGOU_STR . '失败',
		PRO_STA_Normal => '进行中，未成团',
		PRO_STA_Success => '进行中，已成团',
		PRO_STA_Finish => '已结束，'. TUANGOU_STR . '成功',
		PRO_STA_Refund => '已结束，已经返款'
		);
		return $STA_NAME_MAP[$STA_Code];
	}

	public function reSort($productList)
	{
		foreach ($productList as $i => $product)
		{
			if ($product['surplus'] < 0 && $product['order'] > 0)
			{
				logic('product')->Update($product['id'], array('order'=>0));
			}
		}
	}

	public function ClearDraft($pID, $dID, $exceptPID = false)
	{
		if ($pID)
		{
			$sql_filter = '1';
			$exceptPID && $sql_filter = 'id<>'.$exceptPID;
			$whereSQL = 'saveHandler="draft" AND draft='.$pID.' AND '.$sql_filter;
			$affCount = dbc(DBCMax)->delete('product')->where($whereSQL)->done();
			zlog('product')->draftClear($whereSQL, $affCount);
		}
		if (post('draft-pro-id')) $dID = post('draft-pro-id', 'int');
		if ($dID > 0 && $dID != $pID)
		{
			meta('p_hs_'.$dID, null);
			meta('p_ir_'.$dID, null);
			meta('expresslist_of_'.$dID, null);
			meta('paymentlist_of_'.$dID, null);
			if ($pID == 0)
			{
				$whereSQL = 'saveHandler="draft" AND id='.$dID;
				$affCount = dbc(DBCMax)->delete('product')->where($whereSQL)->done();
				zlog('product')->draftClear($whereSQL, $affCount);
			}
		}
		logic('catalog')->ProUpdate();
	}

	public function allowCSaveHandler($pid, $newHandler)
	{
		if (!in_array($newHandler, array('normal','draft'))) return false;
		$product = logic('product')->SrcOne($pid);
		if (!in_array($product['saveHandler'], array('normal','draft'))) return false;
		if ($product['saveHandler'] == 'normal' && $newHandler == 'draft') return false;
		return true;
	}

	public function GetDraftCount()
	{
		$r = dbc(DBCMax)->select('product')->in('COUNT(1) AS DrfCount')->where('saveHandler="draft"')->limit(1)->done();
		return $r['DrfCount'];
	}

	public function GetDraftList()
	{
		return dbc(DBCMax)->select('product')->where('saveHandler="draft"')->done();
	}

	public function CheckProductDraft($pid)
	{
		return dbc(DBCMax)->select('product')->where('saveHandler="draft" AND draft='.$pid)->order('addtime.DESC')->limit(1)->done();
	}


	function productCheck($id,$city=''){		$id = (is_numeric($id) ? $id : 0);
	$now = time();
	if($city!=''){
		$sql='select * from '.TABLE_PREFIX.'tttuangou_product where begintime <= '.$now.' and overtime > '.$now.' and id = '.$id.' and (city = '.floatval($city).' or display = 2)';
	}else{
		$sql='select * from '.TABLE_PREFIX.'tttuangou_product where begintime <= '.$now.' and overtime > '.$now.' and id = '.$id;
	}
	$query = dbc()->Query($sql);
	if (!$query)
	{
		return false;
	}
	$product=$query->GetRow();
	$product['price'] *= 1;
	$product['nowprice'] *= 1;
	return $product;
	}
	function AddSellerProNum($sellerid){
		$sql='update '.TABLE_PREFIX.'tttuangou_seller set productnum = productnum + 1 where id = '.floatval($sellerid);
		$query = dbc()->Query($sql);
		return true;
	}
	function DelSellerProNum($sellerid){
		$sql='update '.TABLE_PREFIX.'tttuangou_seller set productnum = productnum - 1 where id = '.floatval($sellerid);
		$query = dbc()->Query($sql);
		return true;
	}
	function AddSellerSucNum($sellerid){
		$sql='update '.TABLE_PREFIX.'tttuangou_seller set successnum = successnum + 1 where `is_countdown`=0 AND id = '.floatval($sellerid);
		$query = dbc()->Query($sql);
		return true;
	}
	function AddSellerTotMoney($sellerid,$money){
		$sql='update '.TABLE_PREFIX.'tttuangou_seller set money = money + '.$money.' where id = '.floatval($sellerid);
		$query = dbc()->Query($sql);
	}
	function delSellerTotMoney($sellerid,$money){
		$sql='update '.TABLE_PREFIX.'tttuangou_seller set money = money - '.$money.' where id = '.floatval($sellerid);
		$query = dbc()->Query($sql);
	}
	function GetUserSellerProduct($uid){
		$pids = array();
		$sinfo = dbc(DBCMax)->query('select id from '.table('seller')." where userid='".$uid."'")->limit(1)->done();
		$sql = "SELECT id FROM ".table('product')." WHERE sellerid ='".$sinfo['id']."'";
		$product = dbc(DBCMax)->query($sql)->done();
		if($product){
			foreach($product as $key => $val){
				$pids[] = $val['id'];
			}
		}
		return $pids;
	}

	function GetOwnerLink($sellerid){
		$sql = "SELECT `id`,`name` FROM `".table('product')."` WHERE `display`>0 AND `saveHandler`='normal' AND `linkid`=0 AND `sellerid`='".$sellerid."'";
		$data = dbc(DBCMax)->query($sql)->done();
		return $data;
	}
	public function get_link_product($linkid){
		$data = array();
		if($linkid > 0){
			$sql = 'SELECT * FROM `'.table('product_link').'` WHERE `id`='.$linkid;
			$data = dbc(DBCMax)->query($sql)->limit(1)->done();
			if($data){
				$data = $this->_format_link_data($data);
			}
		}
		return $data;
	}
	private function _format_link_data($data){
		$pids = array();
		$linkproduct = unserialize($data['link_product']);
		foreach($linkproduct as $k => $v){
			$pids[] = $v['pid'];
		}
		$productnames = dbc(DBCMax)->query('SELECT id,name FROM `'.table('product').'` WHERE `id` IN('.implode(',',$pids).')')->done();
		foreach($linkproduct as $lk => $lv){
			foreach($productnames as $pk => $pv){
				if($lv['pid'] == $pv['id']){
					$linkproduct[$lk]['product_name'] = $pv['name'];
				}
			}
		}
		$data['products'] = $linkproduct;
		$data['pids'] = $pids;
		return $data;
	}
	public function get_link_list($sellerid = 0){
		$data = array();
		if($sellerid > 0){
			$sql = 'SELECT * FROM `'.table('product_link').'` WHERE `sellerid`='.$sellerid;
		}else{
			$sql = 'SELECT * FROM `'.table('product_link').'`';
		}
		$sql = page_moyo($sql);
		$data = dbc(DBCMax)->query($sql)->done();
		if($data){
			foreach($data as $key => $val){
				$data[$key] = $this->_format_link_data($val);
			}
		}
		return $data;
	}
	public function linksave($sellerid = 0, $data = array()) {
		if($sellerid > 0 && $data && is_array($data) && count($data) > 1){
			$pids = array();
			foreach($data as $k => $v){
				$pids[] = $v['pid'];
			}
			$ldata = array(
				'sellerid' => $sellerid,
				'link_product' => serialize($data)
			);
			$rid = dbc(DBCMax)->insert('product_link')->data($ldata)->done();
			if($rid > 0){
				dbc(DBCMax)->update('product')->data(array('linkid'=>$rid))->where('id IN('.implode(',',$pids).')')->done();
			}
		}
	}
	public function deletelink($id = 0) {
		if($id > 0 && $this->check_link_byid($id)) {
			dbc(DBCMax)->delete('product_link')->where(array('id'=>$id))->limit(1)->done();
			dbc(DBCMax)->update('product')->data(array('linkid'=>'0'))->where(array('linkid'=>$id))->done();
		}
	}
	public function check_link_byid($id = 0){
		$return = false;
		if($id > 0){
			$linkinfo = $this->get_link_product($id);
			if($linkinfo){
				if(MEMBER_ROLE_TYPE == 'seller'){
					$sellerid = logic('seller')->U2SID(MEMBER_ID);
					if($sellerid == $linkinfo['sellerid']){
						$return = true;
					}
				}elseif(MEMBER_ROLE_TYPE == 'admin'){
					$return = true;
				}
			}
		}
		return $return;
	}
	public function updatelink($id = 0, $data = array()) {
		if($id > 0 && $data && is_array($data) && count($data) > 1 && $this->check_link_byid($id)){
			$pids = array();
			foreach($data as $k => $v){
				$pids[] = $v['pid'];
			}
			dbc(DBCMax)->update('product_link')->data(array('link_product'=>serialize($data)))->where(array('id'=>$id))->done();
			dbc(DBCMax)->update('product')->data(array('linkid'=>'0'))->where(array('linkid'=>$id))->done();
			dbc(DBCMax)->update('product')->data(array('linkid'=>$id))->where('id IN('.implode(',',$pids).')')->done();
		}
	}
}
?>