<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name template.han.php
 * @date 2014-11-04 13:51:55
 */
 



function addquote($var) {
	return str_replace("\\\"", "\"", @preg_replace("/\[([a-zA-Z0-9_\-\.\x7f-\xff]+)\]/s", "['\\1']", $var));
}

function stripvtags($expr, $statement) {
		$expr = str_replace("\\\"", "\"", @preg_replace("/\<\?\=(\\\$.+?)\?\>/s", "\\1", $expr));
	$statement = str_replace("\\\"", "\"", $statement);
	return $expr.$statement;
}

class TemplateHandler
{
	var $TemplateRootPath="./templates/";	var $TemplatePath="";					var $TemplateDir="default";				var $CompiledFolder="compiled_tpl/";	var $CompiledPath="";					var $TemplateFile="";					var $CompiledFile="";					var $TemplateString="";				var $TemplateExtension='.html'; 	var $CompiledExtension='.php'; 	var $LinkFileType='css|js|jpeg|jpg|png|bmp|gif|swf'; 		
	
	function TemplateHandler(&$config=null)
	{
		if (!is_null($config))
		{
			$this->TemplateRootPath=isset($config['template_root_path'])?$config['template_root_path']:"./templates/";
			$this->TemplateDir=$config['template_path'];
			$this->TemplatePath=$this->TemplateRootPath.$this->TemplateDir.'/';
			if(!isset($config['compiled_root_path']) or $config['compiled_root_path']=='')
			{
				$this->CompiledPath=$this->TemplatePath.$this->CompiledFolder;
			}
			else
			{
				$this->CompiledPath=$config['compiled_root_path'].'/'.$this->TemplateDir.'/';
			}
		}
		else
		{
			$this->TemplateRootPath = ini('settings.template_root_path');
			$this->TemplateDir = ini('settings.template_path');
			$this->TemplatePath=$this->TemplateRootPath.$this->TemplateDir.'/';
			$cpl_path = ini('settings.compiled_root_path');
			if(!$cpl_path || $cpl_path == '')
			{
				$this->CompiledPath=$this->TemplatePath.$this->CompiledFolder;
			}
			else
			{
				$this->CompiledPath = $cpl_path.'/'.$this->TemplateDir.'/';
			}
		}
			}


	
	function load($name)
	{
		include $this->file($name);
	}

	
	public function absfile($path)
	{
		$this->TemplateFile = $path;
				$this->CompiledFile = handler('io')->initPath(CACHE_PATH.'templates/isingle/'.md5($path).'.php');
		if(!is_file($this->CompiledFile) || (true===DEBUG && @filemtime($this->TemplateFile) > @filemtime($this->CompiledFile))) {
			if(!is_file($this->TemplateFile))
			{
				zlog('error')->found('file.missing', $this->TemplateFile);
				die("模板文件'".$this->TemplateFile."'不存在，请检查目录");
			}

			if($this->LoadFile())
			{
				$this->Compile();
				$this->Write();
			}
			else
			{
				Return false;
			}
		}
		Return $this->CompiledFile;
	}

	
	function file($name)
	{
		$root = false;
		$rootDIR = false;
		if (substr($name, 0, 1) == '@')
		{
			$root = true;
			$name = substr($name, 1);
		}
		if (substr($name, 0, 1) == '#')
		{
			$root = true;
			$rootDIR = true;
			$name = substr($name, 1);
		}
		return $this->Template($name, $root, $rootDIR);
	}

	
	function content($__TPL_NAME__, $__TF_ARGS__)
	{
		extract($__TF_ARGS__);
		ob_start();
		include $this->file($__TPL_NAME__);
		$__CM_CONTENT__ = ob_get_clean();
		return $__CM_CONTENT__;
	}

	
	function Template($filename, $root = false, $rootDIR = false)
	{
		$this->TemplateFile=$this->TemplatePath.$filename.$this->TemplateExtension;
		$this->CompiledFile=$this->CompiledPath.$filename.$this->CompiledExtension;
		if ($root)
		{
			if ($rootDIR)
			{
				$this->TemplateFile=ROOT_PATH.$filename.$this->TemplateExtension;
				$this->CompiledFile=$this->CompiledPath.$filename.$this->CompiledExtension;
			}
			else
			{
				$this->TemplateFile=$this->TemplateRootPath.$filename.$this->TemplateExtension;
				$this->CompiledFile=$this->CompiledPath.$filename.$this->CompiledExtension;
			}
		}
						if(!is_file($this->CompiledFile) || true===DEBUG || @filemtime($this->TemplateFile) > @filemtime($this->CompiledFile)) {
			if(!is_file($this->TemplateFile))
			{
				$tpl_path= strpos($this->TemplateDir,'/') ? dirname($this->TemplatePath) . '/' : dirname($this->TemplatePath).'/default/';
				$this->TemplateFile=$tpl_path.$filename.$this->TemplateExtension;
				$this->CompiledFile=$this->CompiledPath.$filename.$this->CompiledExtension;
				if(!is_file($this->TemplateFile)) {
					$filename && zlog('error')->found('file.missing', $this->TemplateFile);
					die("模板文件'".$this->TemplateFile."'不存在，请检查目录");
				}
			}

			if($this->LoadFile())
			{
				$this->Compile();
				$this->Write();
			}
			else
			{
				Return false;
			}
		}
		Return $this->CompiledFile;
	}

	
	function EvalTemplate($filename)
	{
		$this->TemplateFile=$this->TemplatePath.$filename.$this->TemplateExtension;
		$this->Load();
		$contents=str_replace('"','\"',$this->TemplateString);
		Return "return \"{$contents}\";";
	}

	
	function LoadFile()
	{
		$fp=fopen($this->TemplateFile,'rb');
		if($fp)
		{
			$this->TemplateString=fread($fp,filesize($this->TemplateFile));
		}
		fclose($fp);
		Return true;
	}

	
	function Compile()
	{
		global $rewriteHandler;
		$var_regexp = "((\\\$[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)(-\>[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)?(\[[a-zA-Z0-9_\-\.\"\'\[\]\$\x7f-\xff]+\])*)";
		$const_regexp = "([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)";

		$nest = 5;

		$template=$this->TemplateString;

				$template = @preg_replace("/(\<form.*? method=[\"\']?post[\"\']?)([^\>]*\>)/i","\\1 \\2\n<input type=\"hidden\" name=\"FORMHASH\" value='{FORMHASH}'/>",$template);

				$template = @preg_replace("/\<\!\-\-\{(.+?)\}\-\-\>/s", "{\\1}", $template);

		$template = str_replace("{LF}", "<?=\"\\n\"?>", $template);

		$template = @preg_replace("/\{$var_regexp\}/s", "<?=\\1?>", $template);

		$template = @preg_replace("/$var_regexp/es", "addquote('<?=\\1?>')", $template);
		$template = @preg_replace("/\<\?\=\<\?\=$var_regexp\?\>\?\>/es", "addquote('<?=\\1?>')", $template);

		$template = @preg_replace("/[\n\r\t]*\{template\s+([a-z0-9_@\/]+)\}[\n\r\t]*/is", "\n<? include handler('template')->file('\\1'); ?>\n", $template);
		$template = @preg_replace("/[\n\r\t]*\{template\s+(.+?)\}[\n\r\t]*/is", "\n<? include \$this->TemplateHandler->template(\\1); ?>\n", $template);
		$template = @preg_replace("/[\n\r\t]*\{eval\s+(.+?)\}[\n\r\t]*/ies", "stripvtags('\n<? \\1 ?>\n','')", $template);

		$template = @preg_replace("/[\n\r\t]*\{conf\s+(.+?)\}[\n\r\t]*/ies", "addquote('<?=\$this->Config[\\1]?>')", $template);

		$template = @preg_replace("/[\n\r\t]*\{echo\s+(.+?)\}[\n\r\t]*/ies", "stripvtags('<? echo \\1; ?>','')", $template);
		$template = @preg_replace("/[\n\r\t]*\{elseif\s+(.+?)\}[\n\r\t]*/ies", "stripvtags('<? } elseif(\\1) { ?>','')", $template);
		$template = @preg_replace("/[\n\r\t]*\{else\}[\n\r\t]*/is", "\n<? } else { ?>", $template);

		for($i = 0; $i < $nest; $i++) {
			$template = @preg_replace("/[\n\r\t]*\{loop\s+(\S+)\s+(\S+)\}[\n\r]*(.+?)[\n\r]*\{\/loop\}[\n\r\t]*/ies", "stripvtags('\n<? if(is_array(\\1)) { foreach(\\1 as \\2) { ?>','\n\\3\n<? } } ?>\n')", $template);
			$template = @preg_replace("/[\n\r\t]*\{loop\s+(\S+)\s+(\S+)\s+(\S+)\}[\n\r\t]*(.+?)[\n\r\t]*\{\/loop\}[\n\r\t]*/ies", "stripvtags('\n<? if(is_array(\\1)) { foreach(\\1 as \\2 => \\3) { ?>','\n\\4\n<? } } ?>\n')", $template);
			$template = @preg_replace("/[\n\r\t]*\{if\s+(.+?)\}[\n\r]*(.+?)[\n\r]*\{\/if\}[\n\r\t]*/ies", "stripvtags('\n<? if(\\1) { ?>','\n\\2\n<? } ?>\n')", $template);
			$template = @preg_replace("/[\n\r\t]*\{while\s+(.+?)\}[\n\r]*(.+?)[\n\r]*\{\/while\}[\n\r\t]*/ies", "stripvtags('\n<? while(\\1) { ?>','\n\\2\n<? } ?>\n')", $template);
		}
		$template = @preg_replace("/\{$const_regexp\}/s", "<?=\\1?>", $template);
								$template = @preg_replace("/\{\~(.+?)\}/s", "<?=\\1?>", $template);
				$template = @preg_replace("/[\n\r\t]*\{ini\:(.+?)\}[\n\r\t]*/ies", "addquote('<?=ini(\"\\1\")?>')", $template);

		$template = trim($template);
		$this->TemplateString=$template;

		if(!empty($this->LinkFileType))
		{
			$this->ModifyLinks();
		}
		if($rewriteHandler)
		{
			$this->TemplateString=$rewriteHandler->output($this->TemplateString,true);
		}
	}
	
