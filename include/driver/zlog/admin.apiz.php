<?php

/**
 * ZLOG-APIZ：管理员相关
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package zlog
 * @name admin.apiz.php
 * @version 1.0
 */

class adminZLOG extends iMasterZLOG
{
	protected $zlogType = 'admin';
	public function login($loginR, $username = '', $password = '')
	{
		$username = thtmlspecialchars($username);
		$password = thtmlspecialchars($password);
		if ($loginR == -1)
		{
			$name = '管理员（'.$username.'）登录失败，密码错误';
			$extra = '尝试使用密码 <b>'.$this->mosaic($password).'</b> 登录失败！';
		}
		elseif ($loginR == 0)
		{
			$name = '管理员登录失败，账户不存在';
			$extra = '登录用户名：'.$username.'<br/>登录密码：'.$this->mosaic($password);
		}
		elseif ($loginR == 1)
		{
			$name = '管理员（'.$username.'）登录成功，已经进入后台';
			$extra = '';
		}
		$this->zlogCreate('system', $name, $extra);
	}
	public function roleChange($uid, $roleDATA)
	{
		$roleType = user($uid)->get('role_type');
		if ($roleType != $roleDATA['type'])
		{
			if ($roleType == 'normal' && $roleDATA['type'] == 'admin')
			{
				$name = '普通用户（'.user($uid)->get('name').'）已被分配到管理组（'.$roleDATA['name'].'）';
			}
			elseif ($roleType == 'admin' && $roleDATA['type'] == 'normal')
			{
				$name = '管理组成员（'.user($uid)->get('name').'）已被降级到普通用户组（'.$roleDATA['name'].'）';
			}
			$name && $this->zlogCreate('system', $name);
		}
	}
	private function mosaic($string)
	{
		$len = strlen($string);
		$keep = intval($len / 4);
		$keep < 2 && $keep = 2;
		$keep_f = intval($keep / 2);
		$keep_e = $keep - $keep_f;
		$ms = $len - $keep;
		$mosaic = substr($string, 0, $keep_f).str_repeat('*', $ms).substr($string, $len - $keep_e, $keep_e);
		return $mosaic;
	}
}

?>