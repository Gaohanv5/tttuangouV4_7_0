<?php

/**
 * 界面支持：产品展示
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package UserInterface
 * @name igos.ui.php
 * @version 1.0
 */

class iGOSUI
{
	
	public function load($product)
	{
		$style = ini('ui.igos.style');
		$style || $style = 'lashou';
		if(!in_array($style,array('lashou','meituan'))){
			$style = 'lashou';
		}
		if(INDEX_DEFAULT === true && false == ini('ui.igos.oldindex')){	//INDEX_DEFAULT=true  $style=meituan ini('ui.igos.oldindex')=false
			include handler('template')->file('@html/igos/'.$style.'/default');
		}else{
			include handler('template')->file('@html/igos/'.$style.'/index');
		}
	}
}

?>