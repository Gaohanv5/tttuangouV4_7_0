<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name mycart.mod.php
 * @date 2014-12-11 14:44:49
 */
 

class ModuleObject extends MasterObject
{
    private $cookie_name = '__cart_view_id';
    function ModuleObject( $config )
    {
        $this->MasterObject($config);
        $runCode = Load::moduleCode($this);
        $this->$runCode();
    }
    public function main()
    {
        $upcfg = ini('recharge');
        $bank = ini('bank');
        $maxmoney = intval(user()->get('money'));
        $payaddress = $upcfg['payaddress'] ? $upcfg['payaddress'] : '请电话联系商家确认后再进行操作，否则钱财两空';
        include handler('template')->file('cash');
    }
    
    public function addCart()
    {
        $product_id = get('id', 'int');
        logic('cart_manage')->AddItem($product_id,1);
        $this->Messager('已经成功加入购物车');
    }
    
    public function delCart()
    {
    	$productId = get('id', 'int');
        logic('cart_manage')->RemoveItem($productId);
        $this->Messager('成功删除一条购物车商品');
    }
    
    public function listCart()
    {
        
    }
}