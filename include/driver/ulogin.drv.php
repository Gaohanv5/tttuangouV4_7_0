<?php

/**
 * 驱动：快捷登录
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package driver
 * @name ulogin.drv.php
 * @version 1.0
 */

class UnionLoginDriver
{
	
	public final function api($name)
	{
		$SID = 'driver.ulogin.api.'.$name;
		$obj = moSpace($SID);
		if ( ! $obj )
		{
			require dirname(__FILE__).'/ulogin/'.$name.'.php';
			$className = $name.'UnionLoginDriver';
			$obj = moSpace($SID, (new $className()));
		}
		return $obj;
	}
}

?>