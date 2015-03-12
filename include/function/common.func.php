<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name common.func.php
 * @date 2014-11-04 13:51:54
 */
 



function position()
{
	global $rewriteHandler;
	$decribe=__('您的位置：');
	$child_symbol=' &gt; ';
	$mod=$_GET['rmod']?$_GET['rmod']:$_GET['mod'];
	if(strpos($mod,'_')!==false)list($mod,$mod_child)=explode('_',$mod);
	$code=$_GET['code'];

	$config = ConfigHandler::get();

	$list['index']="<a href='{$config['site_url']}'>{$config['site_name']}".__('首页')."</a>";

	$list['mod']=ConfigHandler::get('header_menu','list',$mod);
	if($list['mod']!=false)
	{
		if($rewriteHandler)$list['mod']['link']=$rewriteHandler->formatURL($list['mod']['link']);
		$list['mod']="<a href='{$list['mod']['link']}'>{$list['mod']['name']}</a>";
	}
	else
	{
		unset($list['mod']);
	}

	$args_list=func_get_args();
	if(is_array($args_list) and count($args_list)>0)
	{
		foreach ($args_list as $key=>$value)
		{
			if(empty($value))continue;
			if(is_string($value))
			{
				if(trim($value)=='')continue;
				$value=preg_replace("~(\s+[/]\s+)|(\-\>)~",$child_symbol,$value);
			}
			else
			{
				if(isset($value['name']))
				{
					$value['url']=($value['url']!='')?$value['url']:$value['link'];
					$url=$value['url'];
					$name=$value['name'];
				}
				else
				{
					$url=current($value);
					$name=key($value);
				}
				if($rewriteHandler)$url=$rewriteHandler->formatURL($url);
				$value="<a href='$url'>$name</a>";
			}
			$list[$key]=$value;
		}
	}
		$position=implode($child_symbol,$list);
	return $decribe.$position;
}


class Obj
{
	function &Obj($name=null)
	{
		Return Obj::_share($name,$null,'get');
	}

	public static function &_share($name=null,&$mixed,$type='set')
	{
		static $_register=array();
		if($name==null)
		{
			Return $_register;
		}
		if(isset($_register[$name]) and $type==='get')
		{
			Return $_register[$name];
		}
		elseif($type==='set')
		{
			$_register[$name]=&$mixed;
		}

		return true;
	}
	
	public static function register($name,&$obj)
	{
		Obj::_share($name,$obj,"set");
	}
	
	public static function &registry($name=null)
	{
		Return Obj::_share($name,$null,'get');
	}
	
	function isRegistered($name)
	{
		Return isset($_register[$name]);
	}
}

function sms_server_init()
{
	return base64_decode('aHR0cDovL3Ntc2xzLnR0dHVhbmdvdS5uZXQ6ODA4MC9MU0JzbXMvc21zSW50ZXJmYWNlLmRv');
}

function ajherrorlog($type='',$log='',$halt=1) {
	$logfile = ROOT_PATH . 'errorlog/'.$type . '-' . date('Y-m').'.php';
	if (!is_file($logfile)) {
		$log ="<? exit; ?>\r\n" . $log;
	}
	$log = "[".my_date_format(time(),"Y-m-d H:i:s")."]" . $log . "\r\n";

	global $IoHandler;
	if(is_null($IoHandler)) {
		$load = new Load();
		$load->lib('io');
		$IoHandler = new IoHandler();
		$log = " \r\n ------------------------------------------------------ \r\n " . $log;
	}
	if (!is_dir(dirname($logfile))) {
		$IoHandler->MakeDir(dirname($logfile));
	}

	$IoHandler->WriteFile($logfile,$log,'a');

	if($halt) {
		exit();
	}
}

function error($type, $message, $file = null, $line = 0)
{
	if(E_NOTICE==$type) return true;
	require_once(LIB_PATH."error.han.php");
	if(false == class_exists("ErrorHandler")) return false;
	$ErrorHandler = new ErrorHandler($type, $message, $file, $line);
	if(error_reporting() && $type) exit($ErrorHandler->fatal());
	return true;
}
?>