<?php

/**
 * 模块：配置自适应管理
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package module
 * @name ini.mod.php
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
        exit(jsonEncode(ini($path)));
    }
    public function Set()
    {
        $path = get('path', 'txt');
        $data = get('data');
                $data = (strlen($data) <= 5) ? (($data == 'true') ? true : (($data == 'false') ? false : $data)) : $data;
                exit(jsonEncode(ini($path, $data)));
    }
}

?>