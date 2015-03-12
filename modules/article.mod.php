<?php

/**
 * 模块：文章展示
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package module
 * @name article.mod.php
 * @version 1.1
 */

class ModuleObject extends MasterObject
{
	function ModuleObject( $config )
	{
		$this->MasterObject($config);
		$runCode = Load::moduleCode($this, false, false);
		$this->$runCode();
	}
	public function main()
	{
		$this->Title = '文章列表';
		$articles = logic('article')->get_list();
		include handler('template')->file('article_list');
	}
	public function view()
	{
		$id = get('id', 'int');
		$article = logic('article')->get_one($id);
		$this->Title = $article['title'];
		include handler('template')->file('article_view');
	}
}

?>