<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name payfrom.logic.php
 * @date 2014-10-30 10:42:13
 */
 


 

class PayfromLogic {

	public function html($uid = 0, $type = 'default') {
		$uid = (0 === $uid ? user()->get('id') : $uid);
		if($uid < 1) {
			return ;
		}
		$pays = self::get($uid);

		$type = (in_array($type, array('default', 'index')) ? $type : 'default');
		include handler('template')->file('@html/payfrom/' . $type);
	}

	public function log($orderid) {
		$orderid = (is_numeric($orderid) ? $orderid : 0);
		if(empty($orderid)) {
			return 'payfrom.log.orderid.empty';
		}
		$paylog = logic('pay')->GetLog($orderid, 0, " `status` IN ('TRADE_FINISHED') ", true);
		if(false == $paylog) {
			return 'payfrom.log.paylog.empty';
		}
		if(false == in_array($paylog['status'], array('TRADE_FINISHED'))) {
			return 'payfrom.log.paylog_status.error';
		}
		$order = logic('order')->GetOne($orderid);
		if(fasle == $order) {
			return 'payfrom.log.orderid.error';
		}
		$money = $paylog['money'];
		$uid = $order['userid'];
		$pid = $order['paytype'];
		$payment = logic('pay')->GetOne($pid);
		$pcode = $payment['code'];
		$pname = $payment['name'];
		if(false == in_array($pcode, array('alipay', 'alipaymobile'))) {
			return 'payfrom.log.payment_code.error';
		}
		$payid = ($_POST['buyer_id'] ? $_POST['buyer_id'] : $_GET['buyer_id']);
		$payfrom = ($_POST['buyer_email'] ? $_POST['buyer_email'] : $_GET['buyer_email']);
		if(empty($payid) || empty($payfrom)) {
			return 'payfrom.log.payid.empty';
		}

		if($payfrom != $paylog['payfrom']) {
			dbc(DBCMax)->update('paylog')->data(array('payfrom'=>$payfrom))->where(array('id'=>$paylog['id']))->done();
		}

		$where = array(
				'uid' => $uid,
				'pid' => $pid,
				'payid' => $payid,
			);
		$info = dbc(DBCMax)->select('payfrom')->where($where)->limit(1)->done();
		$data = array_merge($where, array(
				'pcode' => $pcode,
				'pname' => $pname,
				'payfrom' => $payfrom,
				'paynum' => max(0, (int) $info['paynum']) + 1,
				'paysum' => max(0, (float) $info['paysum']) + $money,
				'pay' => serialize(array_merge($where, array('paylog_id' => $paylog['id'], 'paylog_money' => $money, 'paylog_trade_no' => $paylog['trade_no'], 'paylog_status' => $paylog['status'], ))),
				'time' => time(),
			));
		if(false == $info) {
			$payfromid = dbc(DBCMax)->insert('payfrom')->data($data)->done();
		} else {
			$payfromid = $info['id'];
			if($data['pay'] != $info['pay']) {
				dbc(DBCMax)->update('payfrom')->data($data)->where(array('id' => $payfromid))->done();
			}
		}
		return $payfromid;
	}

	public function get($uid, $pid = '', $payid = '') {
		$where = array('uid' => $uid);
		$pid && $where['pid'] = $pid;
		$payid && $where['payid'] = $payid;
		return dbc(DBCMax)->select('payfrom')->where($where)->done();
	}
}