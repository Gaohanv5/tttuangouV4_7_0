<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name check.mod.php
 * @date 2014-09-01 17:24:22
 */
 

class ModuleObject extends MasterObject
{
	var $Config = array(); 	var $ID;

	function ModuleObject(& $config){
		$this->MasterObject($config);
		$this->initMemberHandler();
		$this->ID=$this->Post['id']?(int)$this->Post['id']:(int)$this->Get['id'];
		Load::moduleCode($this);$this->Execute();
	}

	function Execute(){
		switch ($this->Code){
			case 'truename':
				$this->Truename();
				break;
			case 'email':

				$this->CheckEmail();
				break;
		}

	}

	function CheckEmail(){
		exit('error');
	}
	function Truename(){
		exit('error');
	}
}
?>