<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name phone.mod.php
 * @date 2014-12-11 14:44:49
 */
 




class ModuleObject extends MasterObject
{
	function ModuleObject( $config )
	{
		$this->MasterObject($config);
		$runCode = Load::moduleCode($this, false, false);
		$this->$runCode();
	}
	public function main()
	{
		exit('ok');
	}
	public function vfsend()
	{
		$phone = post('phone', 'txt');

		if(empty($phone) && get('user_phone') && user()->get('phone_validate')) {
			$phone = user()->get('phone');
		}

		$ret = logic('phone')->Check($phone);

		if(false == $ret) {
			$ret = logic('phone')->VfSend($phone);
		}

		exit($ret);
	}
}

?>