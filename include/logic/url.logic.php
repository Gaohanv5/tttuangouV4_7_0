<?php

/**
 * 逻辑区：URL地址管理
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package logic
 * @name url.logic.php
 * @version 1.0
 */

class UrlManageLogic
{
	private $cfgs = array(
		'product' => array(
			'collects' => array(
				'region', 'street', 'tag', 'kw', 'sort'
			),
			'redirect' => array(
				'mod:catalog' => array(
					'mod' => '@mod',
					'code' => '@code'
				)
			)
		),
		'catalog' => array(
			'base' => array(
				'mod' => 'catalog'
			),
			'collects' => array(
				'code', 'region', 'street', 'kw', 'sort'
			)
		),
		'seller' => array(
			'collects' => array(
				'catalog', 'region', 'street', 'price', 'kw', 'sort'
			),
			'redirect' => array(
				'mod:seller' => array(
					'mod' => '@mod',
					'code' => '@code'
				)
			)
		),
	);
	
	public function create($cat, $params)
	{
		$params_sys = $this->get_params($cat);
		$params_all = array_merge($params_sys, $params);
		if ($params_all)
		{
			$url = 'index.php?';
			foreach ($params_all as $k => $v)
			{
				is_null($v) || $url .= $k.'='.urlencode($v).'&';
			}
			$url = substr($url, 0, -1);
			return rewrite($url);
		}
		else
		{
			return rewrite('index.php');
		}
	}
	
	private function get_params($cat)
	{
		$return = array();
		static $caches = array();
		if (isset($caches[$cat]))
		{
			$return = $caches[$cat];
		}
		else
		{
			if (isset($this->cfgs[$cat]))
			{
				$cfg = $this->cfgs[$cat];
				$url = 'index.php?';
				if (isset($cfg['redirect']))
				{
					foreach ($cfg['redirect'] as $exp => $bases)
					{
						list($gK, $gV) = explode(':', $exp);
						if ($_GET[$gK] == $gV)
						{
							foreach ($bases as $mK => $mV)
							{
								if (substr($mV, 0, 1) == '@')
								{
									$mV = $_GET[substr($mV, 1)];
								}
								$cfg['base'][$mK] = $mV;
							}
						}
					}
				}
				$all = array();
				foreach ((array)$cfg['base'] as $k => $v)
				{
					$all[$k] = $v;
				}
				foreach ($cfg['collects'] as $k)
				{
					if (isset($_GET[$k]))
					{
						$all[$k] = $_GET[$k];
					}
				}
				$return = $caches[$cat] = $all;
			}
			else
			{
				$return = $caches[$cat] = array();
			}
		}
		return $return;
	}
}

?>