<?php

/**
 * 模块：用户提现管理
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package module
 * @name cash.mod.php
 * @version 1.0
 */

class ModuleObject extends MasterObject
{
    public function ModuleObject( $config )
    {
        $this->MasterObject($config);
        $runCode = Load::moduleCode($this);
        $this->$runCode();
    }
	public function Main(){
		exit('error');
	}
    public function order()
    {
        $this->CheckAdminPrivs('cashorder');
		$orderid = get('orderid', 'number');
		if($orderid)
		{
			$order = logic('cash')->GetOne($orderid);
			if($order){
				if($order['status'] =='doing'){
					$action = "?mod=cash&code=order&op=save";
				}
				$order_log = logic('cash')->Getlog($orderid);
			}else{
				$this->Messager('操作错误！');
			}
		}
		else
		{
			$paystatus = get('paystatus');
			if (in_array($paystatus,array('no','yes','doing','error')))
			{
				$where = "status = '{$paystatus}'";
			}
			else
			{
				$where = '1';
			}
			$list = logic('cash')->GetList($where);
		}
        include handler('template')->file('@admin/cash_order');
    }
	public function order_save(){
		$this->CheckAdminPrivs('cashorder');
		$orderid = post('orderid', 'number');
		$status = post('status', 'txt');
		$info = strip_tags(post('info', 'txt'));
		if(!in_array($status,array('yes','error'))){
			$this->Messager('操作失败，请选择一个操作结果！');
		}
		$return = logic('cash')->Orderdone($orderid,$status,$info);
		if($return){
			$this->Messager('操作成功！');
		}else{
			$this->Messager('操作失败！');
		}
	}
	
    public function order_confirm()
    {
        $this->CheckAdminPrivs('cashorder');
		$orderid = get('orderid', 'number');
        if ($orderid)
        {
            $r = logic('cash')->MakeSuccessed($orderid);
            exit('ok');
        }
        else
        {
            exit('提现记录流水号不正确');
        }
    }
}
?>