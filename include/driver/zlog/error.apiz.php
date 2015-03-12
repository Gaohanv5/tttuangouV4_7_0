<?php

/**
 * ZLOG-APIZ：错误报告
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package zlog
 * @name error.apiz.php
 * @version 1.1
 */

class errorZLOG extends iMasterZLOG
{
	protected $zlogType = 'error';
	private $mysqlErrorMax = 5;
	private $mysqlErrorCNT = 0;
	public function found($type, $detail = null)
	{
				if (!logic('misc')->siteInstalled())
		{
			return 'SITE_NOT_INSTALLED';
		}
				if ($type == 'mysql')
		{
			$this->mysqlErrorCNT ++ ;
			if ($this->mysqlErrorCNT >= $this->mysqlErrorMax)
			{
				$this->mysqlErrorCNT = 0;
				return 'TOO_MANY_ERROR';
			}
		}
		$nameMAP = array(
			'mysql' => '数据库执行错误',
			'error.msockopen' => 'msockopen函数报错',
			'file.missing' => '系统缺失某些文件，执行中断',
			'missing.gzopen' => '服务器不支持gzopen函数，文件解压失败',
			'denied.io' => '文件读写失败，请检查权限',
			'missing.object' => '系统无法解析对象，执行中断',
			'queue' => '任务队列执行出错'
		);
		if ($detail == null)
		{
			$lastError = function_exists('error_get_last') ? error_get_last() : array();
			if ($lastError['message'])
			{
				$detail = $lastError['message'];
			}
			else
			{
				$detail = '';
			}
		}
		$wName = isset($nameMAP[$type]) ? $nameMAP[$type] : ('未知错误类型：'.$type);
		$btString = '';
		$btAll = function_exists('debug_backtrace') ? debug_backtrace() : false;
		if ($btAll)
		{
			$btLength = count($btAll);
			$btLength > 7 && $btLength = 7;
			$btIII = 0;
			for ($btI = $btLength; $btI > 0; $btI--)
			{
				$btOne = $btAll[$btI-1];
				$btIII ++;
				$btString .= $btIII.'. FILE:'.basename($btOne['file']).' - LINE:'.$btOne['line'].' - FUNC:'.$btOne['function'].'<br/>';
			}
			$btString = '<div class="btString">'.$btString.'</div>';
		}
		$this->zlogCreate($type, $wName, $detail.$btString);
	}
}

?>