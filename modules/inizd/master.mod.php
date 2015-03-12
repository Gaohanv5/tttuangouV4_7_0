<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name master.mod.php
 * @date 2014-09-01 17:24:23
 */
 



class MasterObject
{
    var $Module='index';
    var $Code='';
    var $FILE='inizd';
    var $OPC = '';
    function MasterObject(&$config)
    {
        $this->Get  =  &$_GET;
		$this->Post =  &$_POST;
        $this->Module = trim($this->Post['mod']?$this->Post['mod']:$this->Get['mod']);
        $this->Code = trim($this->Post['code']?$this->Post['code']:$this->Get['code']);
		$this->OPC = trim($this->Post['op']?$this->Post['op']:$this->Get['op']);
    }
}


?>