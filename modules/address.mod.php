<?php

/**
 * 模块：地址相关
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package module
 * @name address.mod.php
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
    
    function Import()
    {
    	$flag = get('flag', 'txt');
        if (!$flag || !ini('alipay.address.import.source.'.$flag)) exit('ERROR: no Import Source');
        $html = logic('address')->import()->linker($flag);
        logic('address')->import()->referer($_SERVER['HTTP_REFERER']);
        include handler('template')->file('@address/import/redirect');
    }
    function Import_callback()
    {
    	$from = get('from', 'txt');
        $data = logic('address')->import()->verify($from);
    	if ($data)
        {
        	        	user()->get('phone') || user()->set('phone', $data['mobile_phone']);
            $aid = logic('address')->import()->insert($data);
            header('Location: '.logic('address')->import()->referer().((rewrite('?c=s') == '?c=s') ? '&aid='.$aid : '/aid-'.$aid));
        }
        else
        {
            $this->Messager(__('获取收货地址时出错！'));
        }
    }
}
?>