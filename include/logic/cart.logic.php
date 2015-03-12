<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name cart.logic.php
 * @date 2014-12-11 14:44:49
 */
 




class CartLogic
{
	
	public function GetOne($id)
	{
		$id = (int)$id;
		if (!$id) return array();
		$ckey = 'cart.getone.'.$id;
		$list = cached($ckey);
		if ($list) return $list;
		return cached($ckey, dbc(DBCMax)->query('SELECT * FROM '.table('cart').' WHERE id='.$id)->limit(1)->done());
	}
	
	public function delete($id)
	{
		$id = (int) $id;
		dbc()->SetTable(table('cart'));
		dbc()->Delete('', 'user_id='.$id);
	}
}

?>