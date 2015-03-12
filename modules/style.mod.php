<?php

/**
 * 模块：样式相关
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package module
 * @name style.mod.php
 * @version 1.0
 */

class ModuleObject extends MasterObject
{
	function ModuleObject( $config )
	{
		$this->MasterObject($config);
		$runCode = Load::moduleCode($this, false, false);
		$this->$runCode();
	}
	
	public function load()
	{
		$id = get('id', 'txt');
		ui('style')->setCSS($id);
		$to = referer();
		if(false !== strpos($to, 'style')) {
			$to = '?';
		}
		$this->Messager('界面切换成功！', $to);
	}
}

?>