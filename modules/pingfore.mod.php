<?php

/**
 * 模块：前端心跳包
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package module
 * @name pingfore.mod.php
 * @version 1.1
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
		$class = get('class')=='mail'?'mail':'sms';
				$runx = 13;
		$pause = 60;
		$MT = ini('service.push.mthread');
		if ($MT || $class == 'sms')
		{
			$runx = 30;
			$pause = 10;
		}
		logic('product_notify')->sync();
		logic('push')->run($runx, $class);
		echo jsonEncode(array(
			'extend' => ($class=='sms'?'mail':'sms'),
			'interval' => $pause
		));
				$this->extend();

		exit;
	}
	function extend()
	{
		app('smsw')->detecting();
	}
}

?>