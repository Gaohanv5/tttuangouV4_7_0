<?php

/**
 * 逻辑区：静态页面管理
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package logic
 * @name html.logic.php
 * @version 1.0
 */

class StaticHTMLMgrLogic
{
	
	private $rootPATH = '';
	
	private $fileEXT = '.html';
	
	public function __construct()
	{
		$this->rootPATH = handler('template')->TemplateRootPath.'html/oofiles/';
	}
	
	public function query($name)
	{
		return $this->loadOOFile($name);
	}
	
	public function page_exists($name)
	{
		return $this->findOOFile($name) == '404' ? false : true;
	}
	
	public function name_invalid($name)
	{
		$name = trim($name);
		if (!preg_match('/^[a-z0-9_]+$/i', $name))
		{
			return '标记名称不符合规则！（只能使用英文字符、数字、下划线）';
		}
		if (is_file($this->rootPATH.$name.$this->fileEXT))
		{
			return '标记名称已被使用！';
		}
		return false;
	}
	
	public function update($flag, $title, $content)
	{
		ini('html.list.'.$flag.'.title', $title);
		file_put_contents($this->rootPATH.$flag.$this->fileEXT, $content);
	}
	
	public function delete($flag)
	{
		ini('html.list.'.$flag, INI_DELETE);
		$rFile = $this->rootPATH.$flag.$this->fileEXT;
		is_file($rFile) && unlink($rFile);
	}
	
	private function loadOOFile($name)
	{
		$return = array();
		$return['name'] = $name = $this->findOOFile($name);
		$return['title'] = ($name == '404') ? '未找到页面' : ini('html.list.'.$name.'.title');
		$html = ($name == '404') ? '您要查看的页面未找到！' : (ini('html.list.'.$name.'.enabled') ? (string)@file_get_contents($this->rootPATH.$name.$this->fileEXT) : '您要查看的页面已经关闭！');
		$return['content'] = trim($html) ? $html : '读取文件错误！';
		return $return;
	}
	
	private function findOOFile($name)
	{
		$name = trim($name);
		if (!preg_match('/^[a-z0-9_]+$/i', $name))
		{
			return '404';
		}
		if (!ini('html.list.'.$name))
		{
			return '404';
		}
		if (!is_file($this->rootPATH.$name.$this->fileEXT))
		{
			return '404';
		}
		return $name;
	}
}

?>