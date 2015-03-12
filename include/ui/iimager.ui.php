<?php

/**
 * 界面支持：产品图片显示
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package UserInterface
 * @name iimager.ui.php
 * @version 1.0
 */

class iImagerUI
{
	public $style = 'default';
	
	public function single($pid, $iid)
	{
		include handler('template')->file('@html/iimager/'.$this->style.'/single');
	}
	
	public function single_lazy()
	{
		include handler('template')->file('@html/iimager/'.$this->style.'/lazyload');
	}
	
	public function multis($pid, $iids)
	{
		include handler('template')->file('@html/iimager/'.$this->style.'/multis');
	}
	
	public function seller_single($sid, $iid)
	{
		include handler('template')->file('@html/iimager/'.$this->style.'/seller_single');
	}
	public function seller_multis($sid, $iids)
	{
		if(false == is_array($iids)) {
			$iids = explode(',', $iids);
		}
		$iids = (array) $iids;
		include handler('template')->file('@html/iimager/'.$this->style.'/seller_multis');
	} 
}

?>