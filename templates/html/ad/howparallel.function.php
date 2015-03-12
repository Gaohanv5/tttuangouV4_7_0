<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name howparallel.function.php
 * @date 2014-09-01 17:24:22
 */
 




function ad_config_save_parser_howparallel(&$data)
{
	if (count($data['list']) < 1) return;
	$orders = array();

	logic('upload')->Save('file_adl', './uploads/images/howparallel/hl.gif');
	logic('upload')->Save('file_adr', './uploads/images/howparallel/hr.gif');

	
	$data['list']['adl']['image'] = 'uploads/images/howparallel/hl.gif';
	$data['list']['adr']['image'] = 'uploads/images/howparallel/hr.gif';
	$data['fu'] = true;
}

?>
