<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name config.han.php
 * @date 2014-09-01 17:24:22
 */
 


if(!defined('CONFIG_PATH'))define("CONFIG_PATH",'./config/');

class ConfigHandler
{
	function ConfigHandler()
	{

	}
	public static function file($type=null)
	{
		return CONFIG_PATH.($type===null?'settings':$type).'.php';
	}
	
	public static function get()
	{
		global $_CONFIG;
		$func_num_args=func_num_args();
		if($func_num_args===0)
		{
			Return Obj::registry('config');
		}
		else
		{
			$func_args=func_get_args();
			$type=$func_args[0];
			if(isset($_CONFIG[$type])===false)
			{
				$file = ConfigHandler::file($type);
				if (!is_file($file)) return null;
				include($file);
				if(isset($config[$type]))
				{
					$_CONFIG[$type]=$config[$type];
				}
				else
				{
					$config=isset(${$type})?${$type}:
					$_CONFIG[$type]=${$type};
				}
			}

			if($func_num_args===1)Return $_CONFIG[$type];

			foreach($func_args as $arg)
			{
				$path_str.="['$arg']";
			}
			$config=eval('return $_CONFIG'.$path_str.";");
			Return $config;
		}
	}
	
	function set()
	{
		global $_CONFIG;
		$func_num_args=func_num_args();
		$func_args=func_get_args();
		$value=array_pop($func_args);
				$value = dstripslashes($value);
		$type=array_shift($func_args);

		$remark = '/'.'*********************************************
 *[tttuangou] (C)2005 - 2010 Cenwor Inc.
 *
 * tttuangou '.$type.'配置
 *
 * @author www.tttuangou.net
 *
 * @time '.date('Y-m-d H:i').'
 *********************************************'.'/

 ';

		$file=ConfigHandler::file($type);
		if($type===null)
		{
			$data="<?php \r\n {$remark} \r\n \$config=".var_export($value,true)."; \r\n ?>";
		}
		else
		{
			if(($config=$_CONFIG[$type])===null)
			{
				$config=array();
				@include($file);
				$config=$config[$type];
			}
			foreach($func_args as $arg)
			{
				$path_str.="['$arg']";
			}
			eval($value===null?'unset($config'.$path_str.');':'$config'.$path_str.'=$value;');
			$data="<?php \r\n {$remark} \r\n\$config['$type']=".var_export($config,true).";\r\n?>";
		}

		@$fp=fopen($file,'wb');
		if(!$fp)
		{
			zlog('error')->found('denied.io', $file);
			die($file."文件无法写入,请检查是否有可写权限。");
		}
		$len=fwrite($fp, $data);
		fclose($fp);

		if($len)$_CONFIG[$type]=$config;
		return $len;
	}
}

?>