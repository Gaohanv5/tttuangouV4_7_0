<?php

/**
 * 模块：打印相关
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package module
 * @name express.mod.php
 * @version 1.0
 */

class ModuleObject extends MasterObject
{
	function ModuleObject( $config )
	{
		$this->MasterObject($config);
		$runCode = Load::moduleCode($this);
		$this->$runCode();
	}
	function Main()
	{
		exit('hello');
	}
	function Delivery()
	{
		$this->CheckAdminPrivs('print');
		$oid = get('oid', 'number');
		$senderID = get('sender', 'int');
				$lastADDR = meta('cdp_service_lastADDR');
		if ($lastADDR != $senderID)
		{
			meta('cdp_service_lastADDR', $senderID);
		}
				$cdpResult = logic('express')->cdp()->CreatePrinterConfig($oid, $senderID);
		if ($cdpResult['__error__'])
		{
			$this->Messager('您还没有设置打印模板，系统正在跳转到模板编辑页面，请稍候...', '?mod=express&code=corp&op=delivery&id='.$cdpResult['corpID'], 3);
		}
				logic('express')->cdp()->Printed($oid, $senderID);
				$cdpCFG = $cdpResult['config'];
		$cdpDATA = $cdpResult['cdp'];
				$background = logic('upload')->GetOne($cdpDATA['bgid']);
		$background['extra'] = unserialize($background['extra']);
		if (!$background['extra']['width'] || !$background['extra']['height'])
		{
			$background['extra'] = handler('image')->Info($background['path']);
			logic('upload')->Field($background['id'], 'extra', serialize(array('width'=>$background['extra']['width'],'height'=>$background['extra']['height'])));
		}
				include handler('template')->file('@admin/print_delivery');
	}
	function Delivery_queue()
	{
		$this->CheckAdminPrivs('print');
		$list = logic('delivery')->GetList(DELIV_PROCESS_IN);
		include handler('template')->file('@admin/print_delivery_queue');
	}
}

?>