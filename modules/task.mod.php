<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name task.mod.php
 * @date 2014-09-01 17:24:23
 */
 


class ModuleObject extends MasterObject
{
	var $Config = array();
	function ModuleObject(& $config)
	{
		$this->MasterObject($config);
		Load::moduleCode($this);$this->Execute();
	}
	function Execute()
	{
		if ('run' == $this->Code)
		{
			$this->Run();
		}
	}
	function Run()
	{
		require_once(LOGIC_PATH.'task.logic.php');
		$TaskLogic=new TaskLogic();
		$TaskLogic->run();
		echo 'ok';
		exit;
	}
}

?>