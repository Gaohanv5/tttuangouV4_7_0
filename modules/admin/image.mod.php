<?php

/**
 * 模块：图片管理
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package module
 * @name image.mod.php
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
	
	public function watermark()
	{
		$this->CheckAdminPrivs('watermark');
		$cfg = ini('image.watermark');
				$cfg['image'] || $cfg['image'] = './static/images/watermark/mark.png';
		$cfg['negalpha'] || $cfg['negalpha'] = 20;
		$cfg['position'] || $cfg['position'] = 4;
		$cfg['enabled'] == 'true' || $cfg['enabled'] = 'false';
				include handler('template')->file('@admin/image_watermark');
	}
	
	public function watermark_save_test()
	{
		$this->CheckAdminPrivs('watermark');
		$this->watermark_save('test');
				$cfg = ini('image.watermark_test');
		$image_test = str_replace('mark.png', 'test.jpg', $cfg['image']);
		$image_wmd = str_replace('mark.png', 'test_wmd.jpg', $cfg['image']);
				logic('upload')->Watermark(ROOT_PATH.$image_test, ROOT_PATH.$image_wmd, $cfg);
				include handler('template')->file('@admin/image_watermark_test');
	}
	
	public function watermark_test_save()
	{
		$this->CheckAdminPrivs('watermark');
		ini('image.watermark', ini('image.watermark_test'));
		$this->Messager('保存成功！', '?mod=image&code=watermark');
	}
	
	public function watermark_save($pox = false)
	{
		$this->CheckAdminPrivs('watermark');
		$data = post('data');
		logic('upload')->Save('file', ROOT_PATH.$data['image']);
		$inip = 'image.watermark';
		if ($pox)
		{
			$inip .= '_'.$pox;
		}
		$data['enabled'] == 'true' || $data['enabled'] = false;
		ini($inip, $data);
		if ($pox)
		{
			return;
		}
		else
		{
			$this->Messager('保存成功！', '?mod=image&code=watermark');
		}
	}
}

?>