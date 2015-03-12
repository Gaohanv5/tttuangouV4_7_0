<?php

/**
 * 模块：充值管理
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package module
 * @name recharge.mod.php
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
    function Card()
    {
        $this->CheckAdminPrivs('rechargecard');
		$used = $_GET['used'];
        is_numeric($used) || $used = -1;
        page_moyo_max_io(50);
        $list = logic('recharge')->card()->GetList($used);
        include handler('template')->file('@admin/recharge_card');
    }
    function Card_generate()
    {
        $this->CheckAdminPrivs('rechargecard');
		include handler('template')->file('@admin/recharge_card_generate');
    }
    function Card_generate_ajax()
    {
        $this->CheckAdminPrivs('rechargecard','ajax');
		$price = get('price', 'number');
        $nums = get('nums', 'int');
        logic('recharge')->card()->Generate($price, $nums);
        exit('ok');
    }
    function Card_delete()
    {
        $this->CheckAdminPrivs('rechargecard','ajax');
		$id = get('id', 'int');
        $affect = logic('recharge')->card()->Delete($id);
        exit($affect > 0 ? 'ok' : 'fail');
    }
    function Order_clean()
    {
        $this->CheckAdminPrivs('rechargeorder','ajax');
		$ckey = 'business.recharge.order.clean.lock';
        fcache($ckey, dfTimer('com.recharge.order.clean')) && exit('no');
        $cleans = logic('recharge')->Clean();
        fcache($ckey, 'DNA.'.md5(time()));
        $rel = $cleans > 0 ? '（系统已经自动清理掉 '.$cleans.' 个过期的充值流水号）' : 'no';
        exit($rel);
    }
    
    public function order()
    {
        $this->CheckAdminPrivs('rechargeorder');
		$paystatus = get('paystatus');
		$where = ' ptype = 1 ';
                if (is_numeric($paystatus))
        {
            if ((int)$paystatus < 1)
            {
                $where .= ' AND paytime = 0';
            }
            else
            {
                $where .= ' AND paytime > 0';
            }
        }
        $list = logic('recharge')->GetList($where);
        include handler('template')->file('@admin/recharge_order');
    }
    
    public function order_confirm()
    {
        $this->CheckAdminPrivs('rechargeorder','ajax');
		$orderid = get('orderid', 'number');
        if ($orderid)
        {
            $r = logic('recharge')->MakeSuccessed($orderid);
            exit('ok');
        }
        else
        {
            exit('充值记录流水号不正确');
        }
    }
	function Config()
    {
        $this->CheckAdminPrivs('rechargeset');
		$upcfg = ini('recharge');
        include handler('template')->file('@admin/recharge_config');
    }
	function Scale()
    {
        $this->CheckAdminPrivs('rechargescale');
		$action = '?mod=recharge&code=scale&op=save';
		$upcfg = ini('rebate_setting');
		if(!(empty($upcfg) || count($upcfg)<0)){
			extract($upcfg);
		}
        include handler('template')->file('@admin/recharge_scale');
    }
	function Scale_save()
    {
        $this->CheckAdminPrivs('rechargescale');
		$upcfg = ini('rebate_setting');
		$buy_pre = $this->Post['buy_pre'];
		$sell_pre = $this->Post['sell_pre'];
		if($buy_pre<0 || $sell_pre<0 ) $this->Messager("返利比例不可小于零",-1);
		$upcfg['buy_pre'] = $buy_pre;
		$upcfg['sell_pre'] = $sell_pre;
		ini('rebate_setting',$upcfg);
        $this->Messager("保存成功",'?mod=recharge&code=scale');
    }
    function Config_save()
    {
        $this->CheckAdminPrivs('rechargeset');
		$percentage = post('percentage', 'int');
		$payaddress = post('payaddress', 'txt');
		$percentage = intval(min(100,max(0,$percentage)));		$upcfg = array(
            'percentage' => $percentage,
			'payaddress' => $payaddress
        );
        ini('recharge', $upcfg);
        $this->Messager('保存成功！');
    }
}
?>