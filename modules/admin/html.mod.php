<?php

/**
 * 模块：静态内容管理
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package module
 * @name html.mod.php
 * @version 1.0
 */

class ModuleObject extends MasterObject
{
	function ModuleObject( $config )
	{
		$this->MasterObject($config);
		$runCode = Load::moduleCode($this);
		$this->$runCode();
	}
	function main()
	{
		exit('hey boy what are you looking for ?');
	}
	function front()
	{
		$this->CheckAdminPrivs('htmlset');
		$list = ini('html.list');
		include handler('template')->file('@admin/html_front_list');
	}
	function add()
	{
		$this->CheckAdminPrivs('htmlset');
		include handler('template')->file('@admin/html_file_edit');
	}
	function edit()
	{
		$this->CheckAdminPrivs('htmlset');
		$flag = get('flag');
		if (!logic('html')->page_exists($flag))
		{
			$this->Messager('静态页面不存在，请重新选择并编辑！', '?mod=html&code=front');
		}
		$html = logic('html')->query($flag);
		include handler('template')->file('@admin/html_file_edit');
	}
	function check_flag()
	{
		$this->CheckAdminPrivs('htmlset','ajax');
		$name_invalid = logic('html')->name_invalid(get('flag'));
		exit( $name_invalid ? $name_invalid : 'true' );
	}
	function save()
	{
		$this->CheckAdminPrivs('htmlset');
		$flag = post('name');
		$title = post('title');
		$content = post('content');
		logic('html')->update($flag, $title, stripcslashes($content));
		$this->Messager('保存完成！', '?mod=html&code=front');
	}
	function del()
	{
		$this->CheckAdminPrivs('htmlset');
		$flag = get('flag');
		logic('html')->delete($flag);
		$this->Messager('页面已经删除！', '?mod=html&code=front');
	}
}

?>