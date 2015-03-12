<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name subscribe.php
 * @date 2014-09-01 17:24:23
 */
 


$config["subscribe"] =  array (
  'validate' =>
  array (
    'do' =>
    array (
      'mail' => false,
      'sms' => false,
    ),
    'undo' =>
    array (
      'mail' => false,
      'sms' => false,
    ),
  ),
);
?>