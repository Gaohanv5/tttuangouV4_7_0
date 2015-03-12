<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name paylog.logic.php
 * @date 2014-10-30 10:42:13
 */
 


 

class PaylogLogic {

	public function get_one($id, $is = 'sign') {
		$id = (is_numeric($id) ? $id : 0);
		if($id < 1) {
			return false;
		}
		$is = (in_array($is, array('id', 'sign', 'trade_no')) ? $is : 'sign');

		return dbc(DBCMax)->select('paylog')->where(array($is => $id))->order(" `id` DESC ")->limit(1)->done();
	}

	public function payfrom($id, $is = 'sign') {
		$one = self::get_one($id, $is);
		return ($one ? $one['payfrom'] : false);
	}
}