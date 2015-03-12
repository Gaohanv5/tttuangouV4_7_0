<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name rebate.logic.php
 * @date 2014-12-11 14:44:49
 */
 


class RebateLogic
{
	
	public function Get_HomeInfo_For_Seller($sellerid)
	{
		$res = dbc(DBCMax)->select('seller')->where('`id`='. (int)$sellerid)->limit(1)->done();
		if( $res ){
			$arr = $this->Get_HomeInfo_For_member($res['userid']);
			$arr['profit_pre'] = $res['profit_pre'];
			return $arr;
		}else{
			return 0;
		}
	}
	public function Get_HomeInfo_For_member($userid)
	{
		$res = dbc(DBCMax)->select('members')->where('`uid`='. (int)$userid)->limit(1)->done();
		if( !$res ) return 0;
		$home_uid = $res['home_uid'];
		$uname    = $res['username'];
		if( $home_uid==0 ){
			return array(
				'userid' => $userid,
				'uname'=>$uname,
			);
		}
		$res = dbc(DBCMax)->select('members')->where('`uid`='. (int)$home_uid)->limit(1)->done();
		if( $res ){
			return array(
				'userid' => $userid,
				'uname'=>$uname,
				'home_uid'=>$res['uid'],
				'buy_pre'=>$res['buy_pre'],
				'sell_pre'=>$res['sell_pre'],
			);
		}else{
			return array(
				'userid' => $userid,
				'uname'=>$uname,
			);
		}
	}
	public function Add_RebateValue_For_Seller($sellerid,$money,$orderid,$ticketid=0,$fundmoney=0){
		$home = $this->Get_HomeInfo_For_Seller($sellerid);
		if( !$home ) return;
				$userid     = $home['userid'];
		$dr_set = ini('rebate_setting');
				if($fundmoney > 0 || $fundmoney < 0){
			$profit_pre = $home['profit_pre'] > 0 ? $home['profit_pre'] : (($dr_set['profit'] && $dr_set['profit'][0]['pre'] > 0) ? $dr_set['profit'][0]['pre'] : 0);
			$data = array(
				'uid'      => $userid,
				'uname' => $home['uname'],
				'home_uid' => 0,
				'deal_money' => $money,
				'fund_money' => $fundmoney,
				'salary_pre' => $profit_pre,
				'salary_money' => $this->gave_me_money($money, $profit_pre),
				'orderid' => $orderid,
				'ticketid' => $ticketid,
				'type' => 'master',
				'addtime' => time(),
			);
			dbc(DBCMax)->insert('rebate_log')->data($data)->done();
			if($fundmoney > 0){
				$salary_money = $fundmoney;
			}elseif($profit_pre > 0){
				$salary_money = $money - $this->gave_me_money($money, $profit_pre);
			}else{
				$salary_money = $money;
			}
			dbc(DBCMax)->update('seller')->data('account_money=account_money+'. $salary_money .',total_money=total_money+'. $salary_money)->where('id='.(int)$sellerid)->done();
		}
				if( !$home['home_uid'] ) return;
		$salary_pre = $home['sell_pre'] > 0 ? $home['sell_pre'] : (($dr_set['sell_pre'] && $dr_set['sell_pre'] > 0) ? $dr_set['sell_pre'] : 0);
		if($salary_pre > 0){
			$salary_money = $this->gave_me_money($money, $salary_pre);
			$home_uid   = $home['home_uid'];
			$data = array(
				'uid'      => $userid,
				'uname' => $home['uname'],
				'home_uid' => $home_uid,
				'deal_money' => $money,
				'salary_pre' => $salary_pre,
				'salary_money' => $salary_money,
				'orderid' => $orderid,
				'ticketid' => $ticketid,
				'type' => 'sell',
				'addtime' => time(),
			);
			dbc(DBCMax)->insert('rebate_log')->data($data)->done();
			logic('me')->money()->add($salary_money, $home_uid, array(
				'name' => '邀请返利',
				'intro' => "卖家【".$home['uname']."】于：".date('Y-m-d H:i:s',time())."<br>卖出商品：".$money."元，您获得返利：".$salary_money."元<br>订单号：".$orderid
			));
		}
	}
	public function Add_RebateValue_For_Buyer($userid,$money,$orderid='',$ticketid=0){
		$home = $this->Get_HomeInfo_For_member($userid);
		if( !$home || !$home['home_uid'] ) return;
		$dr_set = ini('rebate_setting');
		$salary_pre = $home['buy_pre'] > 0 ? $home['buy_pre'] : (($dr_set['buy_pre'] && $dr_set['buy_pre'] > 0) ? $dr_set['buy_pre'] : 0);
		if($salary_pre > 0){
			$home_uid   = $home['home_uid'];
			$salary_money = $this->gave_me_money($money, $salary_pre);
			$data = array(
				'uid'   => $userid,
				'uname' => $home['uname'],
				'home_uid' => $home_uid,
				'deal_money' => $money,
				'salary_pre' => $salary_pre,
				'salary_money' => $salary_money,
				'orderid' => $orderid,
				'ticketid' => $ticketid,
				'type' => 'buy',
				'addtime' => time(),
			);
			dbc(DBCMax)->insert('rebate_log')->data($data)->done();
			dbc(DBCMax)->update('members')->data('salary_number=salary_number+'.$money)->where('uid='.(int)$home_uid)->done();
			logic('me')->money()->add($salary_money, $home_uid, array(
				'name' => '邀请返利',
				'intro' => "买家【".$home['uname']."】于：".date('Y-m-d H:i:s',time())."<br>消费金额：&yen;".$money."元，您获得返利：&yen;".$salary_money."元" . ($orderid ? "<br>订单号：".$orderid : ""),
			));
		}
	}

