<?php

/**
 * 模块：API管理面板
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package module
 * @name api.mod.php
 * @version 1.0
 */

class ModuleObject extends MasterObject
{
	public function ModuleObject( $config )
	{
		$this->MasterObject($config);
		$loader = INCLUDE_PATH.'api/func/loader.php';
		if (is_file($loader))
		{
			require $loader;
		}
		$runCode = Load::moduleCode($this);
		$this->main();
	}
	public function main()
	{
		$this->CheckAdminPrivs('apimanage');
		if (function_exists('apim'))
		{
			apim('dashboard')->main($this);
		}
		else
		{
						include handler('template')->file('@admin/apim.missing');
		}
	}
}

?>