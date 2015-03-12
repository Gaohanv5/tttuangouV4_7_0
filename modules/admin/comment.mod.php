<?php

/**
 * 模块：评论管理
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package module
 * @name comment.mod.php
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
	
	public function vlist()
	{
		$this->CheckAdminPrivs('comments');
		$comments = logic('comment')->admin_vlist();
		include handler('template')->file('@admin/comments_list');
	}
	
	public function status_sync()
	{
		$this->CheckAdminPrivs('comments','ajax');
		$id = get('id', 'int');
		$status = get('status', 'txt');
		$r = logic('comment')->status_sync($id, $status);
		exit($r ? 'ok' : 'error');
	}
	
	public function toped_sync()
	{
		$this->CheckAdminPrivs('comments','ajax');
		$id = get('id', 'int');
		$switch = get('switch', 'txt');
		$r = logic('comment')->toped_sync($id, $switch);
		exit($r ? 'ok' : 'error');
	}
	
	public function ajax_modify()
	{
		$this->CheckAdminPrivs('comments','ajax');
		$id = get('id', 'int');
		$comment = logic('comment')->source_get_one($id);
		include handler('template')->file('@admin/comments_mgr_ajax');
	}
	
	public function ajax_submit()
	{
		$this->CheckAdminPrivs('comments','ajax');
		$id = post('id', 'int');
		$order_id = post('order_id', 'int');
		$product_id = post('product_id', 'int');
		$score = post('score', 'int');
		$content = post('content', 'string');
		$reply = post('reply', 'string');
		$user_name = post('user_name', 'txt');
		$r = logic('comment')->admin_form_submit($score, $content, $reply, $user_name, $product_id, $id, $order_id);
		exit($r ? 'ok' : 'error');
	}
	
	public function ajax_delete()
	{
		$this->CheckAdminPrivs('comments','ajax');
		$id = get('id', 'int');
		logic('comment')->delete($id);
		exit('<script type="text/javascript">comment_delete_result("ok", '.$id.');</script>');
	}
	
	public function ajax_view()
	{
		$this->CheckAdminPrivs('comments','ajax');
		$id = get('id', 'int');
		$comment = logic('comment')->source_get_one($id);
		if($comment['img']){
			$comment['content'] .= "<br><img src='".imager($comment['img'], IMG_Normal)."'>";
		}
		exit($comment['content']);
	}
	
	public function config()
	{
		$this->CheckAdminPrivs('comments');
		$config = ini('comment');
		include handler('template')->file('@admin/comments_config');
	}
	
	public function config_save()
	{
		$this->CheckAdminPrivs('comments');
		$config = post('config');
		ini('comment', $config);
		$this->Messager('设置保存成功！', '?mod=comment&code=config');
	}
}

?>