<?php

/**
 * 功能载入接口
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package base
 * @name load.php
 * @version 1.0
 */

class Load
{
	function functions($name)
	{
		return engine_class_file_load(FUNCTION_PATH.$name.'.func');
	}
	public static function logic($name)
	{
		return engine_class_file_load(LOGIC_PATH.$name.'.logic');
	}
	function lib($name)
	{
		return engine_class_file_load(LIB_PATH.$name.'.han');
	}
	function driver($name)
	{
		return engine_class_file_load(DRIVER_PATH.$name.'.drv');
	}
	public static function moduleCode($class, $debug = DEBUG, $accCheck = true)
	{
		$code = $class->Code;
		$extend = $class->OPC;
		$runs = 'main';
		if (preg_match('/[a-z0-9_]/i', $code))
		{
			if ($extend != '' && preg_match('/[a-z0-9_]/i', $extend))
			{
				$code .= '_'.$extend;
			}
			$runs = $code;
			
			if ($debug && !method_exists($class, $runs))
			{
				$runs = 'main';
			}
		}
						return $runs;
	}
}
?>