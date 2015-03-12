<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name item.php
 * @date 2014-09-01 17:24:23
 */
 


global $_LANG;
$config['item']=
array
(
	'member'=>array
	(
		'table_name'	=>TABLE_PREFIX. 'system_members',			'pri_field'		=>'uid',					'name_field'	=>'username',					'view_url'		=>'index.php?mod=member&code=view&id=%s',		'name'			=>$_LANG['member'],			'photo_field'	=>"face",
		'photo_path'	=>"face",
	),

);

?>