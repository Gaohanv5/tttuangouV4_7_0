<?php

/**
 * 模块：配送方式管理
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
		$this->CheckAdminPrivs('express');
		$list = logic('express')->SrcList();
		include handler('template')->file('@admin/express_list');
	}
	function Add()
	{
		$this->CheckAdminPrivs('express');
		$actionName = '添加';
		$corpList = logic('express')->CorpList();
		include handler('template')->file('@admin/express_manager');
	}
	function Edit()
	{
		$this->CheckAdminPrivs('express');
		$id = get('id', 'int');
		if (!$id)
		{
			$this->Messager('非法配送方式编号！');
		}
		$actionName = '编辑';
		$corpList = logic('express')->CorpList();
		$c = logic('express')->AdmOne($id);
		include handler('template')->file('@admin/express_manager');
	}
	function Save()
	{
		$this->CheckAdminPrivs('express');
		$id = post('id', 'int');
		$c = array();
		$c['name'] = post('name', 'txt');
		$c['express'] = post('express', 'int');
		$c['firstunit'] = post('firstunit', 'float');
		$c['continueunit'] = post('continueunit', 'float');
		$fuu = post('fuunit', 'txt');
		$cuu = post('cuunit', 'txt');
		$c['firstunit'] *= ($fuu == 'g') ? 1 : 1000;
		$c['continueunit'] *= ($cuu == 'g') ? 1 : 1000;
		$c['firstprice'] = post('firstprice', 'float');
		$c['continueprice'] = post('continueprice', 'float');
		$c['regiond'] = post('regiond', 'int');
		$dpenable = post('dpenable', 'txt');
		if ($dpenable)
		{
			$c['dpenable'] = 'true';
		}
		else
		{
			$c['dpenable'] = 'false';
		}
		$c['detail'] = post('detail');
		$c['order'] = post('order', 'int');
		$c['enabled'] = post('enabled', 'txt');
		dbc()->SetTable(table('express'));
		if ($id == 0)
		{
			$id = dbc()->Insert($c);
		}
		else
		{
			dbc()->Update($c, 'id='.$id);
		}
		if ($c['regiond'] == 1)
		{
			$eids = post('ex_region_id');
			$efp = post('ex_firstprice');
			$ecp = post('ex_continueprice');
			$eregions = post('ex_regions');
			foreach ($eids as $i => $eid)
			{
				$e = array();
				$e['parent'] = $id;
				$e['firstprice'] = $efp[$i];
				$e['continueprice'] = $ecp[$i];
				$e['region'] = $eregions[$i];
				if ($e['firstprice']=='' || $e['continueprice']=='' || $e['region']=='')
				{
					continue;
				}
				dbc()->SetTable(table('express_area'));
				if ($eid == 0)
				{
					dbc()->Insert($e);
				}
				else
				{
					dbc()->Update($e, 'id='.$eid);
				}
			}
		}
		$this->Messager('更新成功！', '?mod=express');
	}
	function Del()
	{
		$this->CheckAdminPrivs('express');
		$id = get('id', 'int');
		if (!$id)
		{
			$this->Messager('非法配送方式编号！');
		}
		logic('express')->Del($id);
		$this->Messager('删除成功！');
	}
	function Del_regions()
	{
		$this->CheckAdminPrivs('express','ajax');
		$id = get('id', 'int');
		if (!$id)
		{
			exit;
		}
		logic('express')->AreaDel($id);
		echo 'ok';
		exit;
	}
	function Corp_list()
	{
		$this->CheckAdminPrivs('express');
		$list = logic('express')->CorpList('all');
		foreach ($list as $i => $one)
		{
			$list[$i]['printedCount'] = logic('express')->cdp()->PrintedCount($one['id']);
			$list[$i]['PrinterTemplate'] = logic('express')->cdp()->hasPrinterTemplate($one['id']);
		}
		$tempList = logic('express')->cdp()->sync()->localData('data');
		include handler('template')->file('@admin/express_corp_list');
	}
	function Corp_add()
	{
		$this->CheckAdminPrivs('express');
		$actionName = '添加';
		include handler('template')->file('@admin/express_corp_manager');
	}
	function Corp_edit()
	{
		$this->CheckAdminPrivs('express');
		$id = get('id', 'int');
		if (!$id)
		{
			$this->Messager('非法快递公司编号！');
		}
		$actionName = '编辑';
		$c = logic('express')->CorpOne($id);
		include handler('template')->file('@admin/express_corp_manager');
	}
	function Corp_save()
	{
		$this->CheckAdminPrivs('express');
		$id = post('id', 'int');
		$data = array();
		$data['flag'] = post('flag', 'txt');
		$data['name'] = post('name', 'txt');
		$data['site'] = post('site', 'txt');
		$data['enabled'] = post('enabled', 'txt');
		if ($id)
		{
			$q = dbc(DBCMax)->update('express_corp')->where('id='.$id);
		}
		else
		{
			$q = dbc(DBCMax)->insert('express_corp');
		}
		$q->data($data)->done();
		$this->Messager('更新完成！', '?mod=express&code=corp&op=list');
	}
	function Corp_del()
	{
		$this->CheckAdminPrivs('express');
		$id = get('id', 'int');
		if (!$id)
		{
			$this->Messager('非法快递公司编号！');
		}
		logic('express')->CorpDel($id);
		$this->Messager('删除成功！');
	}
	function Corp_delivery()
	{
		$this->CheckAdminPrivs('express');
		$id = get('id', 'int');
		$id || $this->Messager('请输入正确的快递公司编号！', -1);
		$corp = logic('express')->CorpOne($id);
		$lables = logic('express')->cdp()->supportLables();
		$cdp = logic('express')->cdp()->GetOne($id);
		$flashVars = $this->Corp_cdp_generateFlashVars($cdp);
		include handler('template')->file('@admin/express_corp_delivery');
	}
	private function Corp_cdp_generateFlashVars($data = false)
	{
		$data===false && exit('Goo..');
		$r = '';
		if ($data['background'])
		{
			$r .= 'bcastr_config_bg='.$data['background'].'?'.time().'&';
		}
		if ($data['config'])
		{
			$r .= 'swf_config_lable='.$data['config'];
		}
		return substr($r, 0, -1);
	}
	function Corp_cdpSave()
	{
		$this->CheckAdminPrivs('express');
		$act = post('act', 'txt');
				$act == 'print_upload' && exit($this->Corp_cdp_save_Background(false));
		$act == 'background_delete' && exit($this->Corp_cdp_del_Background(false));
		$act == 'config_save' && exit($this->Corp_cdp_save_Config(false));
	}
	private function Corp_cdp_save_Background($exit = true)
	{
		$exit && exit('Goo..');
		$cid = post('corp_id', 'int');
		$save = handler('io')->initPath(UPLOAD_PATH.'express_cdp/'.$cid.'.jpg');
		$file = logic('upload')->Save('bg', $save);
		echo '<script type="text/javascript">';
		if ($file['error'])
		{
			echo 'parent.alert("'.$file['msg'].'");';
		}
		else
		{
			echo 'parent.call_flash("bg_add", "'.$file['url'].'?'.time().'");';
			logic('express')->cdp()->Update($cid, array('bgid' => $file['id']));
		}
		exit('</script>');
	}
	private function Corp_cdp_del_Background($exit = true)
	{
		$exit && exit('Goo..');
		$cid = post('corp_id', 'int');
		$cdp = logic('express')->cdp()->GetOne($cid);
		logic('upload')->Delete($cdp['bgid']);
		logic('express')->cdp()->Update($cid, array('bgid' => 0));
		exit('<script type="text/javascript">parent.call_flash("bg_delete", "");</script>');
	}
	private function Corp_cdp_save_Config($exit = true)
	{
		$exit && exit('Goo..');
		$cid = post('corp_id', 'int');
		$config = post('config_lable', 'txt');
		logic('express')->cdp()->Update($cid, array('config' => $config));
		$this->Messager('保存完成！', '?mod=express&code=corp&op=list');
	}
	function Address_list()
	{
		$this->CheckAdminPrivs('express');
		$list = logic('express')->cdp()->AddressList();
		include handler('template')->file('@admin/express_address_list');
	}
	
	function Cdp_sync()
	{
		$this->CheckAdminPrivs('express','ajax');
		if (!logic('express')->cdp()->sync()->time2Check())
		{
			exit('cached');
		}
		$checks = logic('express')->cdp()->sync()->checks();
		$checks || exit('cached');
		exit(jsonEncode($checks));
	}
	
	function Cdp_sync_import()
	{
		$this->CheckAdminPrivs('express');
		$cid = get('id', 'int');
		logic('express')->cdp()->sync()->import($cid);
		$this->Messager('导入成功，正在跳转到模板编辑页面...', '?mod=express&code=corp&op=delivery&id='.$cid);
	}
	
	function Cdp_sync_download()
	{
		$this->CheckAdminPrivs('express','ajax');
		exit(jsonEncode(logic('express')->cdp()->sync()->download()));
	}
	
	function Cdp_sync_noAlert()
	{
		$this->CheckAdminPrivs('express','ajax');
		logic('express')->cdp()->sync()->noAlert();
	}
}

?>