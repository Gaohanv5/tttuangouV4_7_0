<?php

/**
 * 模块：APP接口
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package module
 * @name app.mod.php
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
	function main()
	{
		$this->CheckAdminPrivs('appmanage');
		$iphone = ini('iphone');
		$app_img_d = base64_decode('aHR0cDovL3NlcnZlci50dHR1YW5nb3UubmV0L3FyY29kZS8/ZGF0YT0=') . urlencode(ini('settings.site_url').'/index.php?mod=downapp&code=down');
		$app_img_x = base64_decode('aHR0cDovL3NlcnZlci50dHR1YW5nb3UubmV0L3FyY29kZS8/c2l6ZT1zbWFsbCZkYXRhPQ==') . urlencode(ini('settings.site_url').'/index.php?mod=downapp&code=down');
		$from = 'app';
		include handler('template')->file('@admin/app_config');
	}
	function lpc()
	{
		$master = get('master', 'txt');
		$processor = get('processor', 'txt');
		$appObject = app($master);
		if (method_exists($appObject, $processor))
		{
			$appObject->$processor();
			exit;
		}
		else
		{
			exit('LPC.E.404');
		}
	}
}

?>