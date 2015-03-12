<?php

/**
 * 模块：报表管理
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package module
 * @name reports.mod.php
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
	public function main()
	{
		$this->CheckAdminPrivs('reports');
		include handler('template')->file('@admin/reports_main');
	}
	public function view()
	{
		$this->CheckAdminPrivs('reports');
		$vs = $service = get('service', 'string');
		$hoster = get('hoster', 'int');
		$hoster && $vs = array($service, $hoster);
		$is_newest = logic('reports')->is_newest($vs);
		if (!$is_newest)
		{
			$dleft = logic('reports')->datelines_left($vs);
			if (!$dleft)
			{
				$this->Messager('找不到相应的交易数据，暂时无法生成报表！');
			}
		}
		include handler('template')->file('@admin/reports_view');
	}
	public function queryuserid()
	{
		$this->CheckAdminPrivs('reports','ajax');
		$username = get('username', 'string');
		$acc = account()->Search('name', $username, 1);
		if ($acc)
		{
			exit($acc['uid'].'');
		}
		else
		{
			exit('0');
		}
	}
	public function datelines()
	{
		$this->CheckAdminPrivs('reports','ajax');
		$service = get('service', 'string');
		$hoster = get('hoster', 'int');
		$hoster && $service = array($service, $hoster);
		$datelines = logic('reports')->datelines_left($service);
		exit(jsonEncode($datelines));
	}
	public function run()
	{
		$this->CheckAdminPrivs('reports','ajax');
		$service = get('service', 'string');
		$hoster = get('hoster', 'int');
		$hoster && $service = array($service, $hoster);
		$dateline = get('dateline', 'int');
		$ms_ts = microtime(true);
		$r = logic('reports')->generate($service, $dateline);
		$ms_tf = microtime(true);
		exit(jsonEncode(array('ms' => round($ms_tf - $ms_ts, 3) * 1000)));
	}
	
	public function channel()
	{
		$this->CheckAdminPrivs('reports','ajax');
		$service = get('service', 'string');
		$hoster = get('hoster', 'int');
		$hoster && $service = array($service, $hoster);
		$channels = logic('reports')->get_channels($service);
		exit(jsonEncode($channels));
	}
	public function data()
	{
		$this->CheckAdminPrivs('reports','ajax');
		$service = get('service', 'string');
		$hoster = get('hoster', 'int');
		$hoster && $service = array($service, $hoster);
		$date_begin = strtotime(get('begin', 'string'));
		$date_begin || $date_begin = strtotime(date('Y-m-d', time() - 86400 * 7));
		$date_finish = strtotime(get('finish', 'string'));
		$date_finish || $date_finish = strtotime(date('Y-m-d', time() - 86400));
		$channel = get('channel', 'string');
		if ($channel)
		{
			exit(jsonEncode(logic('reports')->get_data($service, $channel, $date_begin, $date_finish)));
		}
		else
		{
			exit('error');
		}
	}
}

?>