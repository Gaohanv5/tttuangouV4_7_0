<?php

/**
 * 模块：系统逻辑自适应管理
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package module
 * @name lgc.mod.php
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
                list($search, $lgName) = explode('@', $path);
        list($sParm, $lgFunc) = explode('~', $search);
        $logic = logic($lgName);
        if (method_exists($logic, $lgFunc))
        {
            $r = $logic->$lgFunc('get', $sParm);
        }
        else
        {
            $r = false;
        }
        exit(jsonEncode($r));
    }
    public function Set()
    {
        $path = get('path', 'txt');
        $data = get('data');
                list($search, $lgName) = explode('@', $path);
        list($sParm, $lgFunc) = explode('~', $search);
        $logic = logic($lgName);
        if (method_exists($logic, $lgFunc))
        {
            $r = $logic->$lgFunc('set', $sParm, $data);
        }
        else
        {
            $r = false;
        }
        exit('end');
    }
}

?>