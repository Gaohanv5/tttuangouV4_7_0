<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name nav.php
 * @date 2014-09-01 17:24:23
 */
 
 
 

  
$config['nav']=array (
  0 => 
  array (
    'order' => '1',
    'name' => '首页',
    'url' => '',
    'title' => '网站首页',
    'target' => '_self',
  ),
  1 => 
  array (
    'order' => '2',
    'name' => '商家',
    'url' => '?mod=seller',
    'title' => '商家列表',
    'target' => '_self',
  ),
  2 => 
  array (
    'order' => '3',
    'name' => '限时抢购',
    'url' => '?mod=countdown',
    'title' => '限时抢购',
    'target' => '_self',
  ),
  3 => 
  array (
    'order' => '4',
    'name' => '邀请有奖',
    'url' => '?mod=list&code=invite',
    'title' => '邀请好友参加团购获得返利',
    'target' => '_self',
  ),
  4 => 
  array (
    'order' => '5',
    'name' => '下载手机版',
    'url' => '?mod=downapp',
    'title' => '手机客户端',
    'target' => '_self',
  ),
);
?>