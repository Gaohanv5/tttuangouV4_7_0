<?php

/**
 * 模块：数据导出
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package module
 * @name export.mod.php
 * @version 1.0
 */

class ModuleObject extends MasterObject
{
	function ModuleObject( $config )
	{
		$this->MasterObject($config);
		$runCode = Load::moduleCode($this);
		$this->$runCode();
	}
	function Main()
	{
		exit('Modules.export.index');
	}
	function Order()
	{
		$this->CheckAdminPrivs('ordermanage');
		$this->selector('order');
	}
	function Order_generate()
	{
		$this->CheckAdminPrivs('ordermanage');
		$format = $this->__set_filter('order');
				$ordSTA = get('ordsta', 'number');
		is_numeric($ordSTA) || $ordSTA = ORD_STA_ANY;
		$ordPROC = get('ordproc', 'string');
		$ordPROC = $ordPROC ? ('process="'.$ordPROC.'"') : '1';
		if(MEMBER_ROLE_TYPE == 'seller'){
			$pids = logic('product')->GetUserSellerProduct(MEMBER_ID);
			$asql = 0;
			if($pids){
				$asql = implode(',',$pids);
			}
			$ordPROC .=  ' AND productid IN('.$asql.')';
		}
		$list = logic('order')->GetList(0, $ordSTA, ORD_PAID_ANY, $ordPROC);
				include handler('template')->file('@export/order.'.$format);
		$this->doResult('order', $format);
	}
	function Coupon()
	{
		$this->CheckAdminPrivs('coupon');
		$this->selector('coupon');
	}
	function Coupon_generate()
	{
		$this->CheckAdminPrivs('coupon');
		$format = $this->__set_filter('coupon');
				$coupSTA = get('coupsta', 'number');
		is_numeric($coupSTA) || $coupSTA = TICK_STA_ANY;
		$fpids = '';
		if(MEMBER_ROLE_TYPE == 'seller'){
			$pids = logic('product')->GetUserSellerProduct(MEMBER_ID);
			$fpids = 0;
			if($pids){
				$fpids = implode(',',$pids);
			}
		}
        $list = logic('coupon')->GetList(USR_ANY, ORD_ID_ANY, $coupSTA, false, $fpids);
				include handler('template')->file('@export/coupon.'.$format);
		$this->doResult('coupon', $format);
	}
	function Delivery()
	{
		$this->CheckAdminPrivs('delivery');
		$this->selector('delivery');
	}
	function Delivery_generate()
	{
		$this->CheckAdminPrivs('delivery');
		$format = $this->__set_filter('delivery');
				$alsend = get('alsend', 'txt');
		$alsend = ($alsend == 'yes') ? DELIV_SEND_Yes : (($alsend == 'no') ? DELIV_SEND_No : DELIV_SEND_OK);
		$list = logic('delivery')->GetList($alsend);
				include handler('template')->file('@export/delivery.'.$format);
		$this->doResult('delivery', $format);
	}
	function Seller()
	{
		$this->CheckAdminPrivs('seller');
		$this->selector('seller');
	}
	function Seller_generate()
	{
		$this->CheckAdminPrivs('seller');
		$format = $this->__set_filter('seller');
		$area = get('area', 'int');
		is_numeric($area) || $area = -1;
		$keyword = get('keyword', 'txt');
		$keyword = $keyword ? "sellername like '%".$keyword."%'" : "1";
		$_GET['page'] = get('page', 'int');
		$list = logic('seller')->GetList($area, $keyword);
		include handler('template')->file('@export/seller.'.$format);
		$this->doResult('seller', $format);
	}
	function Countproduct()
	{
		$this->CheckAdminPrivs('salecount');
		$this->selector('countproduct');
	}
	function Countproduct_generate()
	{
		$this->CheckAdminPrivs('salecount');
		$format = $this->__set_filter('countproduct');
		$list = logic('salecount')->count_product();
		$ltime = $_GET['iscp_tvbegin_salecount_product'].' '.$_GET['iscp_tvfinish_salecount_product'];
		include handler('template')->file('@export/countproduct.'.$format);
		$this->doResult('countproduct', $format);
	}
	function Countpayment()
	{
		$this->CheckAdminPrivs('salecount');
		$this->selector('countpayment');
	}
	function Countpayment_generate()
	{
		$this->CheckAdminPrivs('salecount');
		$format = $this->__set_filter('countpayment');
		$list = logic('salecount')->count_payment();
		$ltime = $_GET['iscp_tvbegin_salecount_payment'].' '.$_GET['iscp_tvfinish_salecount_payment'];
		include handler('template')->file('@export/countpayment.'.$format);
		$this->doResult('countpayment', $format);
	}
	function Countuser()
	{
		$this->CheckAdminPrivs('salecount');
		$this->selector('countuser');
	}
	function Countuser_generate()
	{
		$this->CheckAdminPrivs('salecount');
		$format = $this->__set_filter('countuser');
		$list = logic('salecount')->count_user();
		$ltime = $_GET['iscp_tvbegin_salecount_user'].' '.$_GET['iscp_tvfinish_salecount_user'];
		include handler('template')->file('@export/countuser.'.$format);
		$this->doResult('countuser', $format);
	}
	function Countfund()
	{
		$this->CheckAdminPrivs('salecount');
		$this->selector('countfund');
	}
	function Countfund_generate()
	{
		$this->CheckAdminPrivs('salecount');
		$format = $this->__set_filter('countfund');
		$list = logic('salecount')->count_fund();
		$ltime = $_GET['iscp_tvbegin_salecount_fund'].' '.$_GET['iscp_tvfinish_salecount_fund'];
		include handler('template')->file('@export/countfund.'.$format);
		$this->doResult('countfund', $format);
	}
	function Subscribe()
	{
		$this->CheckAdminPrivs('subscribe');
		$this->selector('subscribe');
	}
	function Subscribe_generate()
	{
		$this->CheckAdminPrivs('subscribe');
		$format = $this->__set_filter('subscribe');
				$class = get('class', 'txt');
		$class = $class ? $class : 'mail';
        $list = logic('subscribe')->GetList($class);
				include handler('template')->file('@export/subscribe.'.$format);
		$this->doResult('subscribe', $format);
	}
	private function selector($class)
	{
		$action = $class;
		$filter = $this->__get_filter();
		include handler('template')->file('@admin/export_selector');
	}
	private function doResult($class, $format)
	{
		$export = ob_get_contents();
		$file = $this->__write_cache($class, $format, $export);
		header('Location: ?mod=export&code=result&file='.$file);
		exit;
	}
	public function result()
	{
		$file = get('file');
		$ops = array(
			'name' => $file,
			'url' => ini('settings.site_url').'/cache/export/'.$file
		);
		exit(jsonEncode($ops));
	}
	private function __write_cache($class, $format, $content)
	{
		$dir = CACHE_PATH.'/export/';
		if (!is_dir($dir))
		{
			@tmkdir($dir);
		}
		$file = $class.'_'.date('YmdHis').'.'.$format;
		file_put_contents($dir.$file, ENC_IS_GBK ? $content : ENC_U2G($content));
		return $file;
	}
	private function __get_filter()
	{
		$url = urldecode(get('referrer'));
				$params = explode('&', $url);
		$_PARMS = array();
		foreach ($params as $query)
		{
			list($key, $val) = explode('=', $query);
			if ($key == 'mod' || $key == 'code')
			{
				continue;
			}
			$_PARMS[$key] = $val;
		}
		$filter = base64_encode(serialize($_PARMS));
		return $filter;
	}
	private function __set_filter($class)
	{
		$geneall = get('geneall', 'txt');
		$filter = unserialize(base64_decode(get('filter')));
		$_GET = array_merge($_GET, $filter);
				if(strstr($class,'count')){
			$_GET['mod'] = 'salecount';
			$_GET['code'] = str_replace('count','',$class);
		}else{
			$_GET['mod'] = $class;
			$_GET['code'] = 'vlist';
		}
		
		if ($geneall == 'yes')
		{
						$_GET[EXPORT_GENEALL_FLAG] = EXPORT_GENEALL_VALUE;
		}
		return get('format', 'txt');
	}
}


?>