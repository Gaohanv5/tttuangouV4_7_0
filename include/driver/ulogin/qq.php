<?php

/**
 * 登录接口：qq账号登陆
 * @copyright (C)2013 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package ulogin
 * @name qq.php
 * @version 1.0
 */

class qqUnionLoginDriver extends UnionLoginDriver
{
	private $qc;

	
	public function __construct()
	{
		if ( ini_get("allow_url_fopen") == '' )
		{
			exit('请在php.ini里开启allow_url_fopen选项');
		}

		if ( function_exists('openssl_open') === false )
		{
			exit('请确认开启了openssl组件');
		}

		require_once(dirname(__FILE__)."/qq/API/qqConnectAPI.php");
		$this->qc = new QC();
	}

	
	public function linker()
	{
		$this->qc->qq_login();
		exit;
	}

	
	public function  get_user_info()
	{
		return $this->qc->get_user_info();
	}

		public function get_openid()
	{
		$this->qc->qq_callback();
		$this->qc->get_openid();

		header('Location: '.rewrite('?mod=account&code=qqgetuserinfo'));
		exit;
	}

}
