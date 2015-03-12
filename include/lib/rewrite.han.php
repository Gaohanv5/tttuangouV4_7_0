<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name rewrite.han.php
 * @date 2014-09-03 17:06:17
 */
 



class rewriteHandler
{
	var $host = '';
	var $subDomainVar = '';
	var $absPath='/';
	var $argSeparator='/';
	var $varSeparator="-";
	var $prependVarList=array('mod', 'code');	var $default='index.php';
	var $gateway='';
	var $extention='';
	var $varReplaceList=array();
	var $valueReplaceList=array();
	var $redirect=false;	
	function rewriteHandler()
	{
	}
		private function checkVal($val){
		$val = preg_replace('/[\s\(\)\=]/', '', $val);
		if( strlen($val) > 255 ) die('非法调用');
		return $val;
	}
	function parseRequest($request=null)
	{
		if($request===null)$request=$this->getRequestUri();

				if(strpos($request,$this->absPath)===0)$request=substr($request,strlen($this->absPath));

				if ($this->subDomainVar && $_SERVER['HTTP_HOST']!=$this->host) {
			$subDomain = substr($_SERVER['HTTP_HOST'],0,strpos($_SERVER['HTTP_HOST'],'.' . $this->host));
			$_SERVER['HTTP_HOST'] = $this->host;
			$request = $subDomain . $this->argSeparator . $request;
		}

				if($this->gateway && ($pos=strpos($request,$this->gateway))!==false)
		{
			$request=substr($request,$pos+strlen($this->gateway));
		}
				if($this->extention && ($len=strlen($this->extention)) && substr($request,-$len)==$this->extention)
		{
			$request=substr($request,0,-$len);
		}
				if(($pos=strpos($request,'?'))!==false)
		{
			if($this->redirect==false || $_SERVER['REQUEST_METHOD']=='POST')return null;
			@header("HTTP/1.1 301 Moved Permanently");
			@header("Location: ".$this->formatQueryString(substr($request,$pos+1),null));
			exit;
		}
		if($request==$this->default)return null;
				$request=explode($this->argSeparator,$request);
		if($request[0]==$this->default)array_shift($request);
				$_v=0;
		$query_string='';
				$var_separator_len=strlen($this->varSeparator);
		foreach ($request as $arg)
		{
			if($arg==='')continue;

			if(($pos=strpos($arg,$this->varSeparator))!==false)
			{
				$var=substr($arg,0,$pos);
				$value=substr($arg,$pos+$var_separator_len);
			}
			else
			{
				if(($var=$this->prependVarList[$_v])===null)$var=$arg;
				else $value=$arg;
				$_v++;
			}
			if(($_r=$this->valueReplaceList[$var]) && ($_value=array_search($value,$_r))!==false)$value=$_value;
			$value=urldecode($value);
			if($this->varReplaceList && ($_var=array_search($var,$this->varReplaceList))!==false)$var=$_var;
			$value = $this->checkVal($value);
			$_GET[$var]=$value;
			$_POST[$var]=$value;
			$_REQUEST[$var]=$value;
			$GLOBALS[$var]=$value;
			$query_string.=$arg_separator.$var.'='.$value;
			$arg_separator="&";
		}
		$_SERVER['QUERY_STRING']=$query_string;
	}
	function getRequestUri()
	{
		if (isset($_SERVER['HTTP_X_REWRITE_URL'])) 		{
			$request_uri = $_SERVER['HTTP_X_REWRITE_URL'];
		}
		elseif (isset($_SERVER['REQUEST_URI']))
		{
			$request_uri = $_SERVER['REQUEST_URI'];
		}
		elseif (isset($_SERVER['ORIG_PATH_INFO'])) 		{
			$request_uri = $_SERVER['ORIG_PATH_INFO'];
			if (!empty($_SERVER['QUERY_STRING']))
			{
				$request_uri .= '?' . $_SERVER['QUERY_STRING'];
			}
		}
		else
		{
			$request_uri = null;
		}
		return $request_uri;
	}
	function output($output,$return=false)
	{
				$output=preg_replace_callback("~(action|href|src)\s*=\s*([\"\'])(([a-z]*:\/?\/?)?((?:".$this->default.")?\??)(.*?\.?(css|js|gif|jpg|png|jpeg)?))\\2~i",array($this,'_formatLink'),$output);
		$output=preg_replace("~([\"\'])(ajax\.php)\\1~i","\\1".$this->absPath."\\2\\1",$output);
		if($return)return $output;
		exit($output);
	}
		function formatURL($url,$encode=false)
	{
		if(strpos($url,":/"."/")!==false || ($pos=strpos($url,'?'))===false)return $url;
		$url=substr($url,$pos+1);
		$url=$this->formatQueryString($url,$encode);
		$_sch = array('/'.'/'.'/'.'/','/'.'/'.'/',"/./");
		if(!$this->subDomainVar && !$this->host) $_sch[] = '/'.'/';
		return str_replace($_sch,'/',$url);
	}
		function formatQueryString($query_string,$encode=false)
	{
		$host = $this->host ? ('http:/'.'/' . $this->host) : '';
		if(empty($query_string))return $host . $this->absPath.$this->default;
		if(($anchor_pos=strpos($query_string,"#"))!==false)		{
			$anchor=substr($query_string,$anchor_pos);
			$query_string=substr($query_string,0,$anchor_pos);
		}
		$_arg_list=explode("&",str_replace("amp;","",$query_string));
		$_args1=$_args2=array();
		foreach ($_arg_list as $arg)
		{
			if(($pos=strpos($arg,'='))!==false)
			{
				$var=substr($arg,0,$pos);
				$value=substr($arg,$pos+1);
				if($_value=$this->valueReplaceList[$var][$value])$value=$_value;				if($encode)$value=urlencode($value);				if(null===$encode)$value=urlencode(urldecode($value));
				if($this->subDomainVar && $this->subDomainVar == $var) {
					$host = str_replace('http:/'.'/','http:/'.'/'.$value.'.',$host);				} else {
					if($_var=$this->varReplaceList[$var]) {
						$var=$_var;					}
					if($this->prependVarList && ($p=array_search($var,$this->prependVarList))!==false)
					{
						$_args1[$p]=$value;
					}
					else
					{
						$_args2[$var]=$var.$this->varSeparator.$value;
					}
				}
			}
		}
		$query=$this->absPath.$this->gateway;
		if(!$_args1 && !$_args2)
		{
			$query.=$this->default;
		}
		else
		{
			if($this->prependVarList)
			{
				ksort($_args1);
				if($_args1)$query.=implode($this->argSeparator,$_args1);
			}
			if($_args2 && ksort($_args2))$query.=$this->argSeparator.implode($this->argSeparator,$_args2);
		}
		return $host . $query.$this->extention.$anchor;
	}
		function _formatLink($link_info)
	{
		list(,$attr,$quote,$_url,$xieyi,$default,$query_string,$js_img)=$link_info;

				if (!$xieyi)
		{
			if ($js_img!="")			{
				$_url=$this->absPath.$_url;
			}
			elseif ($_url!="" && $default!="") 			{
				$_url=$this->formatQueryString($query_string);
			}
			elseif (strpos($_url,'$')===false)			{
				$_url=$this->absPath.$_url;
			}

			$_sch = array('/'.'/'.'/'.'/'.'/', '/'.'/'.'/'.'/', '/'.'/'.'/', "/./", );
			if(!$this->subDomainVar && !$this->host) $_sch[] = '/'.'/';
			$_url = str_replace($_sch,'/',$_url);
			$_url = str_replace($_sch,'/',$_url);

			
			
			if('/' != $this->absPath)
			{
				$_find = str_replace($_sch, '/', "/{$this->absPath}/{$this->absPath}/");
				$_strpos = strpos($_url, $_find);
				if(0 === $_strpos)
				{
					$_url = str_replace(array(str_replace($_sch, '/', "/{$this->absPath}/{$this->absPath}/{$this->absPath}/{$this->absPath}/"), str_replace($_sch, '/', "/{$this->absPath}/{$this->absPath}/{$this->absPath}/"), $_find, ), "/{$this->absPath}/", $_url);
					$_url = str_replace($_sch, '/', $_url);
				}
			}
		}
		return $attr.'='.$quote.$_url.$quote;
	}
}


?>