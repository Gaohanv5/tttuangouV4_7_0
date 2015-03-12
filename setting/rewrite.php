<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name rewrite.php
 * @date 2014-09-01 17:24:23
 */
 

 $_rewrite=array (
  'abs_path' => '/',
  'arg_separator' => '/',
  'gateway' => '',
  'mode' => '',
  'prepend_var_list' =>
  array (
    0 => 'mod',
    1 => 'code',
  ),
  'value_replace_list' =>
  array (
    'mod' =>
    array (
      'index' => 'home',
      'list' => 'channel',
      'me' => 'user',
    ),
  ),
  'var_replace_list' =>
  array (
    'mod' =>
    array (
    ),
  ),
  'var_separator' => '-',
);
?>