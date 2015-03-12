<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name howdow.function.php
 * @date 2014-10-30 10:42:13
 */
 




function ad_config_save_parser_howdow(&$data)
{
	if (count($data['list']) < 1) return;
	$orders = array();
	$ic = 0;
	foreach ($data['list'] as $id => $cfg)
	{
		$orders[$id] = $cfg['order'];
		$fid = 'file_'.$id;
		if (isset($_FILES[$fid]) && is_array($_FILES[$fid]))
		{
			logic('upload')->Save($fid, ROOT_PATH.$data['list'][$id]['image']);
		}
		$ic++;
	}
	arsort($orders);
	$dn = array();
	foreach ($orders as $id => $order)
	{
		$dn[$id] = $data['list'][$id];
	}
	$data['list'] = $dn;
	$data['fu'] = true;
}

?>
