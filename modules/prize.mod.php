<?php

/**
 * 模块：抽奖相关
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package module
 * @name prize.mod.php
 * @version 1.1
 */

class ModuleObject extends MasterObject
{
	function ModuleObject( $config )
	{
		$this->MasterObject($config);
		if (MEMBER_ID < 1)
		{
			if (get('pid'))
			{
				header('Location: '.rewrite('?view='.get('pid')));
				exit;
			}
			$this->Messager(__('请先登录！'), '?mod=account&code=login');
		}
		$runCode = Load::moduleCode($this);
		$this->$runCode();
	}
	
	function Main()
	{
		header('Location: .');
	}
	function Sign()
	{
		$pid = get('pid', 'int');
				$prizes = logic('prize')->GetList($pid, user()->get('id'), 1);
		if ($prizes)
		{
						header('Location: '.rewrite('?mod=prize&code=view&pid='.$pid));
			exit;
		}
				$phone = logic('prize')->phone();
				$product = logic('product')->BuysCheck($pid, false);
		isset($product['false']) && $this->Messager($product['false']);
		include handler('template')->file('prize_sign');
	}
	function Iniz()
	{
		$pid = get('pid', 'int');
		$iz = logic('prize')->InizTicket($pid, user()->get('id'));
		if ($iz !== true)
		{
			$this->Messager($iz);
		}
		header('Location: '.rewrite('?mod=prize&code=view&pid='.$pid));
	}
	function View()
	{
		$pid = get('pid', 'int');
		$pid || exit('.O O. I need the Product-ID...');
		$prizes = logic('prize')->GetList($pid, user()->get('id'));
				$product = logic('product')->GetOne($pid);
		$product || exit('> _ < Product-ID invaid');
		$this->Title = $product['name'];
		include handler('template')->file('prize_view');
	}
	function Ajax_S2Phone()
	{
		$phone = get('phone', 'number');
		if (strlen($phone) != 11) exit('无效的手机号码！');
		$r = logic('prize')->S2Phone($phone);
		exit($r === true ? 'ok' : $r);
	}
	function Ajax_Vfcode()
	{
		$phone = get('phone', 'number');
		if (strlen($phone) != 11) exit('无效的手机号码！');
		$vcode = get('vcode', 'number');
		if (strlen($vcode) != 5) exit('无效的验证码！');
		$r = logic('prize')->Vfcode($phone, $vcode);
		exit($r === true ? 'ok' : $r);
	}
}

?>