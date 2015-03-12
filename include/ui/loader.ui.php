<?php

/**
 * 界面支持：资源加载器
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package UserInterface
 * @name loader.ui.php
 * @version 1.0
 */

class LoaderUI
{
	
	private $__loaded_list = array();
	private $__addon_map = array();
	private $__build = '';
	public function __construct()
	{
		$this->__addon_map = array(
			'picker.date' => '@DatePicker/WdatePicker',
			'editor.kind' => array(
				'css' => array('@KindEditor/themes/default/default', '@KindEditor/themes/simple/simple'),
				'js' => array('@KindEditor/kindeditor', '@KindEditor/lang/zh_CN'),
			),
			'uploader.swf' => array(
				'css' => array('@SwfUploader/swfupload'),
				'js' => array('@SwfUploader/swfobject', '@SwfUploader/swfupload')
			),
			'dialog.art' => '@artDialog/jquery.artDialog?skin=default',
			'dialog.art.iframe' => '@artDialog/artDialog.iframeTools'
		);
		$this->__build = (int) strstr(SYS_BUILD, ' ');
		if($this->__build < 1) {
			$this->__build = date('Ymd');
		}
	}
	function js($name, $once = false, $dirSAt = 'static/js')
	{
		list($name, $loc) = $this->__GetLoc($name, 'js', $dirSAt);
		$extend = '';
		if (strstr($name, '?'))
		{
			$extend = strstr($name, '?');
			$name = substr($name, 0, -strlen($extend));
		}
		$file = ROOT_PATH.$loc.$name.'.js';
		$url = ini('settings.site_url').$loc.$name.'.js';
		if (!is_file($file)) return '';
		if ($this->loaded('js_'.$name)) return '';
		if ($once)
		{
			$this->loaded('js_'.$name, true);
		}
		return '<script type="text/javascript" src="'.$url.$extend.(empty($extend) ? '?' . $this->__build : '').'"></script>';
	}
	function css($name, $once = false, $dirSAt = 'static/css')
	{
		list($name, $loc) = $this->__GetLoc($name, 'styles', $dirSAt);
		$file = ROOT_PATH.$loc.$name.'.css';
		if (!is_file($file)){
			$file = str_replace(ini('settings.template_path'),'default',$file);
			$loc = str_replace(ini('settings.template_path'),'default',$loc);
		}
		$url = ini('settings.site_url').$loc.$name.'.css?' . $this->__build;
		if (!is_file($file)) return '';
		if ($this->loaded('css_'.$name)) return '';
		if ($once)
		{
			$this->loaded('css_'.$name, true);
		}
		return '<link href="'.$url.'" rel="stylesheet" type="text/css" />';
	}
	function addon($flag)
	{
		$dirSAt = 'static/addon';
		$map = $this->__addon_map[$flag];
		if (is_string($map))
		{
						return $this->js($map, false, $dirSAt);
		}
		$html = '';
		foreach ($map as $type => $list)
		{
			foreach ($list as $i => $idx)
			{
				$html .= $this->$type($idx, false, $dirSAt);
			}
		}
		return $html;
	}
	private function __GetLoc($name, $dirCom, $dirSAt)
	{
		$template_path = ini('settings.template_root_path').ini('settings.template_path').'/'.$dirCom.'/';
		if (substr($name, 0, 1)=='@')
		{
			$name = substr($name, 1);
			$template_path = './'.$dirSAt.'/';
		}
		elseif (substr($name, 0, 1)=='#')
		{
			$name = substr($name, 1);
			$template_path = ini('settings.template_root_path');
		}
		if (substr($template_path, 0, 1)=='.')
		{
			$template_path = substr($template_path, 1);
		}
		return array($name, $template_path);
	}
	private function loaded($name, $flag = false)
	{
		if (!$flag)
		{
			return isset($this->__loaded_list[$name]) ? true : false;
		}
		return $this->__loaded_list[$name] = true;
	}
}

?>