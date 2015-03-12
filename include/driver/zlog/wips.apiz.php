<?php

/**
 * ZLOG-APIZ：入侵检测
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package zlog
 * @name wips.apiz.php
 * @version 1.0
 */

class wipsZLOG extends iMasterZLOG
{
	protected $zlogType = 'wips';
	
	public function sql($r, $m)
	{
		$m = '<b64>'.base64_encode($m).'</b64>';
		$this->zlogCreate('sql', '发现SQL注入攻击', '结果：<font color="green">拦截成功</font><br/>类型：'.$r.'<br/>关键字：'.$m.'');
	}
}

?>