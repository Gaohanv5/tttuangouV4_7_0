<?php
#购物车
#auth yyfjj
#date:2014-11-05
class MyCart
{
    private $num = 10;
    
    private $cookie_name = '__my_cart_id';
    
    private $id = '';
    
    public $ids = array();
    #添加到购物车
    function addCart()
    {
        echo "添加到购物车";
    }
    
    #从购物车取出
    function delCart()
    {
        
    }
    
    #列出当前购物车商品
    function listCart()
    {
        
    }
    
    
    
}

interface ICart
{
    public $cookie_name = '__my_cart_id';
    function addCart();
    function delCart();
}

class CookieCart implements ICart
{
    function addCart($id)
    {
        handler('cookie')->SetVarPush($this->cookie_name, $id, 864000);
    }
    function delCart($id)
    {
        handler('cookie')->DeleteVarAnyPop($this->cookie_name, $id);
    }
}

class serviceCart implements ICart
{
    function addCart($id)
    {
        
    }
    
    function delCart($id)
    {
        
    }
}