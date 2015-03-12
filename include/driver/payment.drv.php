<?php

/**
 * 驱动：支付方式
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package driver
 * @name payment.drv.php
 * @version 1.0
 */

class PaymentDriver
{
    
    public final function load($name)
    {
        $file = dirname(__FILE__).'/payment/'.$name.'.php';
	    include_once $file;
	    $className = $name.'PaymentDriver';
	    return new $className();
    }
    
    public function CreateLink($payment, $parameter)
    {

    }
    
    public function CreateConfirmLink($payment, $order)
    {

    }
    
    public function CallbackVerify($payment)
    {

    }
    
    public function GetTradeData()
    {

    }
    
    public function StatusProcesser($status)
    {

    }
    
    public function GoodSender($payment, $express, $sign, $type)
    {

    }
}

?>