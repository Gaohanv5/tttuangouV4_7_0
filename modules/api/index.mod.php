<?php

/**
 * 模块：默认功能区
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package module
 * @name index.mod.php
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
		if (function_exists('apim'))
		{
			$uri = get('r')? get('r') : post('r');
			apim('gateway')->dispatch($uri);
		}
		else
		{
			exit('API ERROR : NOT SUPPORTED');
		}
	}
}

?>