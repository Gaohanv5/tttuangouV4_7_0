<?php

/**
 *
 * 逻辑区：角色权限控制
 *
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package logic
 * @name rbac.logic.php
 * @version 1.0
 */

class RBACLogic
{
    
    public function Access($file, $module, $action)
    {
        $idx = 'rbac.list.'.$file.'.'.$module.'.'.$action;
                $action = ini($idx);
        if (!$action)
        {
            ini($idx, array(
                'name' => '',
                'enabled' => false,
            ));
            return;
        }
        if (!$action['enabled'])
        {
            return;
        }
                if (user()->get('id') != 1)
        {
			$text = '抱歉，演示帐号不可以访问此功能！';
			include handler('template')->file('@inizd/alert');
            exit();
        }
    }
}

?>