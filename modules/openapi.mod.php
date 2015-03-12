<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name openapi.mod.php
 * @date 2014-09-01 17:24:23
 */
 


class ModuleObject extends MasterObject
{
	var $Config = array();
	var $ProductLogic = null;
	function ModuleObject(& $config)
	{
		$this->MasterObject($config);
		Load::logic('product');
		$this->ProductLogic = new ProductLogic();
		$this->Execute();
	}
	function Execute()
	{
		if ($this->Code == '')
		{
			$this->Code = 'main';
		}
		$this -> config=ConfigHandler::get('product');
		list($this->cityary,$this->city,$this->cityname)=logic('misc')->City();
		if ('main' == $this->Code)
		{
			ob_start();
			$this->UrlList();
			$body = ob_get_clean();
			$this->ShowBody($body);
		}
		else
		{
			$this->RssOutput();
		}
	}
	function UrlList()
	{
		$supportList = array
		(
			'index' => array(
				'title' => $this->Config['site_name'],
				'submit' => ''
			),
			'jutao' => array(
				'title' => '聚淘网',
				'submit' => 'http:/'.'/www.jutao.com/about/addsite.jsp'
			),
			'2345' => array(
				'title' => '2345团购导航',
				'submit' => 'http:/'.'/bbs.2345.com/tgAPI/api.php'
			),
			'baidu' => array(
				'title' => '百度/hao123团购导航',
				'submit' => 'http:/'.'/www.hao123.com/redian/api.htm'
			),
			'ganji' => array(
				'title' => '赶集网团购导航',
				'submit' => 'http:/'.'/hz.ganji.com/tuan/api.php'
			),
			'sohu' => array(
				'title' => '搜狐团购导航',
				'submit' => 'http:/'.'/t123.sohu.com/site.html'
			),
			'soso' => array(
				'title' => '搜搜团购导航',
				'submit' => 'http:/'.'/tuan.soso.com/help/group_help_siteSubmit.html'
			),
			'tuan800' => array(
				'title' => '团800团购网',
				'submit' => 'http:/'.'/www.tuan800.com/open_api'
			),
			'tuanp' => array(
				'title' => '街市网',
				'submit' => 'http:/'.'/www.jieshi.com/site/api'
			)
		);
				global $rewriteHandler;
		include_once INCLUDE_PATH.'rewrite.php';
		$url_pre = '/?mod=openapi&code=';
		if ($rewriteHandler)
		{
			$url_pre = $rewriteHandler->formatURL($url_pre);
		}
		$this->Title = '开放API';
		include($this->TemplateHandler->Template('tttuangou_rss'));
	}
	function RssOutput()
	{
		$allowList = array
		(
			'index','360','2345','baidu','ganji','sohu','soso','tuan800','tuanp', 'jutao'
		);
		if (!in_array($this->Code, $allowList))
		{
			echo 'Action not allowed';
			return;
		}
				require 'rss/Output.class.php';
				$productList = logic('product')->GetList(-1, PRO_ACV_Yes);
		if (count($productList)<1)
		{
			echo 'Null of Database';
			return;
		}
		$cityList_nf = logic('misc')->CityList();
		foreach ($cityList_nf as $i => $one)
		{
			$cityList[$one['cityid']] = $one['cityname'];
		}
				include 'rss/'.$this->Code.'.php';
	}
}

?>