<?php

/**
 * 模块：文件上传管理
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package module
 * @name upload.mod.php
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
    function Config()
    {
        $this->CheckAdminPrivs('upload');
		$upcfg = ini('upload');
        list($size_unit, $size_val) = explode(':', $upcfg['size']);
		$sys_roles = array(
			'admin' => array('name' => '管理员', 'id' => 'admin'),
			'seller' => array('name' => '合作商家', 'id' => 'seller'),
			'normal' => array('name' => '普通用户', 'id' => 'normal')
		);
        $sel_roles = explode(',', $upcfg['role']);
        include handler('template')->file('@admin/upload_config');
    }
    function Config_save()
    {
        $this->CheckAdminPrivs('upload');
		$exts = post('exts', 'text');
        $size_unit = post('size_unit', 'text');
        $size_val = post('size_val', 'int');
        $roles = post('role');
        $upcfg = array(
            'exts' => $exts,
            'size' => $size_unit.':'.$size_val,
            'role' => implode(',', $roles)
        );
        ini('upload', $upcfg);
        $this->Messager('保存成功！');
    }
}

?>