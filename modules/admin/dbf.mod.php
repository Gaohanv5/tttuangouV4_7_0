<?php

/**
 * 模块：数据库字段自适应管理
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package module
 * @name dbf.mod.php
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
    public function Main()
    {
            }
    public function Get()
    {
        $path = get('path', 'txt');
                list($search, $field) = explode('/', $path);
        list($sWhere, $sTable) = explode('@', $search);
        list($sField, $sValue) = explode(':', $sWhere);
        $sql = 'SELECT `'.$field.'` FROM '.table($sTable).' WHERE '.$sField.'='.(is_numeric($sValue) ? $sValue : '"'.$sValue.'"');
        $result = dbc()->Query($sql)->GetRow();
        exit(jsonEncode($result[$field]));
    }
    public function Set()
    {
        $path = get('path', 'txt');
        $data = get('data');
                list($search, $field) = explode('/', $path);
        list($sWhere, $sTable) = explode('@', $search);
        list($sField, $sValue) = explode(':', $sWhere);
        $sql = 'UPDATE `'.table($sTable).'` SET `'.$field.'`='.(is_numeric($data) ? $data : '"'.$data.'"').' WHERE `'.$sField.'`='.(is_numeric($sValue) ? $sValue : '"'.$sValue.'"');
        dbc()->Query($sql);
				if($sTable == 'seller' && $field == 'enabled'){
			logic('seller')->setmembertype($sValue,$data);
		}
        exit('end');
    }
}

?>