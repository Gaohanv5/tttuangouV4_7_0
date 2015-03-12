<?php

/**
 * 模块：文章管理
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package module
 * @name article.mod.php
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
	public function main()
	{
		$this->CheckAdminPrivs('article');
		$articles = logic('article')->get_list();
		include handler('template')->file('@admin/articles_list');
	}
	
	public function create()
	{
		$this->CheckAdminPrivs('article');
		$article = array(
			'writer' => MEMBER_NAME
		);
		include handler('template')->file('@admin/article_modify');
	}
	
	public function modify()
	{
		$this->CheckAdminPrivs('article');
		$id = get('id', 'int');
		$article = logic('article')->get_one($id);
		include handler('template')->file('@admin/article_modify');
	}
	
	public function delete()
	{
		$this->CheckAdminPrivs('article');
		$id = get('id', 'int');
		logic('article')->delete($id);
		$this->Messager('删除成功！', '?mod=article');
	}
	
	public function save()
	{
		$this->CheckAdminPrivs('article');
		$title = post('title', 'string');
		$content = post('content');
		$writer = post('writer', 'string');
		if ($title && $content && $writer)
		{
			$id = post('id', 'int');
			if ($id)
			{
				logic('article')->update($id, $title, $content, $writer);
			}
			else
			{
				logic('article')->create($title, $content, $writer);
			}
			$this->Messager('保存成功！', '?mod=article');
		}
		else
		{
			$this->Messager('标题或者内容或者署名都不可以为空！', -1);
		}
	}
}

?>