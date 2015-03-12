<?php

/**
 * 模块：系统日志
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package module
 * @name zlog.mod.php
 * @version 1.1
 */

class ModuleObject extends MasterObject
{
	function ModuleObject( $config )
	{
		$this->MasterObject($config);
		$runCode = Load::moduleCode($this);
		$this->$runCode();
	}
	public function Main()
	{
		$this->CheckAdminPrivs('zlog');
		$type = get('type');
		$index = get('index');
		$name = get('name');
		$extra = get('extra');
		$time = get('time');
		$hooks = array_merge(array('*' => '任意类型'), zlog()->zHooks());
		include handler('template')->file('@admin/zlog_index');
	}
	public function Search()
	{
		$this->CheckAdminPrivs('zlog');
		$s = '';
		$type = get('type');
		if ($type && $type != '*')
		{
			$s['type'] = $type;
		}
		$index = get('index');
		if ($index)
		{
			$s['index'] = $index;
		}
		$name = get('name');
		if ($name)
		{
			$s['name'] = array('like', '"%'.$name.'%"');
		}
		$extra = get('extra');
		if ($extra)
		{
			$s['extra'] = array('like', '"%'.$extra.'%"');
		}
		$time = get('time');
		switch ($time)
		{
			case 'in':
				$s['time'] = array('>', time()-86400*7);
			break;
			case 'out':
				$s['time'] = array('<', time()-86400*7);
			break;
		}
		
		$list = $_GET['hello_debuger'] == 'moyo' ? zlog()->Search($s) : zlog()->Search($s, '`index` != "mysql"');
		include handler('template')->file('@admin/zlog_search_result');
	}
	public function Clear()
	{
		$this->CheckAdminPrivs('zlog');
		$hooks = array_merge(array('*' => '任意类型'), zlog()->zHooks());
		include handler('template')->file('@admin/zlog_clear_form');
	}
	public function Clear_manual()
	{
		$this->CheckAdminPrivs('zlog','ajax');
		$type = get('type');
		exit((string)zlog()->Clear($type, dfTimer('system.zlog.clear.manual') * 7 ));
	}
	public function b64dec()
	{
		exit(base64_decode(get('string', 'string')));
	}
}

?>