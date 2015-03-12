<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name image.php
 * @date 2014-09-01 17:24:23
 */
 


$config["image"] =  array (
  'watermark_test' =>
  array (
    'type' => 'text',
    'image' => './static/images/watermark/mark.png',
    'text' => '天天团购系统，开源，免费 www.tttuangou.net',
    'font' => 'font.ttf',
    'fontsize' => '13',
    'position' => '4',
    'enabled' => 'true',
  ),
  'watermark' =>
  array (
    'type' => 'image',
    'image' => './static/images/watermark/mark.png',
    'text' => '天天团购系统，开源，免费 www.tttuangou.net',
    'font' => 'font.ttf',
    'fontsize' => '13',
    'position' => '4',
    'enabled' => 'false',
  ),
);
?>