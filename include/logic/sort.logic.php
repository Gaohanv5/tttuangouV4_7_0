<?php

/**
 * 逻辑区：排序管理
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package logic
 * @name sort.logic.php
 * @version 1.0
 */

class SortManageLogic
{
	
	private $filterPools = array(
		'default' => array(
			'name' => '默认',
			'sql' => 'p.order DESC, p.id DESC'
		),
		'recent' => array(
			'name' => '最新',
			'title' => '按照上架时间显示最新',
			'dir' => 'down',
			'sql' => 'p.begintime DESC'
		),
		'sells' => array(
			'name' => '销量',
			'title' => '按照产品销量显示热卖产品',
			'dir' => 'down',
			'sql' => 'p.sells_count+p.virtualnum DESC'
		),
		'price-asc' => array(
			'name' => '价格(最低)',
			'title' => '按照价格从低到高显示',
			'dir' => 'up',
			'sql' => 'p.nowprice ASC'
		),
		'price-desc' => array(
			'name' => '价格(最高)',
			'title' => '按照价格从高到低显示',
			'dir' => 'down',
			'sql' => 'p.nowprice DESC'
		),
	);
	public $sellerPools = array(
		'default' => array(
			'name' => '默认',
			'title' => '按照系统默认排序',
			'dir' => 'down',
			'sql' => ' s.display_order DESC, s.id DESC '
		),
		'recent' => array(
			'name' => '最新',
			'title' => '按照添加时间显示最新',
			'dir' => 'down',
			'sql' => ' s.id DESC '
		),
		'sells' => array(
			'name' => '销量',
			'title' => '按照产品销量显示商家',
			'dir' => 'down',
			'sql' => ' s.successnum DESC, s.id DESC ',
		),
		'price-asc' => array(
			'name' => '人均(最低)',
			'title' => '按照人均消费从低到高显示',
			'dir' => 'up',
			'sql' => ' s.price_avg ASC, s.id DESC '
		),
		'price-desc' => array(
			'name' => '人均(最高)',
			'title' => '按照人均消费从高到低显示',
			'dir' => 'down',
			'sql' => ' s.price_avg DESC, s.id DESC '
		),
	);
	private $product_navigate_init = false;
	
	public function filter_pools()
	{
		return $this->filterPools;
	}
	
	public function set_filter_pool($key, $config)
	{
		$this->filterPools[$key] = $config;
	}
	
	public function product_navigate($reinit = false)
	{
		if(false == $reinit && true == $this->product_navigate_init) {
			return ;
		}		
		$this->product_navigate_init = true;
		
		$sortKey = $this->get_sort_key();
		$sorts = $this->filterPools;
		foreach ($sorts as $k => $data)
		{
			$sorts[$k]['url'] = logic('url')->create('product', array('sort' => $k));
			if ($sortKey == $k)
			{
				$sorts[$k]['selected'] = true;
			}
		}
		include handler('template')->file('home_sort_navigate');
	}
	
	public function product_sql_filter()
	{
		return $this->filterPools[$this->get_sort_key()]['sql'];
	}
	
	private function get_sort_key()
	{
		$sk = get('sort', 'string');
		return isset($this->filterPools[$sk]) ? $sk : 'default';
	}

	
	public function set_seller_pool($key, $config)
	{
		$this->sellerPools[$key] = $config;
	}	
	private function seller_sort_key()
	{
		$sk = get('sort', 'string');
		return (isset($this->sellerPools[$sk]) ? $sk : 'default');
	}
	public function seller_sql_filter()
	{		
		return $this->sellerPools[$this->seller_sort_key()]['sql'];
	}
	public function seller_navigate()
	{
		$sortKey = $this->seller_sort_key();
		$sorts = $this->sellerPools;
		foreach ($sorts as $k => $data)
		{
			$sorts[$k]['url'] = logic('url')->create('seller', array('sort' => $k));
			if ($sortKey == $k)
			{
				$sorts[$k]['selected'] = true;
			}
		}
		include handler('template')->file('seller_sort_navigate');
	}
}

?>