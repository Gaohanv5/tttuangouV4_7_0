<?php

/**
 * 模块：多风格管理
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package module
 * @name styles.mod.php
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
	
	public function temp()
	{
		$this->CheckAdminPrivs('templateset');
		$action = "admin.php?mod=styles&code=settemp";
		include(ROOT_PATH.'templates/tplinfo.php');
		$temp_list = $templates;
		$this->FormHandler = new FormHandler();
		include(CONFIG_PATH.'settings.php');
		$temp_select = $this->FormHandler->Select('temp_id', $temp_list, $config['settings']['template_path']);
		include handler('template')->file('@admin/styles_temp');
	}
	
	public function vlist()
	{
		$this->CheckAdminPrivs('styles');
		$default = ini('styles.default');
		$styles = ui('style')->get_all();
		include handler('template')->file('@admin/styles_list');
	}
	
	public function setdefault()
	{
		$this->CheckAdminPrivs('styles');
		$id = get('id');
		if (ini('styles.local.'.$id.'.enabled'))
		{
			ini('styles.default', $id);
			$this->Messager('默认风格设置成功！', '?mod=styles&code=vlist');
		}
		else
		{
			$this->Messager('默认风格设置失败，此风格未启用，或者不存在！', '?mod=styles&code=vlist');
		}
	}
	
	public function settemp()
	{
		$this->CheckAdminPrivs('templateset');
		$result = @is_dir(ROOT_PATH.'templates/'.$this->Post['temp_id']);
		if ($result)
		{
			include(CONFIG_PATH.'settings.php');
			$new_config = $config['settings'];
			$new_config['template_path'] = $this->Post['temp_id'];
			ini('settings', $new_config);
			$this->Messager('默认模板设置成功！', '?mod=styles&code=temp');
		}
		else
		{
			$this->Messager('默认模板设置失败，此模板文件不存在！', '?mod=styles&code=temp');
		}
	}
}

?>