	public function Add_Rebate_For_Ticket($product){
		$sellerid = $product['sellerid'];
		$orderid = $product['coupon']['orderid'];
		$ticketid = $product['coupon']['ticketid'];
		$mutis = $product['coupon']['mutis'];
		$data_order = dbc(DBCMax)->select('order')->in('productnum,totalprice,expressprice,userid')->where('`orderid`='. $orderid)->limit(1)->done();
		$totalprice = $data_order['totalprice'];
		$productnum = (int)$data_order['productnum'];
		$userid = $data_order['userid'];
		if( $mutis == $productnum ){
			$money = $totalprice - $data_order['expressprice'];
			$fundmoney = $product['fundprice'] * $productnum;
			$score = $productnum * $product['score'];
		}else{
			$money = ($totalprice - $data_order['expressprice']) / $productnum;
			$fundmoney = $product['fundprice'];
			$score = $product['score'];
		}
		$this->Add_RebateValue_For_Buyer($userid,$money,$orderid,$ticketid);
		$this->Add_RebateValue_For_Seller($sellerid,$money,$orderid,$ticketid,$fundmoney);
				logic('credit')->add_score($product['id'],$userid,$score);
				dbc(DBCMax)->update('order')->data(array('comment' => '1'))->where(array('orderid' => $orderid,'comment' => '0'))->done();
	}
	public function Add_Rebate_For_Item($data_order){
		$orderid = $data_order['orderid'];
		$productid = $data_order['productid'];
		$totalprice = $data_order['totalprice'];
		$productnum = (int)$data_order['productnum'];
		$userid = $data_order['userid'];
		$data_product = dbc(DBCMax)->select('product')->in('sellerid,fundprice,score')->where('`id`='. $productid)->limit(1)->done();
		$sellerid = $data_product['sellerid'];
		$money = $totalprice - $data_order['expressprice'];
		$fundmoney = $data_product['fundprice'] * $productnum;
		$this->Add_RebateValue_For_Buyer($userid,$money,$orderid,$ticketid);
		$this->Add_RebateValue_For_Seller($sellerid,$money,$orderid,$ticketid,$fundmoney);
				$score = $productnum * $data_product['score'];
		logic('credit')->add_score($productid,$userid,$score);
				dbc(DBCMax)->update('order')->data(array('comment' => '1'))->where(array('orderid' => $orderid,'comment' => '0'))->done();
	}

