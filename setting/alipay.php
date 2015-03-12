<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name alipay.php
 * @date 2014-09-01 17:24:23
 */
 


$config["alipay"] =  array (
  'account' =>
  array (
    'login' =>
    array (
      'source' =>
      array (
        'alipay' => '支付宝快捷登录',
      ),
      'enabled' => false,
    ),
  ),
  'address' =>
  array (
    'import' =>
    array (
      'source' =>
      array (
        'alipay' => '支付宝获取收货地址',
      ),
      'enabled' => false,
    ),
  ),
);
?>