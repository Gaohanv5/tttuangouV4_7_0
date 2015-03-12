<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name rewrite.php
 * @date 2014-09-01 17:24:22
 */
 

include_once('./setting/rewrite.php');
if($_rewrite['mode']!=''){
	include_once('./include/lib/rewrite.han.php');
	$rewriteHandler=new rewriteHandler();
	$rewriteHandler->absPath=$_rewrite['abs_path'];
	$rewriteHandler->gateway=$_rewrite['gateway'];
	$rewriteHandler->argSeparator=$_rewrite['arg_separator'];
	$rewriteHandler->varSeparator=$_rewrite['var_separator'];
	$rewriteHandler->prependVarList=$_rewrite['prepend_var_list'];
	$rewriteHandler->varReplaceList=(array)$_rewrite['var_replace_list'];
	$rewriteHandler->valueReplaceList=(array)$_rewrite['value_replace_list'];
	$rewriteHandler->parseRequest($_rewrite['request']);
}
?>