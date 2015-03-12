<?php

/**
 * 模块：城市管理
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package module
 * @name city.mod.php
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
	function main()
	{
		$this->CheckAdminPrivs('city');
		header('Location: admin.php?mod=tttuangou&code=city');
	}
	
	public function place()
	{
		$this->CheckAdminPrivs('city');
		$cityId = get('cid', 'int');
		$city = dbc(DBCMax)->select('city')->where(array('cityid' => $cityId))->limit(1)->done();
		$places = logic('city')->get_places($cityId);
		include handler('template')->file('@admin/city_place_list');
	}
	
	public function place_add()
	{
		$this->CheckAdminPrivs('city','ajax');
		$parenttype = get('parenttype', 'string');
		$parentid = get('parentid', 'int');
		include handler('template')->file('@admin/city_place_add');
	}
	public function place_save()
	{
		$this->CheckAdminPrivs('city','ajax');
		$parenttype = post('parenttype', 'string');
		$parentid = post('parentid', 'int');
		$name = post('name', 'text');
		$id = logic('city')->add_place($parenttype, $parentid, $name);
		exit('<script type="text/javascript">var op = window.opener ? window.opener : window.parent; op.__cplace_add_finish('.$id.');</script>');
	}
	
	public function place_del()
	{
		$this->CheckAdminPrivs('city','ajax');
		$id = get('id', 'int');
		$id || exit('false');
		logic('city')->del_place($id);
		exit('ok');
	}
	
	public function place_ajaxlist()
	{
		$type = get('type', 'string');
		$id = get('id', 'int');
		$datas = logic('city')->get_of_parent($type, $id);
		$html = '<option value="0">全部</option>';
		foreach ($datas as $data)
		{
			$html .= '<option value="'.$data['id'].'">'.$data['name'].'</option>';
		}
		exit($html);
	}
    
    function Hot_ajax() {
    	$this->CheckAdminPrivs('city','ajax');
		$id = get('id', 'int');
		$info = logic('city')->get_place_one($id);
		include handler('template')->file('@admin/city_hot_ajax');
    }

    function Hot_save() {
    	$this->CheckAdminPrivs('city','ajax');
		$id = post('id', 'int');
				$hot = (post('hot', 'int') ? 1 : 0);
		$new = array(
			'hot' => $hot,
			'fontcolor' => post('fontcolor', 'string'),
			'hotorder' => post('hotorder', 'int'),
		);
		$r = logic('city')->hot_save($id, $new);		
		
		exit('<script type="text/javascript">var op = window.opener ? window.opener : window.parent; op.__cplace_hot_finish("'.$id.'", "'.$hot.'");</script>');
    }
}

?>