	function write()
	{
		$save_dir=dirname($this->CompiledFile);
		if(!is_dir($save_dir))$this->MakeDir($save_dir);
		$fp = fopen($this->CompiledFile, 'wb');
		if(!$fp)
		{
			zlog('error')->found('denied.io', $this->CompiledFile);
			die('模板无法写入,请检查目录是否有可写');
		}
		$length=fwrite($fp, $this->TemplateString);
		fclose($fp);
		Return $length;
	}

	

	
	function MakeDir($dir_name, $mode = 0777)
	{
		return tmkdir($dir_name, $mode);
	}
	
	function ModifyLinksbak()
	{
		preg_match_all("/src=[\"\'\s]?(.*?)[\"\'\s]|url[\(\"\']{1,3}(.*?)[\s\"\'\)]|background=[\"\']?(.*?)[\"\'\s]|href=[\"\'\s]?(.*?)[\"\'](.*?)\>/si", $this->TemplateString, $match);

		$old = @array_values(array_merge(@array_unique($match[1]), $match[2], @array_unique($match[3]), $match[4]));
		$old = array_unique($old);
		$old=preg_grep("~.*?\.(".$this->LinkFileType.")$~i",$old);
		foreach($old as $link)
		{
			if(trim($link) != "" and !strpos($link, ':/'.'/'))
			{
				if(strpos($link,'../')===0)
				{
					$this->TemplateString=str_replace($link, dirname($this->TemplatePath) . '/' . ltrim($link, './'), $this->TemplateString);
				}
				else
				{
				$this->TemplateString = str_replace($link, rtrim($this->TemplatePath,'\/') . '/' . ltrim($link, './'), $this->TemplateString);
				}
			}
		}
		return $this->TemplateString;
	}
		function ModifyLinks()
	{
		preg_match_all("/src=[\"\'\s]?(.*?)[\"\'\s]|url[\(\"\']{1,3}(.*?)[\s\"\'\)]|background=[\"\']?(.*?)[\"\'\s]|href=[\"\'\s]?(.*?)[\"\'](.*?)\>/si", $this->TemplateString, $match);

		$old = @array_values(array_merge(@array_unique($match[1]), $match[2], @array_unique($match[3]), $match[4]));
		$old = array_unique($old);
		$old=preg_grep("~.*?\.(".$this->LinkFileType.")$~i",$old);
		foreach($old as $link)
		{
			if(trim($link) != "" and false===strpos($link, ':/'.'/'))
			{
				$private_file=str_replace('templates/default/','templates/'.$this->TemplateDir.'/',$link);
				if (!is_file($private_file) && false===strpos($private_file,'templates')) {
					$private_file = 'templates/' . $this->TemplateDir . '/' . $private_file;
				}
				if('default'!=$this->TemplateDir && !is_file($private_file)) {
					$private_file = str_replace('templates/'.$this->TemplateDir.'/','templates/default/',$private_file);
				}
				if(is_file($private_file)==false) {
					continue;
				}

				$this->TemplateString = str_replace($link,$private_file, $this->TemplateString);
			}
		}
		return $this->TemplateString;
	}

	
	function RepairBracket($var)
	{
		Return @preg_replace("~\[([a-z0-9_\x7f-\xff]*?[a-z_\x7f-\xff]+[a-z0-9_\x7f-\xff]*?)\]~i","[\"\\1\"]",$var);
	}


}



?>