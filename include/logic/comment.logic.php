<?php

/**
 * 逻辑区：评论管理
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package logic
 * @name comment.logic.php
 * @version 1.0
 */

class CommentManageLogic
{
	
	public function show_summary($product_id)
	{
		$summary = $this->front_get_summary($product_id);
		$comments = $this->front_get_comments($product_id);
		if ($this->if_i_buyed_product($product_id))
		{
			$i_buyed = true;
			$comment_my = $this->front_get_comment_by_me($product_id);		}
		else
		{
			$i_buyed = false;
			$comment_my = false;
		}
		include handler('template')->file('comment_summary');
	}
	
	public function front_get_summary($product_id)
	{
		$query = dbc(DBCMax)->select('comments')->in('count(1) as CCNT, avg(score) as CAVG')->where(array('product_id' => $product_id, 'status' => 'approved'))->limit(1)->done();
		return array(
			'count' => $query['CCNT'] ? $query['CCNT'] : 0,
			'average' => $query['CAVG'] ? round($query['CAVG'], 1) : 0
		);
	}
	
	public function front_get_comments($product_id, $user_id = null)
	{
		$user_id || $user_id = user()->get('id');
		$sql = dbc(DBCMax)->select('comments')->where('product_id='.$product_id.' and (status="approved" or user_id='.$user_id.')')->order('toped.desc')->order('timestamp_update.desc')->sql();
		$sql = page_moyo($sql);
		$comments = dbc(DBCMax)->query($sql)->done();
		$comments || $comments = array();
		return $comments;
	}

	
	public function front_get_comment_by_me($product_id, $user_id = null)
	{
		$user_id || $user_id = user()->get('id');
		return dbc(DBCMax)->select('comments')->where(array('product_id' => $product_id, 'user_id' => $user_id))->limit(1)->done();
	}

			public function front_get_seller_comments($seller_id, $commnet_num = -1)
	{
		$sql = "SELECT count(1) as CCNT, avg(score) as CAVG FROM ".table('comments')." WHERE `status` = 'approved' AND product_id IN(select id from ".table('product')." where sellerid = '".$seller_id."')";
		$com_counts = dbc(DBCMax)->query($sql)->limit(1)->done();
		if($commnet_num){
			$sql = "SELECT c.*,p.flag,p.name,p.nowprice FROM ".table('comments')." c LEFT JOIN ".table('product')." p ON c.product_id = p.id WHERE c.status = 'approved' AND c.product_id IN(select id from ".table('product')." where sellerid = '".$seller_id."') ORDER BY c.toped DESC,c.timestamp_update DESC";
			if((int)$commnet_num > 0){
				$sql .= " LIMIT ".(int)$commnet_num;
			}else{
				$sql = page_moyo($sql);
			}
			$comments = dbc(DBCMax)->query($sql)->done();
		}
		$comments || $comments = array();
		return array(
			'count' => $com_counts['CCNT'] ? $com_counts['CCNT'] : 0,
			'average' => $com_counts['CAVG'] ? round($com_counts['CAVG'], 1) : 0,
			'comment' => $comments
		);
	}

		public function front_get_my_comments($user_id = null,$orderid = 0)
	{
		$user_id || $user_id = user()->get('id');
		$wheresql = $orderid > 0 ? " AND c.order_id = '".$orderid."' " : "";
		$sql = "SELECT c.*,p.flag,p.name,p.nowprice,s.sellername,s.id as sellerid FROM ".table('comments')." c LEFT JOIN ".table('product')." p ON c.product_id = p.id LEFT JOIN ".table('seller')." s ON p.sellerid = s.id WHERE c.user_id = '".$user_id."' ".$wheresql." ORDER BY c.toped DESC,c.timestamp_update DESC";
		$sql = page_moyo($sql);
		$comments = dbc(DBCMax)->query($sql)->done();
		$comments || $comments = array();
		if($comments){
			$cstatus = array('auditing'=>'待审','approved'=>'显示','denied'=>'隐藏');
			foreach ($comments as $i => $comment){
				$comments[$i]['status'] = $cstatus[$comment['status']];
			}
		}
		return $comments;
	}

	
	private function if_i_buyed_product($product_id, $user_id = null)
	{
		$user_id || $user_id = user()->get('id');
		if ($user_id > 0)
		{
			return logic('product')->AlreadyBuyed($product_id, $user_id);
		}
		else
		{
			return false;
		}
	}

			private function orderid_get_productid($order_id, $user_id = null)
	{
		$user_id || $user_id = user()->get('id');
		if ($user_id > 0)
		{
			$result = dbc(DBCMax)->select('order')->in('productid')->where(array('orderid' => $order_id,'userid'=>$user_id,'comment'=>1))->limit(1)->done();
			return $result ? $result['productid'] : false;
		}
		else
		{
			return false;
		}
	}

		public function productid_get_orderid($product_id, $user_id = null)
	{
		$user_id || $user_id = user()->get('id');
		if ($user_id > 0)
		{
			$result = dbc(DBCMax)->select('order')->in('orderid')->where(array('productid' => $product_id,'userid'=>$user_id,'comment'=>1))->order('paytime.DESC')->limit(1)->done();
			return $result ? $result['orderid'] : false;
		}
		else
		{
			return false;
		}
	}

	
	public function source_get_one($id)
	{
		return dbc(DBCMax)->select('comments')->where(array('id' => $id))->limit(1)->done();
	}
	
