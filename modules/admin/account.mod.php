<?php

/**
 * 模块：账户管理
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package module
 * @name account.mod.php
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
	public function Main()
	{
		exit('admin.mod.account.index');
	}
	public function config()
	{
		$this->CheckAdminPrivs('userreg');
		include handler('template')->file('@admin/account_config');
	}
	public function credits(){
		$this->CheckAdminPrivs('credits');
		$credit_set = false;
		$credits = ini('credits.level');
        $credits = explode(',', $credits);
        include handler('template')->file('@admin/setting_credits');
	}
	public function creditssave(){
		$this->CheckAdminPrivs('credits');
		$credits = post('credits');
				foreach ($credits as $k => $v) {
			if ( (int)$v === 0) {
				unset($credits[$k]);
			}
		}
		sort($credits);
		$credits = implode(',', $credits);
		ini('credits.level', $credits);
		$this->Messager('等级设置成功！', 'admin.php?mod=account&code=credits');
	}
	public function credit(){
		$this->CheckAdminPrivs('creditset');
		$credit_set = true;
		$config = ini('credits.config');
        include handler('template')->file('@admin/setting_credits');
	}
	public function creditsave(){
		$this->CheckAdminPrivs('creditset');
		$config = post('config');
		foreach ($config as $k => $v) {
			$config[$k] = max(0,(int)$v);
		}
		ini('credits.config', $config);
		$this->Messager('积分设置成功！', 'admin.php?mod=account&code=credit');
	}
}

?>