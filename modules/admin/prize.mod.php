<?php

/**
 * 模块：抽奖管理
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package module
 * @name prize.mod.php
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
	function Main()
	{
		$this->CheckAdminPrivs('prize');
		header('Location: ?mod=product&code=vlist');
	}
	function vList()
	{
		$this->CheckAdminPrivs('prize');
		logic('product')->Maintain();
				$list = logic('product')->GetList(-1, null, 'type="prize"');
				include handler('template')->file('@admin/product_prize_list');
	}
	function Mgr()
	{
		$this->CheckAdminPrivs('prize');
		$pid = get('pid', 'int');
		$product = logic('product')->GetOne($pid);
		$pzwin = logic('prize')->PrizeWIN($pid);
		if ($pzwin)
		{
			$smsContent = user($pzwin['uid'])->get('name').'，您好，您在我们网站参与的活动有好结果了，恭喜您。详情请登录查看：'.ini('settings.site_url');
			$broadcastContent = '您好，您在我们网站参与的活动结果已出，赶快来看看吧！详情请登录：'.ini('settings.site_url');
		}
		include handler('template')->file('@admin/prize_mgr');
	}
	function Ajax_query()
	{
		$this->CheckAdminPrivs('prize','ajax');
		$pid = get('pid', 'int');
		$ticket = get('ticket', 'int');
		$prize = logic('prize')->Query('pid='.$pid.' AND number='.$ticket);
		if (!$prize)
		{
			echo('查询失败，找不到相关用户！<br/>失败原因：<br/>1，您输入的中奖号码不在可控范围内；<br/>2，您设置了虚拟购买人数，此中奖号码为虚拟占位号码；<br/>：：：不过，您仍然可以公开此中奖号码');
		}
		else
		{
			$u = user($prize['uid']);
			$phoneDATA = logic('prize')->GetPhone('uid', $u->get('id'));
			echo '用户名：'.$u->get('name').'<br/>手机：'.$phoneDATA['phone'].'<br/>邮箱：'.$u->get('email').'<br/>QQ：'.$u->get('qq').'';
			echo '<br/>';
			echo '抽奖号备注：'.$prize['remark'];
		}
		echo '<br/>';
		exit('<input type="button" onclick="public_prize_win()" value="公开中奖号码" />');
	}
	function Ajax_publish()
	{
		$this->CheckAdminPrivs('prize','ajax');
		$pid = get('pid', 'int');
				$pzwin = logic('prize')->PrizeWIN($pid);
		if ($pzwin)
		{
			exit('此抽奖活动已经公开过中奖号码，无法再次提交！');
		}
		$ticket = get('ticket', 'int');
		$r = logic('prize')->PrizePUB($pid, $ticket);
		exit($r === true ? 'ok' : $r);
	}
	function Ajax_notify()
	{
		$this->CheckAdminPrivs('prize','ajax');
		$phone = get('phone', 'number');
		$content = post('content', 'txt');
		logic('push')->addi('sms', $phone, array('content'=>$content));
		exit('ok');
	}
	function Ajax_broadcast()
	{
		$this->CheckAdminPrivs('prize','ajax');
		$pid = get('pid', 'int');
		$excUID = get('excuid', 'int');
		$content = post('content', 'txt');
		$phones = logic('prize')->GetPhoneList($pid, ';', 'uid!='.$excUID);
		logic('push')->add('sms', $phones, array('content'=>$content));
		exit('ok');
	}
	function nums_list()
	{
		$this->CheckAdminPrivs('prize');
		$pid = get('pid', 'int');
		$prizes = logic('prize')->GetList($pid, false, false, 'number.DESC', true);
		include handler('template')->file('@admin/prize_nums_list');
	}
}
?>