<?php

/**
 * 逻辑区：文章管理
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package logic
 * @name article.logic.php
 * @version 1.0
 */

class ArticleManageLogic
{
	
	public function get_list($limit = 0)
	{
		if ($limit) {
			$sql = dbc(DBCMax)->select('article')->order('timestamp_create.desc')->limit($limit);
		}else{
			$sql = dbc(DBCMax)->select('article')->order('timestamp_create.desc');
		}
		$sql = dbc(DBCMax)->sql($sql);
		if (!$limit) $sql = page_moyo($sql);
		$results = dbc(DBCMax)->query($sql)->done();
		return $results;
	}
	
	public function get_one($id)
	{
		return dbc(DBCMax)->select('article')->where(array('id' => $id))->limit(1)->done();
	}
	
	public function create($title, $content, $writer)
	{
		return dbc(DBCMax)->insert('article')->data(array(
			'title' => $title,
			'content' => $content,
			'writer' => $writer,
			'author_id' => MEMBER_ID,
			'timestamp_create' => time()
		))->done();
	}
	
	public function update($id, $title, $content, $writer)
	{
		return dbc(DBCMax)->update('article')->data(array('title' => $title, 'content' => $content, 'writer' => $writer))->where(array('id' => $id))->done();
	}
	
	public function delete($id)
	{
		return dbc(DBCMax)->delete('article')->where(array('id' => $id))->done();
	}
}

?>