<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name cache.func.php
 * @date 2014-09-01 17:24:22
 */
 


function cache($name,$lifetime=null,$only_get=false)
{
	static $_filelist,$_lastfile,$_file,$_caches;
	$path		="./cache/";

	if($lifetime!==null)
	{
		if($_file!==null)$_lastfile=$_file;
		$_file=$path.$name.'.cache.php';
		$_filelist[$_file]=$_lastfile;
		$file=$_file;
		if($only_get)$_file=null;
		if ($lifetime===0) return @unlink($file);
		if($_caches[$name.$lifetime]!==null)return $_caches[$name.$lifetime];
		@$cache_exists=include($file);
		if($cache_exists && ($lifetime===-1 || @filemtime($file)+$lifetime>time()))return $_caches[$name.$lifetime]=$cache;
	}
	else
	{
		if($_file===null)if($_lastfile===null)return false;else $_lastfile=$_filelist[$_file=$_lastfile];
		if(@is_writeable($path)===false)return @trigger_error("缓存目录 $path 不可写",E_USER_WARNING);
		if(@is_dir($cache_dir=@dirname($_file))==false)
		{
			tmkdir($cache_dir);
		}

		$data=var_export($name,true);

		$data="<?php \r\n\$cache=$data;\r\n?>";
		@$fp=fopen($_file,"wb");
		@flock($fp, LOCK_EX);
		@$len=fwrite($fp,$data);
		@flock($fp, LOCK_UN);
		@fclose($fp);
		$_file=null;
		return $len;
	}
	return false;
}

function clearcache()
{
	return cacheclear(true);
}
function cacheclear($handle=false)
{
	if(true === $handle) {
		static $sCacheIoHandler = null;
		if(is_null($sCacheIoHandler)) {
			include_once(LIB_PATH . 'io.han.php');
			$sCacheIoHandler = new IoHandler();
		}
		return @$sCacheIoHandler->ClearDir(CACHE_PATH);
	}
	return true;
}


?>