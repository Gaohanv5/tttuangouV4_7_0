<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name product_notify.logic.php
 * @date 2014-12-11 14:44:49
 */
 




class Product_notifyLogic
{
	public function enabled($type = '') {
		$enabled = false;
		$types = array('sms', 'mail');
		$type = ($type ? (array) $type : $types);
		foreach($type as $t) {
			if(in_array($t, $types)) {
				$enabled = ($enabled || ini('notify.event.user_product_notify.hook.' . $t . '.enabled'));
			}
		}		
		return $enabled;
	}

	
	public function check($product_id) {
		$enabled = $this->enabled();
		$product_id = (int) $product_id;
		if($product_id > 0 && $enabled) {
			$enabled = false;
			$product = logic('product')->GetOne($product_id);
			if($product['time_remain'] < 0) {
				; 			} else {
				if($product['surplus'] > 0) {
					if($product['maxnum']>0 && $product['surplus']<=0) {
						; 					} elseif ($product['begin_date'] || $product['limit_time']) {
						; 						$enabled = true;
					} else {
						; 					}
				} else {
					; 				}
			}
		}
		return $enabled;
	}

	
	public function get_one($uid, $product_id, $notify_type = null) {
		$uid = (int) $uid;
		$product_id = (int) $product_id;
		if($uid < 1 || $product_id < 1) {
			return false;
		}
		$where = array('uid'=>$uid, 'product_id'=>$product_id);
		if(isset($notify_type)) {
			$where['notify_type'] = $notify_type;
		}
		return dbc(DBCMax)->select('product_notify')->where($where)->limit(1)->done();
	}

	
	public function save($p) {
		$uid = user()->get('id');
		$product_id = (int) $p['product_id'];
		$notify_id = $p['notify_id'];
		if($uid < 1 || $product_id < 1 || empty($notify_id) || false == ($product = logic('product')->GetOne($product_id))) {
			return 0;
		}
		if($product['begintime'] < time()) {
			return -2;
		}
		if(empty($p['time'])) {
			$p['time'] = $product['begintime'] - 900;
		}
		if(empty($p['status'])) {
			$p['status'] = '-1';
		}
		$one = $this->get_one($uid, $product_id, $p['notify_type']);
		if($one) {
			return $this->update($p, $one['id']);
		}
		$data = array(
			'uid' => $uid,
			'product_id' => $product_id,
			'notify_id' => $notify_id,
			'notify_type' => $p['notify_type'],
			'notify_to' => $p['notify_to'],
			'time' => $p['time'],
			'status' => $p['status'],
			'status_time' => time(),
		);
				dbc()->SetTable(table('product_notify'));
		return dbc()->Insert($data);
	}

	
	public function update($p, $id = 0) {
		$id = (int) ($id ? $id : $p['id']);
		if($id < 1 || false == ($one = dbc(DBCMax)->select('product_notify')->where(array('id'=>$id))->limit(1)->done())) {
			return -1;
		}
		$data = array();
		$can_upks = array('notify_id', 'notify_type', 'notify_to', 'time', 'status');
		foreach($can_upks as $upk) {
			if(isset($p[$upk]) && $p[$upk] != $one[$upk]) {
				$data[$upk] = $p[$upk];
			}
		}
		if($data) {
			if(isset($data['status'])) {
				$data['status_time'] = time();
			}
						dbc()->SetTable(table('product_notify'));
			dbc()->Update($data, " `id`='{$id}' ");
		}
		return $id;
	}

	
	public function sync() {
		$time = time();		
		$sql = "select * from " . table('product_notify') . " where `time`<'{$time}' and `status`='-1'";
		$list = dbc(DBCMax)->query($sql)->done();
		if(is_array($list) && count($list)) {
			foreach($list as $row) {
				$this->update(array('status'=>'1'), $row['id']);
				$product = logic('product')->GetOne($row['product_id']);
				
				if($product && $row['notify_id'] && $row['notify_type']) {
					$msg = ini('notify.event.' . $row['notify_id'] . '.msg.' . $row['notify_type']);
					if($msg && $row['notify_to']) {
						$product['begin_date'] = date('Y-m-d H:i:s', $product['begintime']);
						driver('notify')->FlagParser($row['notify_id'] . '.'. $row['notify_type'], $product, $msg);
						logic('push')->add($row['notify_type'], $row['notify_to'], array('content'=>$msg, 'subject'=>ini('settings.site_name').'提醒您'));
					}
				}
			}
		}
	}

}