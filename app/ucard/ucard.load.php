<?php

/**
 * 应用：用户名片
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package app
 * @name ucard.load.php
 * @version 1.0
 */

class UserCardAPP
{
	public function init()
	{
		echo ui('loader')->css('@ucard', true, 'app/ucard/resource');
		echo ui('loader')->js('@ucard.master', true, 'app/ucard/resource');
		echo ui('loader')->js('@ucard.user.ajax', true, 'app/ucard/resource');
	}
	public function load($uid)
	{
		$uName = user($uid)->get('name');
		$uid > 0 || $uid = 'guest';
		return '<font class="ucard" uid="'.$uid.'">'.$uName.'</font>';
	}
	public function ajax()
	{
		$uid = get('uid', 'int');
		$user = user($uid)->get();
		include handler('template')->file('#app/ucard/resource/ajax');
	}
	private function ajax_response($status, $msg)
	{
		exit(jsonEncode(array('status'=>$status,'msg'=>$msg)));
	}
	public function recharge()
	{
		if(false === admin_priv('quickrecharge')) {
			$this->ajax_response('err', 'forbidden');
		}
		$uid = get('uid', 'int');
		$money = get('money', 'float');
		$remark = get('remark', 'txt');
		$r = logic('me')->money()->add($money, $uid, array('name' => '管理员后台充值', 'intro' => $remark));
		$r ? $this->ajax_response('ok', '充值成功！') : $this->ajax_response('err', '充值失败！');
	}
	public function lessmoney()
	{
		if(false === admin_priv('quickrecharge')) {
			$this->ajax_response('err', 'forbidden');
		}
		$uid = get('uid', 'int');
		$money = get('money', 'float');
		$remark = get('remark', 'txt');
		$r = logic('me')->money()->less($money, $uid, array('name' => '管理员后台扣费', 'intro' => $remark));
		$r ? $this->ajax_response('ok', '扣费成功！') : $this->ajax_response('err', '扣费失败！');
	}
	public function send_mail()
	{
		$uid = get('uid', 'int');
		$title = get('title', 'txt');
		if (trim($title) == '') $this->ajax_response('err', '请输入邮件标题！');
		$content = get('content');
		if (trim($content) == '') $this->ajax_response('err', '请输入邮件内容！');
		logic('push')->addi('mail', user($uid)->get('email'), array('subject'=>$title,'content'=>$content));
		$this->ajax_response('ok', '邮件发送完成！');
	}
	public function send_sms()
	{
		$uid = get('uid', 'int');
		$content = get('content', 'txt');
		if (trim($content) == '') $this->ajax_response('err', '请输入短信内容！');
		logic('push')->addi('sms', user($uid)->get('phone'), array('content'=>$content));
		$this->ajax_response('ok', '短信发送完成！');
	}
}

?>