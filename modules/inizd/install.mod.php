<?php

/**
 * 模块：程序安装
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package module
 * @name install.mod.php
 * @version 1.0
 */

class ModuleObject extends MasterObject
{
	public function __construct($config)
	{
		if (is_file(DATA_PATH.'install.lock'))
		{
			return $this->Alert('您已安装，如需重新安装请先删除 '.DATA_PATH.' 目录下的install.lock文件！');
		}
		if (true == in_array(ini('settings.site_domain'), array('localx.uuland.org', 'dev.tttuangou.net', )))
		{
						ini('settings.site_domain', $_SERVER['HTTP_HOST']);
			ini('settings.site_url', rtrim(thtmlspecialchars('http:/'.'/'.$_SERVER['HTTP_HOST'].preg_replace("/\/+/",'/',str_replace("\\",'/',dirname($_SERVER['PHP_SELF']))."/")),'/'));
		}
		$this->MasterObject($config);
		$runCode = Load::moduleCode($this);
		$this->$runCode();
	}
	public function Main()
	{
		include handler('template')->file('@inizd/install/welcome');
	}
	private function Alert($text)
	{
		include handler('template')->file('@inizd/alert');
	}
	public function Env()
	{
				$env = array();
		$env['os'] = array('val' => PHP_OS, 'sp' => true);
		$env['phpv'] = array('val' => PHP_VERSION, 'sp' => (PHP_VERSION > '5'));
		$_up_allow = intval(@ini_get('file_uploads'));
		$_up_max_size = @ini_get('upload_max_filesize');
		$env['upload'] = array('val' => ($_up_allow ? '允许/最大'.$_up_max_size : '不允许'), 'sp' => $_up_allow);
		if (function_exists('gd_info'))
		{
			$gdfunction = 'gd_info';
			$gd = $gdfunction();
			$gdv = $gd['GD Version'];
		}
		else
		{
			$gdv = '未知版本';
		}
		$env['gd'] = array('val' => $gdv, 'sp' => true);

		$_short_allow = @ini_get('short_open_tag');
		$env['short'] = array('val'=>($_short_allow ? '开启' : '未开启<br>请在php.ini中<br>设置 short_open_tag=On 启用'), 'sp'=>$_short_allow);

		$_free_space = intval(diskfreespace('.') / (1024 * 1024));
		if ($_free_space > 0)
		{
			$env['space'] = array('val' => $_free_space.'MB', 'sp' => ($_free_space > 10));
		}
		else
		{
			$env['space'] = array('val' => '未知空间大小', 'sp' => true);
		}
		$rwList = array(
			'setting/',
			'cache/',
			'cache/fcache/',
			'cache/templates/',
			'errorlog/',
			'data/',
			'data/upgrade/',
			'backup/',
			'backup/db/',
			'uploads/',
			'uploads/apks/',
			'uploads/images/',
			'templates/html/',
			'templates/widget/',
			'uc_client/data/cache/',
		);
		$fcList = array(
			'mysql_connect',
			'gethostbyname',
			'fsockopen',
			'msockopen',
			'file_get_contents',
			'file_put_contents'
		);
		$dir = $this->DirPermission($rwList);
		$file = $this->FilePermission('setting/');
		$permissions = array_merge($dir, $file);
		$function = $this->FunctionTest($fcList);
		include handler('template')->file('@inizd/install/env');
	}
	public function DBS()
	{
		include handler('template')->file('@inizd/install/dbs');
	}
	public function DBS_save()
	{
		if ($_SERVER['REQUEST_METHOD'] != 'POST')
		{
			return $this->Alert('请求方式无效！');
		}
		$db = post('db');
		$handler = @mysql_connect($db['host'], $db['username'], $db['password']);
		if (!$handler)
		{
			return $this->Alert('无法连接数据库服务器！');
		}
		if (!@mysql_select_db($db['name']))
		{
			$sql = 'CREATE DATABASE IF NOT EXISTS `'.$db['name'].'` DEFAULT CHARACTER SET '.str_replace('-', '', ini('settings.charset'));
			@mysql_query($sql);
			if (!@mysql_select_db($db['name']))
			{
				return $this->Alert('没有找到数据库，并且您的帐号没有权限创建新的数据库！');
			}
		}
		$version = mysql_get_server_info($handler);
		if ( $version < '5.0.1' )
		{
			return $this->Alert('安装天天团购最少需要“MySQL 5.0.1”及以上版本，您当前版本为：'.$version);
		}
				if (strstr($db['host'], ':'))
		{
			list($host, $port) = explode(':', $db['host']);
		}
		else
		{
			$host = $db['host'];
			$port = '3306';
		}
		ini('settings.db_host', $host);
		ini('settings.db_port', $port);
		ini('settings.db_user', $db['username']);
		ini('settings.db_pass', $db['password']);
		ini('settings.db_name', $db['name']);
		ini('settings.db_table_prefix', ($db['prefix'] != '') ? $db['prefix'] : 'tttuangou_'.$this->RandString(6).'_');
				header('Location: ?mod=install&code=config');
	}
	public function Config()
	{
		include handler('template')->file('@inizd/install/config');
	}
	public function Config_save()
	{
		$c = post('c');
		if (trim($c['username']) == '')
		{
			return $this->Alert('用户名不能为空！');
		}
		if ($c['password'] != $c['repassword'])
		{
			return $this->Alert('两次密码不一致！');
		}
		if ($c['password'] == '')
		{
			return $this->Alert('密码不能为空！');
		}
		if(strlen($c['password']) < 6) {
			return $this->Alert('密码最少6位，建议设置8位以上的字母和数字组合');
		}
		if ($c['email'] == '')
		{
			return $this->Alert('邮箱地址不能为空！');
		}
				ini('__install_config_temp', $c);
		header('Location: ?mod=install&code=install');
	}
	public function Install()
	{
		$test = ini('__install_config_temp.test');
		include handler('template')->file('@inizd/install/install');
	}
	public function Process_struct()
	{
		$this->RunSQL('struct');
		$this->RunSQL('data');
		$this->RunSQL('regions');
	}
	public function Process_admin()
	{
		$c = ini('__install_config_temp');
		$sql = file_get_contents(DATA_PATH.'install/admin.sql');
		$sql = preg_replace('/\{\$username\}/', $c['username'], $sql);
		$sql = str_replace('{$password}', md5($c['password']), $sql);
		$sql = str_replace('{$email}', $c['email'], $sql);
		$this->RunSQL($sql);
	}
	public function Process_setting()
	{
		$c = ini('__install_config_temp');
		ini('settings.site_name', $c['sitename']);
		ini('settings.site_admin_email', $c['email']);
		ini('settings.auth_key', $this->RandString(32));
		ini('settings.safe_key', $this->RandString(32));
		ini('settings.cookie_prefix', 'TTtuangou_'.$this->RandString(6).'_');
	}
	public function Process_test()
	{
		$this->RunSQL('test');
	}
	public function Process_clean()
	{
		@unlink(CONFIG_PATH.'__install_config_temp.php');
	}
	public function Process_ends()
	{
		file_put_contents(DATA_PATH.'install.lock', date('Y-m-d H:i:s', time()));
	}
	public function Process_lives()
	{
		$this->iLinks();
		$this->iLives();
	}
	private function DirPermission($list)
	{
		$return = array();
		foreach ($list as $i => $dir)
		{
			$result = array();
			$result['path'] = $dir;
			$path = ROOT_PATH.$dir;
			if (false == is_dir($path))
			{
				tmkdir($path);
			}
			$file = $path.'.tttg.dir.permission.test';
			if (!@file_put_contents($file, 'moyo'))
			{
				$result['rw'] = false;
			}
			else
			{
				if (@file_get_contents($file) != 'moyo')
				{
					$result['rw'] = false;
				}
				else
				{
					$result['rw'] = true;
					@unlink($file);
				}
			}
			$return[] = $result;
		}
		return $return;
	}
	private function FilePermission($dir)
	{
		$path = ROOT_PATH.$dir;
		$fp = opendir($path);
		$return = array();
		while (false != $file = readdir($fp))
		{
			if (substr($file, -4) == '.php')
			{
				$result = array(
					'path' => $dir.$file
				);
				if (@touch($path.$file))
				{
					$result['rw'] = true;
				}
				else
				{
					$result['rw'] = false;
				}
				$result['rw'] || $return[] = $result;
			}
		}
		return $return;
	}
	private function FunctionTest($list)
	{
		$return = array();
		foreach ($list as $i => $func)
		{
			if ($func == 'msockopen')
			{
				$return[] = array(
					'name' => $func,
					'sp' => msockopen() ? true : false
				);
			}
			else
			{
				$return[] = array(
					'name' => $func,
					'sp' => function_exists($func)
				);
			}
		}
		return $return;
	}
	private function RandString($length)
	{
		$hash = '';
		$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz';
		$max = strlen($chars) - 1;
		PHP_VERSION < '4.2.0' && mt_srand((double)microtime() * 1000000);
		for($i = 0; $i < $length; $i++) {
			$hash .= $chars[mt_rand(0, $max)];
		}
		return $hash;
	}
	private function RunSQL($file)
	{
		if (strlen($file) < 12)
		{
			$sql = @file_get_contents(DATA_PATH.'install/'.$file.'.sql');
		}
		else
		{
			$sql = $file;
		}
		if ($sql == '') return;
		$sql = str_replace("\r", "\n", str_replace('`{prefix}', "`" . ini('settings.db_table_prefix'), $sql));
		$sql = preg_replace('/\/\*.*?\*\/[;]?/s', '', $sql);
		$ret = array();
		$num = 0;
		foreach(explode(";\n", trim($sql)) as $query) {
			$queries = explode("\n", trim($query));
			foreach($queries as $query) {
				$ret[$num] .= $query[0] == '#' || $query[0].$query[1] == '--' ? '' : $query;
			}
			$num++;
		}
		unset($sql);

		$dbcharset = str_replace('-', '', ini('settings.charset'));

		foreach($ret as $query) {
			$query = trim($query);
			if($query) {
				if(substr($query, 0, 13) == 'CREATE TABLE ') {
					$name = preg_replace("/CREATE TABLE .*?([a-z0-9_]+)`? .*/is", "\\1", $query);
					$_sql = $this->Createtable($query, $dbcharset);
					dbc()->Query($_sql);
				} else {
					dbc()->Query($query);
				}
			}
		}
	}
	private function Createtable($sql, $dbcharset)
	{
		$type = strtoupper(preg_replace("/^\s*CREATE TABLE\s+.+\s+\(.+?\).*(ENGINE|TYPE)\s*=\s*([a-z]+?).*$/isU", "\\2", $sql));
		$type = in_array($type, array('MYISAM', 'HEAP')) ? $type : 'MYISAM';
		return preg_replace("/^\s*(CREATE TABLE\s+.+\s+\(.+?\)).*$/isU", "\\1", $sql).
		(mysql_get_server_info() > '4.1' ? " ENGINE=$type DEFAULT CHARSET=$dbcharset" : " TYPE=$type");
	}
	private function iLinks()
	{
		include_once MOD_PATH.'install.live.php';
		install_links();
	}
	private function iLives()
	{
		include_once MOD_PATH.'install.live.php';
		install_request(array(),$install_request_error);
	}
}

?>