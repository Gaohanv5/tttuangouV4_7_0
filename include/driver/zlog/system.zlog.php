<?php

/**
 * ZLOG-SYS：主控制类
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package zlog
 * @name system.zlog.php
 * @version 1.1
 */

class iSystemZLOG
{
	
	public function Search($filter, $extSQL = '1')
	{
		return dbc(DBCMax)->query(page_moyo(dbc(DBCMax)->select('zlog')->where($filter)->where($extSQL)->order('time.DESC')->sql()))->done();
	}
	
	public function zHooks()
	{
		return array(
			'product' => '产品相关',
			'coupon' => TUANGOU_STR . '券相关',
			'admin' => '管理员相关',
			'error' => '错误报告',
			'wips' => '入侵检测'
		);
	}
	
	public function Clear($type, $timeBefore)
	{
		$where = array();
		$type != '*' && $where['type'] = $type;
		$where['time'] = array('<', time() - (float)$timeBefore);
		return dbc(DBCMax)->delete('zlog')->where($where)->done();
	}
}

?>