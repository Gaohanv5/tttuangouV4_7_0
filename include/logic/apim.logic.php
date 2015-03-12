<?php
/**
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package php
 * @name apim.logic.php
 * @date 2013-11-07 19:52:54
 */
 




class ApimLogic
{
	
	public function __construct()
	{
		$loader = INCLUDE_PATH.'api/func/loader.php';
		if (is_file($loader))
		{
			require $loader;
		}
	}
	
	public function protocol_acl($do, $ps, $data = null)
	{
		return apim('dashboard')->protocol_acl_status($do, $ps, $data);
	}
}

?>