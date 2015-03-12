<?php
/**
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package php
 * @name master.mod.php
 * @date 2013-11-07 19:52:54
 */
 




class MasterObject
{
	public function MasterObject(&$config)
	{
		$loader = INCLUDE_PATH.'api/func/loader.php';
		if (is_file($loader))
		{
			require $loader;
		}
 	}
}

?>