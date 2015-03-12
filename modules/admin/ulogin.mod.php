<?php

/**
 * 模块：社会化登录管理
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package module
 * @name ulogin.mod.php
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
		$this->CheckAdminPrivs('ulogin');
		$is_allow_url_fopen = ini_get("allow_url_fopen") == '' ? false : true;
		$openssl_open 		= function_exists('openssl_open') === false ? false : true;
		$session_start		= function_exists('session_start') === false ? false : true;

		$file = DATA_PATH.'ulogin.qq.php';
		if (file_exists($file)) $data = file_get_contents($file);
		$data = json_decode($data);
		include handler('template')->file('@admin/ulogin');
	}

	
	public function qqset()
	{
		$this->CheckAdminPrivs('ulogin');
		if ( ini_get("allow_url_fopen") == '' )
		{
			$this->Messager('请在php.ini里开启allow_url_fopen选项');
		}

		if ( function_exists('openssl_open') === false )
		{
			$this->Messager('请确认开启了openssl组件');
		}

		if (function_exists('session_start') === false) {
			$this->Messager('请确认启用了session支持');
		}
		$_POST['callback'] = ini('settings.site_url').'/?mod=account';
		$_POST['storageType'] = "file";
	    	    	    	    	    $_POST['scope'] = 'get_user_info';
	    $_POST['errorReport'] = 'false';
	    unset($_POST['FORMHASH']);
	    unset($_POST['mod']);
	    $setting = json_encode($_POST);
	    $setting = str_replace("\/", "/",$setting);

	    $file = DATA_PATH.'ulogin.qq.php';
		@file_put_contents($file, $setting);

	    if (!empty($_POST['appid']) && !empty($_POST['appkey'])) {
			$data = ini('alipay.account.login.source');
			$data = array_merge($data, array('qq'=>'QQ快捷登录'));
			ini('alipay.account.login.source',$data);
		}else{
			$udata = ini('alipay.account.login.source');
			unset($udata['qq']);
			ini('alipay.account.login.source',$udata);
		}
		$this->Messager('设置成功！', '?mod=ulogin');
	}
}

?>