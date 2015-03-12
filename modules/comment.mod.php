<?php

/**
 * 模块：评论相关
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package module
 * @name comment.mod.php
 * @version 1.1
 */

class ModuleObject extends MasterObject
{
	function ModuleObject( $config )
	{
		$this->MasterObject($config);
		if (MEMBER_ID < 1)
        {
            $this->Messager(__('请先登录！'), '?mod=account&code=login');
        }
		$runCode = Load::moduleCode($this, false, false);
		$this->$runCode();
	}

	function Main()
	{
		$order_id = get('oid', 'number');
		$order_cid = get('coid', 'number');
		if($order_id){
			$order = logic('order')->GetOne($order_id);
			if($order && $order['userid'] == MEMBER_ID && $order['comment'] == 1){
				$this->Title = '评价产品';
				$product = $order['product'];
				unset($order);
				include handler('template')->file('comment');
			}else{
				$this->Messager('操作错误！','?mod=me&code=order&comment=1');
			}
		}else{
			$this->Title = '我给商家的评价';
			$comments = logic('comment')->front_get_my_comments(MEMBER_ID,$order_cid);
			include handler('template')->file('comment_my');
		}
    }

	
	public function submit()
	{
		$order_id = get('oid', 'number');
		$score = post('score', 'int');
		$content = strip_tags(post('content', 'txt'));
		$anonymous = post('anonymous', 'int');
		$img = '';
		if(trim($content) == ''){
			$this->Messager('请输入评价内容！');
		}
		if (isset($_FILES['commentimg']['name']) && $_FILES['commentimg']['error'] == 0){
			$uploadimg = logic('upload')->Save('commentimg', false, false);
			if($uploadimg['id']){
				$img = $uploadimg['id'];
			}
		}
		$result = logic('comment')->front_user_submit($order_id, $score, $content, MEMBER_ID, NULL, $img, $anonymous);
		if (is_numeric($result))
		{
			$this->Messager('评价成功！','?mod=comment');
		}
		else
		{
			$this->Messager('评价失败！'.$result);
		}
	}

		public function ajaxsubmit()
	{
		$id = post('id','int');
		$reply = trim(strip_tags(post('reply')));
		$retrun = logic('comment')->seller_reply($id, $reply);
	}
}

?>