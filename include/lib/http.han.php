<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name http.han.php
 * @date 2014-09-01 17:24:22
 */
 


class HttpHandler
{
	function HttpHandler()
	{

	}
	
	public static function &CheckVars(&$array,$reserve=false)
	{
		foreach($array as $key=>$val)
		{
			if($reserve) return ;
			if($key==false) continue;
			if(is_array($val)==false)
			{
				$array[$key]=HttpHandler::CleanVal($val);
			}
			else
			{
				$array[$key]=HttpHandler::CheckVars($val);
			}
		}

		Return $array;
	}
	
	public static function CleanVal(&$val)
	{
				if(MAGIC_QUOTES_GPC==0) $val = addslashes($val);

				Return $val;
	}
	function UnCleanVal(&$val)
	{
		$val=stripslashes($val);

		return $val;
	}
}

#列子:

?>