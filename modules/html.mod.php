<?php

/**
 * 模块：静态内容显示
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package module
 * @name html.mod.php
 * @version 1.1
 */

class ModuleObject extends MasterObject
{
	function ModuleObject( $config )
	{
		$this->MasterObject($config);
		$runCode = Load::moduleCode($this, false, false);
		$this->load($runCode);
	}
	function load($pageName)
	{
		$html = logic('html')->query($pageName);
		$this->Title = $html['title'];
		include handler('template')->file('html_static');
	}
}

?>