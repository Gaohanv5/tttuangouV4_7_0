<?php

/**
 * 模块：属性管理
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package module
 * @name attrs.mod.php
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
	public function Main(){
		exit('error');
	}
	
	public function map()
	{
		$all = logic('attrs')->get(get('id', 'int'));
		exit(jsonEncode($all));
	}

	

	public function ops_cat_append()
	{
		$this->ops_cat_modify();
	}
	public function ops_cat_modify()
	{
		$product_id = post('pid', 'int');
		$cat_id = post('catId', 'int');
		$cat_name = post('catName', 'txt');
		$cat_required = post('catRequired', 'txt');
		$cat_data = array(
			'name' => $cat_name,
			'required' => $cat_required
		);
		if ($cat_id)
		{
			$cat_data['id'] = $cat_id;
		}
		$cat_id = logic('attrs')->cat_get_id($product_id, $cat_data);
		exit((string)$cat_id);
	}
	public function ops_cat_remove()
	{
		$cat_id = post('catId', 'int');
		$eff = logic('attrs')->cat_remove($cat_id);
		exit((string)$eff);
	}
	public function ops_item_append()
	{
		$this->ops_item_modify();
	}
	public function ops_item_modify()
	{
		$cat_id = post('catId', 'int');
		$attr_id = post('attrId', 'int');
		$attr_name = post('attrName', 'txt');
		$attr_price_moves = post('priceMoves', 'float');
		$attr_binding = post('attrBinding', 'txt');
		$attr_data = array(
			'name' => $attr_name,
			'pricemoves' => $attr_price_moves,
			'binding' => $attr_binding
		);
		if ($attr_id)
		{
			$attr_data['id'] = $attr_id;
		}
		$attr_id = logic('attrs')->attr_get_id($cat_id, $attr_data);
		exit((string)$attr_id);
	}
	public function ops_item_remove()
	{
		$attr_id = post('attrId', 'int');
		$eff = logic('attrs')->attr_remove($attr_id);
		exit((string)$eff);
	}
}

?>