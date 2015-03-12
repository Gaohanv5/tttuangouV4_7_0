<?php

/**
 * 模块：配送管理
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package module
 * @name delivery.mod.php
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
    function Main()
    {
        $this->CheckAdminPrivs('delivery');
		header('Location: ?mod=delivery&code=vlist');
    }
    function vList()
    {
        $this->CheckAdminPrivs('delivery');
		$ordPROC = get('ordproc', 'string');
		$dlvPROC = $ordPROC ? ('o.process="'.$ordPROC.'"') : '1';
		if(MEMBER_ROLE_TYPE == 'seller'){
			$pids = logic('product')->GetUserSellerProduct(MEMBER_ID);
			$asql = 0;
			if($pids){
				$asql = implode(',',$pids);
			}
			$dlvPROC .= ' AND o.productid IN('.$asql.') ';
		}
        $list = logic('delivery')->GetList($ordPROC,$dlvPROC);
        include handler('template')->file('@admin/delivery_list');
    }
    function Process()
    {
        $this->CheckAdminPrivs('delivery');
		$oid = get('oid', 'number');
        $order = logic('order')->GetOne($oid);
        if (!$order)
        {
            $this->Messager(__('找不到相关订单！'));
        }
        $user = user($order['userid'])->get();
        $payment = logic('pay')->SrcOne($order['paytype']);
        $express = logic('express')->SrcOne($order['expresstype']);
        $address = logic('address')->GetOne($order['addressid']);
        include handler('template')->file('@admin/delivery_process');
    }
    function Upload_single()
    {
        $this->CheckAdminPrivs('delivery','ajax');
		if(strlen(get('no','txt')) > 8){
			logic('delivery')->Invoice(get('oid', 'number'), get('no', 'txt')) && exit('ok');
		}else{
			exit('error');
		}
    }
}

?>