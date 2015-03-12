<?php

/**
 * 模块：商家后台
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package module
 * @name seller.mod.php
 * @version 1.0
 */

class ModuleObject extends MasterObject
{
	private $uid = 0;
	private $sid = 0;
	
	private function iniz()
	{
		$this->uid = user()->get('id');
		if ($this->uid < 0)
		{
			$this->Messager('请先登录！', '?mod=account&code=login');
		}
		$this->sid = logic('seller')->U2SID($this->uid);
		if ($this->sid < 0)
		{
			if($this->uid == 1){
				$this->Messager('请您先去后台，添加自己的商家信息！', 0);
			}else{
				$this->Messager('您不是商家，无法查看商家后台！', 0);
			}
		}else{
			$sellerinfo = dbc(DBCMax)->query('select * from '.table('seller').' where id='.$this->sid)->limit(1)->done();
			if($sellerinfo['enabled']=='false'){
				$this->Messager('您的商家身份未通过审核！', 'index.php');
			}
		}
	}
	function ModuleObject( $config )
	{
		$this->MasterObject($config);
		$runCode = Load::moduleCode($this);
		
		
		global $rewriteHandler;
		$rewriteHandler = null;
		
		$this->$runCode();
	}
	public function main()
	{
		$catalog = get('catalog');
		$seller = logic('seller')->GetList(logic('misc')->City('id'), ($catalog ? logic('catalog')->Filter($catalog, 'seller') : '1'));
		$this->Title = "商家列表";
		include handler('template')->file('seller');
	}
	public function view()
	{
		$id = get('id', 'int');
		$seller = logic('seller')->GetOne($id);
		$this->Title = $seller['sellername'];
		$commentdata = logic('comment')->front_get_seller_comments($id);
		include handler('template')->file('seller_view');
	}
	public function manage()
	{
						$this->product_list();
	}
	
	public function product_list()
	{
		$this->iniz();
		$filter = 'p.sellerid='.$this->sid;
		if(isset($_GET['prosta'])){
			$prosta = get('prosta', 'int');
			is_numeric($prosta) && $filter .= ' AND p.status='.$prosta;
		}
		if(isset($_GET['prodsp'])){
			$prodsp = get('prodsp', 'int');
			is_numeric($prodsp) && $filter .= ' AND p.display='.$prodsp;
		}
		$products = logic('product')->GetList(-1, null, $filter);
		logic('seller')->AVParser($products);
		$seller_info = logic('seller')->GetOne(null,MEMBER_ID);
		$money = $seller_info['money'];
		$total_money = $seller_info['total_money'];
		$account_money = $seller_info['account_money'];
		$forbid_money = $seller_info['forbid_money'];
		$this->Title = '我的产品列表';
		include handler('template')->file('seller_product_list');
	}

	
	public function comment_list()
	{
		$this->iniz();
		$seller_info = logic('seller')->GetOne(null,MEMBER_ID);
		$money = $seller_info['money'];
		$total_money = $seller_info['total_money'];
		$account_money = $seller_info['account_money'];
		$forbid_money = $seller_info['forbid_money'];
		$comments = logic('comment')->front_get_seller_comments($this->sid);
		$this->Title = '用户对我的评价';
		include handler('template')->file('seller_comment_list');
	}

	
	public function ticket_list()
	{
		$this->iniz();
		if(isset($_GET['coupsta'])){
			$coupSTA = get('coupsta', 'int');
		}else{
			$coupSTA = TICK_STA_ANY;
		}
		$fpids = '';
		$pids = logic('product')->GetUserSellerProduct(MEMBER_ID);
		$fpids = 0;
		if($pids){
			$fpids = implode(',',$pids);
		}
        $tickets = logic('coupon')->GetList(USR_ANY, ORD_ID_ANY, $coupSTA, false, $fpids);
		$seller_info = logic('seller')->GetOne(null,MEMBER_ID);
		$money = $seller_info['money'];
		$total_money = $seller_info['total_money'];
		$account_money = $seller_info['account_money'];
		$forbid_money = $seller_info['forbid_money'];
		$this->Title = '团购券列表';
		include handler('template')->file('seller_product_ticket');
	}
	
	public function order_list()
	{
		$this->iniz();
		if(isset($_GET['ordsta'])){
			$ordSTA = get('ordsta', 'int');
		}else{
			$ordSTA = ORD_STA_ANY;
		}
		$ordPROC = get('ordproc', 'string');
		if ($ordPROC == '__PAY_YET__') {
			$ordPROC = 'pay > 0 and paytime > 0';
		}elseif($ordPROC == 'WAIT_BUYER_PAY'){
			$ordPROC = 'pay = 0 and paytime = 0';
		}else{
			$ordPROC = $ordPROC ? ('process="'.$ordPROC.'"') : '1';
		}
		$pids = logic('product')->GetUserSellerProduct(MEMBER_ID);
		$asql = 0;
		if($pids){
			$asql = implode(',',$pids);
		}
		$ordPROC .=  ' AND productid IN('.$asql.')';
		$orders = logic('order')->GetList(0, $ordSTA, ORD_PAID_ANY, $ordPROC);
		$seller_info = logic('seller')->GetOne(null,MEMBER_ID);
		$money = $seller_info['money'];
		$total_money = $seller_info['total_money'];
		$account_money = $seller_info['account_money'];
		$forbid_money = $seller_info['forbid_money'];
		$this->Title = '订单列表';
		include handler('template')->file('seller_product_order');
	}
	
	public function delivery_list()
	{
		$this->iniz();
		$ordPROC = get('ordproc', 'string');
		$dlvPROC = $ordPROC ? ('o.process="'.$ordPROC.'"') : '1';
		$pids = logic('product')->GetUserSellerProduct(MEMBER_ID);
		$asql = 0;
		if($pids){
			$asql = implode(',',$pids);
		}
		$dlvPROC .= ' AND o.productid IN('.$asql.') ';
        $deliveries = logic('delivery')->GetList($ordPROC,$dlvPROC);
		$seller_info = logic('seller')->GetOne(null,MEMBER_ID);
		$money = $seller_info['money'];
		$total_money = $seller_info['total_money'];
		$account_money = $seller_info['account_money'];
		$forbid_money = $seller_info['forbid_money'];
		$this->Title = '发货单列表';
		include handler('template')->file('seller_product_delivery');
	}
	
	public function delivery_single()
	{
		$this->iniz();
		$order = logic('order')->SrcOne(get('oid', 'number'));
		if ($order)
		{
			$product = logic('product')->SrcOne($order['productid']);
			if ($product['sellerid'] == $this->sid)
			{
				if(strlen(get('no','txt')) > 8){
					logic('delivery')->Invoice(get('oid', 'number'), get('no', 'txt')) && exit('ok');
				}else{
					exit('error');
				}
			}
		}
		exit('error');
	}
	
	public function ajax_alert()
	{
		$this->iniz();
		$id = get('id', 'int');
		$c = logic('coupon')->GetOne($id);
		logic('notify')->Call($c['uid'], 'admin.mod.coupon.Alert', $c);
		exit('ok');
	}
}

?>