<?php

/**
 * 模块：报表统计
 * @copyright (C)2014 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package module
 * @name salecount.mod.php
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
		$this->CheckAdminPrivs('salecount');
		include handler('template')->file('@admin/salecount_main');
	}
	public function product()
	{
		$this->CheckAdminPrivs('salecount');
		$list = logic('salecount')->count_product();
		include handler('template')->file('@admin/salecount_product');
	}
	public function payment()
	{
		$this->CheckAdminPrivs('salecount');
		$list = logic('salecount')->count_payment();
		include handler('template')->file('@admin/salecount_payment');
	}
	public function user()
	{
		$this->CheckAdminPrivs('salecount');
		$list = logic('salecount')->count_user();
		
		include handler('template')->file('@admin/salecount_user');
	}
	public function fund()
	{
		$this->CheckAdminPrivs('salecount');
		$list = logic('salecount')->count_fund();
		include handler('template')->file('@admin/salecount_fund');
	}
}
?>