	private function gave_me_money($money,$pre100){
				return round($money * $pre100 / 100, 3);
	}
	
	public function Get_Rebate_setting($toHtml = false){
		$cfg = ini('rebate_setting');
		return $cfg;
	}
	public function Save_Rebate_Setting($data){
	}

	public function get_list_for_me($member_uid){
		$sql = "SELECT * FROM ". table('rebate_log') ." WHERE home_uid='". (int)$member_uid ."' AND `type` != 'master' ORDER BY id DESC";
		$sql = page_moyo($sql);
		$res = dbc(DBCMax)->query($sql)->done();
		if( $res ){
			return $res;
		}else{
			return array();
		}
	}

	public function get_percent($uid){
		$res = dbc(DBCMax)->select('members')->in('buy_pre,sell_pre')->where('`uid`='. (int)$uid)->limit(1)->done();
		if( $res ){
			return array(
				'buy_pre'=>$res['buy_pre'],
				'sell_pre'=>$res['sell_pre'],
			);
		}else{
			return 0;
		}
	}
	public function get_sum_list($uid){
		$sql = 'SELECT uid, uname, SUM(deal_money) AS total_money, SUM(salary_money) AS salary_money FROM ';
		$sql .= table('rebate_log') .' WHERE `home_uid`='. (int)$uid .' AND `type` != "master" GROUP BY `uid`';
		$sql = page_moyo($sql);
		$res = dbc(DBCMax)->query($sql)->done();
		if( !$res ) return 0;
		$uid_list = array();
		foreach ($res as $v) {
			$uid_list[] = $v['uid'];
		}
		$userInfo = dbc(DBCMax)->select('members')->in('uid, regdate, phone')->where('`uid` IN('. implode(',' , $uid_list) .')')->done();
		$sellInfo = dbc(DBCMax)->select('seller')->in('userid, sellername')->where('`userid` IN('. implode(',' , $uid_list) .')')->done();
		$ulist = array();
		$slist = array();
		foreach ($userInfo as $v) {
			$ulist[ $v['uid'] ] = $v;
		}
		if( $sellInfo ){
			foreach ($sellInfo as $v) {
				$slist[ $v['userid'] ] = $v;
			}
		}
		foreach ($res as &$v) {
			$v['regtime'] = $ulist[ $v['uid'] ]['regdate'];
			$v['regtime'] = date('Y/m/d',$v['regtime']);
			$v['phone'] = $ulist[ $v['uid'] ]['phone'];
			if( $slist && $slist[ $v['uid'] ]) $v['is_seller'] = '商户';
			else $v['is_seller'] = '普通会员';
		}
		unset($userInfo);
		unset($ulist);
		unset($slist);
		return $res;
	}
	public function get_anybody_list($uid,$who){
		$sql = 'SELECT * FROM '. table('rebate_log') .' WHERE `uid`='. (int)$who .' AND `home_uid`='. (int)$uid .' AND `type` != "master"';
		$sql = page_moyo($sql);
		$res = dbc(DBCMax)->query($sql)->done();
		if( $res ){
			return $res;
		}else{
			return array();
		}
	}
	public function get_my_rebate_list(){
		$sql = "SELECT r.*,t.number,t.mutis,p.flag FROM ". table('rebate_log') ." r LEFT JOIN ". table('ticket') ." t ON r.ticketid = t.ticketid LEFT JOIN ". table('order') ." o ON r.orderid = o.orderid LEFT JOIN ". table('product') ." p ON o.productid = p.id WHERE r.uid='". MEMBER_ID ."' AND r.type='master' ORDER BY r.id DESC";
		$sql = page_moyo($sql);
		$res = dbc(DBCMax)->query($sql)->done();
		if( $res ){
			return $res;
		}else{
			return array();
		}
	}
}
?>