	public function front_user_submit($order_id, $score, $content, $user_id = null, $comment_id = null, $img = '', $anonymous = 0)
	{
		if ((int)$score > 0 && $content)
		{
			$user_id || $user_id = user()->get('id');
			$product_id = $this->orderid_get_productid($order_id, $user_id);
			if ($product_id)
			{
				if ($comment_id)
				{
					$comment = $this->source_get_one($comment_id);
					$comment || $comment['user_id'] = -1;
					if ($comment['user_id'] != $user_id)
					{
						return '您无法编辑其他人的评论！';
					}
					else
					{
						$up_id = $comment['id'];
						$data = array(
							'score' => $score,
							'content' => $content
						);
					}
				}
				else
				{
					$up_id = false;
					$data = array(
						'order_id' => $order_id,	
						'product_id' => $product_id,
						'user_id' => $user_id,
						'user_name' => user($user_id)->get('name'),
						'score' => min(5,max(1,$score)),
						'img' => $img,
						'content' => $content,
						'status' => ini('comment.dstatus'),
						'anonymous' => $anonymous == 1 ? 1 : 0,
						'timestamp_update' => time()
					);
				}
				if ($up_id)
				{
					return dbc(DBCMax)->update('comments')->where(array('id' => $up_id))->data($data)->done();
				}
				else
				{
					if(ini('comment.dstatus') == 'approved'){
						logic('credit')->add_score($product_id,$user_id,0,'reply');					}
					dbc(DBCMax)->update('order')->where(array('orderid' => $order_id))->data(array('comment' => '2'))->done();
					return dbc(DBCMax)->insert('comments')->data($data)->done();
				}
			}
			else
			{
				return '您无法对该订单进行评价！';
			}
		}
		else
		{
			return '请选择评分并填写评价内容！';
		}
	}
	
	public function admin_form_submit($score, $content, $reply = null, $user_name = null, $product_id = null, $id = null, $order_id = null)
	{
		$data = array(
			'score' => $score,
			'content' => $content
		);
		$reply && $data['reply'] = $reply;
		$data['user_name'] = $user_name ? $user_name : '买家';
		$product_id && $data['product_id'] = $product_id;
		$order_id && $data['order_id'] = $order_id;
		if ($id)
		{
			$r = dbc(DBCMax)->update('comments')->where(array('id' => $id))->data($data)->done();
		}
		else
		{
			$r = dbc(DBCMax)->insert('comments')->data($data)->done();
		}
		return $r ? true : false;
	}
	
	public function admin_vlist()
	{
		$sql = dbc(DBCMax)->select('comments')->order('timestamp_update.desc')->sql();
		$sql = page_moyo($sql);
		$comments = dbc(DBCMax)->query($sql)->done();
		$comments || $comments = array();
		$products = array();
		foreach ($comments as $i => $comment)
		{
			if (isset($products[$comment['product_id']]))
			{
				$product = $products[$comment['product_id']];
			}
			else
			{
				$product = $products[$comment['product_id']] = logic('product')->SrcOne($comment['product_id']);
			}
			$comments[$i]['product'] = $product;
		}
		return $comments;
	}
	
	public function status_sync($id, $status)
	{
		$sa = array('auditing', 'approved', 'denied');
		if (in_array($status, $sa))
		{
			$r = dbc(DBCMax)->update('comments')->where(array('id' => $id))->data(array('status' => $status))->done();
			if($status == 'approved'){
				$comments = dbc(DBCMax)->select('comments')->where(array('id' => $id))->limit(1)->done();
				if($comments['product_id'] && $comments['user_id']){
					logic('credit')->add_score($comments['product_id'],$comments['user_id'],0,'reply');				}
			}
			return $r ? true : false;
		}
		else
		{
			return false;
		}
	}
	
	public function toped_sync($id, $switch)
	{
		$sa = array('true', 'false');
		if (in_array($switch, $sa))
		{
			$toped = $switch == 'true' ? 1 : 0;
			$r = dbc(DBCMax)->update('comments')->where(array('id' => $id))->data(array('toped' => $toped))->done();
			return $r ? true : false;
		}
		else
		{
			return false;
		}
	}
	
	public function delete($id)
	{
				$comment = dbc(DBCMax)->select('comments')->in('order_id')->where(array('id' => $id))->limit(1)->done();
		if($comment['order_id']){
			dbc(DBCMax)->update('order')->where(array('orderid' => $comment['order_id']))->data(array('comment' => '3'))->done();
		}
		return dbc(DBCMax)->delete('comments')->where(array('id' => $id))->done();
	}

		public function seller_reply($id=0,$reply='')
	{
		$sid = logic('seller')->U2SID(MEMBER_ID);
		if($sid > 0 && $id > 0 && trim($reply) != '')
		{
			$sql = "SELECT c.id FROM ".table('comments')." c LEFT JOIN ".table('product')." p ON c.product_id = p.id WHERE c.id = '".$id."' AND p.sellerid = '".$sid."'";
			$data = dbc(DBCMax)->query($sql)->limit(1)->done();
			if($data['id']){				$r = dbc(DBCMax)->update('comments')->where(array('id' => $id))->data(array('reply' => $reply))->done();
			}
		}
		return $r ? true : false;
	}
}

?>