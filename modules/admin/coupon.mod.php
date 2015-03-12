<?php

/**
 * 模块：团购券管理
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package module
 * @name coupon.mod.php
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
        $this->CheckAdminPrivs('coupon');
		header('Location: ?mod=coupon&code=vlist');
    }
    function vList()
    {
    	$this->CheckAdminPrivs('coupon');
		if(isset($_GET['coupsta'])){
			$coupSTA = get('coupsta', 'int');
		}else{
			$coupSTA = TICK_STA_ANY;
		}
		$fpids = '';
		if(MEMBER_ROLE_TYPE == 'seller'){
			$pids = logic('product')->GetUserSellerProduct(MEMBER_ID);
			$fpids = 0;
			if($pids){
				$fpids = implode(',',$pids);
			}
		}
        $list = logic('coupon')->GetList(USR_ANY, ORD_ID_ANY, $coupSTA, false, $fpids);
        include handler('template')->file('@admin/coupon_list');
    }
    function Add()
    {
        $this->CheckAdminPrivs('coupon');
		$uid = get('uid', 'int');
        $pid = get('pid', 'int');
        $oid = get('oid', 'number');
        include handler('template')->file('@admin/coupon_add');
    }
    function Add_save()
    {
        $this->CheckAdminPrivs('coupon','ajax');
		$uid = get('uid', 'int');
        $pid = get('pid', 'int');
        $oid = get('oid', 'number');
        $number = get('number', 'number');
        if (!$number || strlen($number) != 9) $number = false;
        $password = get('password', 'number');
        if (!$password || strlen($password) != 3) $password = false;
        $mutis = get('mutis', 'int');
        if (!$mutis) $mutis = 1;
        logic('coupon')->Create($pid, $oid, $uid, $mutis, $number, $password);
        exit('ok');
    }
    function Alert()
    {
        $this->CheckAdminPrivs('coupon','ajax');
		$id = get('id', 'int');
		if($this->doforbidden($id)){
			exit('forbidden');
		}
        $c = logic('coupon')->GetOne($id);
        logic('notify')->Call($c['uid'], 'admin.mod.coupon.Alert', $c);
        exit('ok');
    }
    function Reissue()
    {
        $this->CheckAdminPrivs('coupon','ajax');
		$id = get('id', 'int');
		if($this->doforbidden($id)){
			exit('forbidden');
		}
        $c = logic('coupon')->SrcOne($id);
        $uid = $c['uid'];
        $data = array
        (
            'uid' => $c['uid'],
            'productid' => $c['productid'],
        	'orderid' => $c['orderid'],
    		'number' => $c['number'],
    		'password' => $c['password'],
            'mutis' => $c['mutis'],
			'status' => $c['status']
        );
        logic('coupon')->Create_OK($uid, $data);
        exit('ok');
    }
    function Delete()
    {
        $this->CheckAdminPrivs('coupon','ajax');
		$id = get('id', 'int');
		if($this->doforbidden($id)){
			exit('forbidden');
		}
        logic('coupon')->Delete($id);
        exit('ok');
    }
    function Config()
    {
        $this->CheckAdminPrivs('coupon');
		include handler('template')->file('@admin/coupon_config');
    }
	private function doforbidden($ticketid){
		$return = false;
		if(MEMBER_ROLE_TYPE == 'seller'){
			$pids = logic('product')->GetUserSellerProduct(MEMBER_ID);
			$tinfo = dbc(DBCMax)->query('select productid from '.table('ticket')." where ticketid='".$ticketid."'")->limit(1)->done();
			if(!in_array($tinfo['productid'],$pids)){
				$return = true;
			}
		}
		return $return;
	}
}

?>