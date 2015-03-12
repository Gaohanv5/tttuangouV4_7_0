<?php

/**
 * 模块：通知方式管理
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package module
 * @name notify.mod.php
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
		$this->CheckAdminPrivs('notify');
		$list = ini('notify.api');
		include handler('template')->file('@admin/notify_list');
	}
	function Config()
	{
		$this->CheckAdminPrivs('notify');
		$flag = get('flag', 'txt');
		if (in_array($flag, array('mail', 'sms')))
		{
			header('Location: ?mod=service&code='.$flag);
		}
		$file = DRIVER_PATH.'notify/'.$flag.'.config.php';
		if (!is_file($file))
		{
			$this->Messager('此通知方式没有配置项！');
		}
		else
		{
			include handler('template')->absfile($file);
		}
	}
	function Online()
	{
		$this->CheckAdminPrivs('notify');
		$flag = get('flag', 'txt');
		$power = get('power', 'txt');
		$tf = ($power == 'on') ? true : false;
		ini('notify.api.'.$flag.'.enabled', $tf);
		$this->Messager('更新完成！');
	}
	function Save()
	{
		$this->CheckAdminPrivs('notify');
		$flag = post('flag', 'txt');
		$config = post('cfg');
		$config['enabled'] = ($config['enabled'] == 'on') ? true : false;
		ini('notify.api.'.$flag, $config);
		$this->Messager('修改完成！');
	}
	function Event()
	{
		$this->CheckAdminPrivs('notifyevent');
		$api = ini('notify.api');
		$apiTitles = '';
		foreach ($api as $i => $one)
		{
			if (!$one['enabled'])
			{
				unset($api[$i]);
			}
			else
			{
				$apiTitles .= '<td width="10%">'.$one['name'].'</td>';
			}
		}
		$colspan = count($api)+4;
		$list = ini('notify.event');
		foreach ($list as $name => $cfg)
		{
			$string = '';
			foreach ($api as $flag => $one)
			{
				$status = 'enable';
				if (!$cfg['hook'][$flag]['enabled'])
				{
					$status = 'disable';
				}
				$string .= '<td><img class="'.$status.'" title=".event.'.$name.'.hook.'.$flag.'.enabled" /><img class="editor" title="'.$name.'.msg.'.$flag.'" /></td>';
			}
			$list[$name]['_apis'] = $string;
		}
		$listener = ini('notify.listener') ? 'enable' : 'disable';
		$extend = '';
		
				$etsCheck = $this->ETS_iCheck();
		include handler('template')->file('@admin/notify_events'.$extend);
	}
	function Event_switch()
	{
		$this->CheckAdminPrivs('notifyevent','ajax');
		$hook = get('hook', 'txt');
		$power = get('power', 'txt');
		$tf = ($power == 'enable') ? true : false;
		ini('notify'.$hook, $tf);
		exit($power);
	}
	function Event_rename()
	{
		$this->CheckAdminPrivs('notifyevent','ajax');
		$hook = get('hook', 'txt');
		$name = get('name', 'txt');
		ini('notify.event.'.$hook.'.name', $name);
		exit('ok');
	}
	function Event_msg()
	{
		$this->CheckAdminPrivs('notifyevent','ajax');
		$hook = get('hook', 'txt');
		list($event, $flag) = explode('.msg.', $hook);
		$struct = ini('notify.event.'.$event.'.struct');
		$msg = ini('notify.event.'.$event.'.msg.'.$flag);
		$ops = array(
			'status' => 'ok',
			'name' => ini('notify.event.'.$event.'.name'),
			'msg' => $msg,
			'al2user' => ini('notify.event.'.$event.'.cfg.'.$flag.'.al2user') ? true : false,
			'cc2admin' => ini('notify.event.'.$event.'.cfg.'.$flag.'.cc2admin') ? true : false,
			'tags' => $this->ETS_Parser($event, $struct)
		);
		exit(jsonEncode($ops));
	}
	function Event_save()
	{
		$this->CheckAdminPrivs('notifyevent','ajax');
		$hook = get('hook', 'txt');
		$msg = post('msg', 'txt');
		$al2user = post('al2user', 'txt');
		$cc2admin = post('cc2admin', 'txt');
		ini('notify.event.'.$hook, $msg);
				$hookc = str_replace('.msg.', '.cfg.', $hook);
		$tf = ($cc2admin == 'true') ? true : false;
		ini('notify.event.'.$hookc.'.cc2admin', $tf);
		$tf = ($al2user == 'true') ? true : false;
		ini('notify.event.'.$hookc.'.al2user', $tf);
				logic('notify')->Clears();
		exit('ok');
	}
	function Event_test()
	{
		$this->CheckAdminPrivs('notifyevent','ajax');
		logic('notify')->Call(user()->get('id'), 'admin.mod.notify.Event.test', date('Y-m-d H:i:s'));
		exit('ok');
	}
	function Event_delete()
	{
		$this->CheckAdminPrivs('notifyevent','ajax');
		$event = get('hook', 'txt');
		ini('notify.event.'.$event, INI_DELETE);
		exit('ok');
	}
	function Tag_clear()
	{
		$hook = get('hook', 'txt');
		list($event) = explode('.', $hook);
		ini('notify.event.'.$event.'.struct', '');
		exit('ok');
	}
	function AdminID_save()
	{
		$this->CheckAdminPrivs('notify');
		$adminid = post('adminid', 'int');
		if (!$adminid)
		{
			$this->Messager('请输入有效的管理员ID！');
		}
		else
		{
			ini('notify.adminid', $adminid);
			$this->Messager('保存完成！');
		}
	}
	function ETS_Parser($event, $struct)
	{
		$tags = explode(',', $struct);
		$data = $this->ETS_Storage();
		$lang = array_merge($data['~common'] ? $data['~common'] : array(), isset($data[$event]) ? $data[$event] : array());
		$array = array();
		foreach ($tags as $i => $tag)
		{
			if (!$tag) continue;
			$array[] = array(
				'src' => $tag,
				'name' => isset($lang[$tag]) ? $lang[$tag] : $tag
			);
		}
		return $array;
	}
	function ETS_Storage($data = false, $build = '')
	{
		$file = DATA_PATH.'notify.ets.lang.cache.php';
		if (!$data)
		{
			return (is_file($file)) ? include($file) : array();
		}
		$data['__AW_upTime__'] = date('Y-m-d H:i:s');
		$write = '<?php return '.var_export($data, true).'; ?>';
		file_put_contents($file, $write);
		if ($build != '')
		{
			ini('notify.upcheck.ets.lang.build', $build);
		}
		return $data;
	}
	function ETS_iCheck($flag = false)
	{
		$ckey = 'notify.event.tags.lang.upcheck';
		if ($flag)
		{
			fcache($ckey, $flag);
			return $flag;
		}
		$etsCheck = true;
		if (fcache($ckey, dfTimer('com.notify.etl.upcheck')))
		{
			$etsCheck = false;
		}
		return $etsCheck;
	}
	function ETS_iServer($action = 'check')
	{
				$server = base64_decode('aHR0cDovL3NlcnZlci50dHR1YW5nb3UubmV0L3Byb2Nlc3Nvci9ldHMvdXBkYXRlLnBocA==').'?charset='.ini('settings.charset').'&';
		return dfopen($server.'do='.$action, 10485760, '', '', true, 10, 'CENWOR.TTTG.ETS.UPC.AGENT.'.SYS_VERSION.'.'.SYS_BUILD);
	}
	function ETS_Check()
	{
		$base = $this->ETS_iServer('check');
		$last = ini('notify.upcheck.ets.lang.build');
		if ($base != $last)
		{
			exit($base);
		}
		else
		{
			exit($this->ETS_iCheck('noups'));
		}
	}
	function ETS_Update()
	{
		$lang = $this->ETS_iServer('update');
		preg_match_all('/<(.*?)>(.*?)<\/(.*?)>/ims', $lang, $subs);
		if (count($subs[0]) > 0)
		{
						$area = $subs[1];
			$defs = $subs[2];
			$store = array();
			foreach ($area as $i => $name)
			{
				$name = ($name == '__common__') ? '~common' : $name;
				$langd = explode("\n", $defs[$i]);
				$array = array();
				foreach ($langd as $ii => $def)
				{
					$def = trim($def);
					if (!$def) continue;
					list($key, $val) = explode('=', $def);
					$array[trim($key)] = trim($val);
				}
				$store[$name] = $array;
			}
			preg_match('/::([a-z0-9]{32})::/i', $lang, $mach);
			$build = $mach[1];
			$this->ETS_Storage($store, $build);
		}
		exit('ok');
	}
}

?>