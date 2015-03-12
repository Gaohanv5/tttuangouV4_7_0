<?php

/**
 * 逻辑区：用户相关
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package logic
 * @name me.logic.php
 * @version 1.0
 */

class MeLogic
{
	
	public function user($uid = null)
	{
		if ($uid === null)
		{
			$uid = handler('member')->MemberFields['uid'];
		}
		$SID = 'logic.me.user.'.$uid;
		$obj = moSpace($SID);
		if ( ! $obj )
		{
			$obj = moSpace($SID, (new MeLogic_User($uid)));
		}
		return $obj;
	}
	
	public function money()
	{
		return loadInstance('logic.me.money', 'MeLogic_Money');
	}
	
	public function role()
	{
		return loadInstance('logic.me.role', 'MeLogic_Role');
	}

	
	var $DatabaseHandler;
	var $Config;
	function MeLogic()
	{
		$this->CookieHandler = handler('cookie');
		$this->DatabaseHandler = dbc();
		$this->Config = &Obj::registry("config");
	}
	function infoMe( $uid )
	{
		$sql = 'SELECT * FROM ' . TABLE_PREFIX . 'system_members WHERE uid=' . $uid;
		return $this->DatabaseHandler->Query($sql)->GetRow();
	}
	function ShowMoneyLog( $user )
	{
		$page = intval($_REQUEST['page']) == false ? 1 : intval($_REQUEST['page']);
		$sql = 'SELECT count(*) from ' . TABLE_PREFIX . 'tttuangou_usermoney where userid = ' . intval($user);
		$query = $this->DatabaseHandler->Query($sql);
		$num = $query->GetRow();
		$num = $num['count(*)'];
		$pagenum = 20; 		$page_arr = page($num, $pagenum, $query_link, $_config);
		$sql = 'select * from ' . TABLE_PREFIX . 'tttuangou_usermoney where userid = ' . intval($user) . ' order by `mid` desc limit  ' . ($page - 1) * $pagenum . ',' . $pagenum;
		$query = $this->DatabaseHandler->Query($sql);
		$moneylog = $query->GetAll();
		$moneylog['page_arr'] = $page_arr;
		return $moneylog;
	}
	function mailCron( $ary )
	{
		$keys = $values = '';
		foreach ( $ary as $i => $valuez )
		{
			$a = $i == 'addtime' ? "" : ',';
			$keys .= '`' . $i . '`' . $a;
			$values .= '\'' . $valuez . '\'' . $a;
		}
		$sql = 'insert into ' . TABLE_PREFIX . 'tttuangou_cron (' . $keys . ') VALUES (' . $values . ')';
		$this->DatabaseHandler->Query($sql);
	}
	function finder( $uid, $productid )
	{
		if (!meta('p_ir_'.$productid))
		{
			return false;
		}
		$sql = 'select finder,findtime from ' . TABLE_PREFIX . 'system_members where uid = ' . intval($uid);
		$query = $this->DatabaseHandler->Query($sql);
		if ( ! $query ) return false;
		$finder = $query->GetRow();
		$finderid = $finder['finder'];
		$findtime = $finder['findtime'];
		if ( $finderid == 0 || $findtime == 0 )
		{
			return false;
		}
		if ( $findtime + (72 * 3600) < time() )
		{
			return false;
		}
				$sql = 'select count(*) from ' . TABLE_PREFIX . 'tttuangou_order where userid = ' . intval($uid) . ' AND paytime > 0';
		$query = $this->DatabaseHandler->Query($sql);
		$result = $query->GetRow();
		if ( $result['count(*)'] > 1 )
		{
			return false;
		}
		$ary = array(
			'buyid' => $uid, 'buytime' => time(), 'productid' => $productid, 'finderid' => $finderid, 'findtime' => $findtime
		);
		$this->DatabaseHandler->SetTable(TABLE_PREFIX . 'tttuangou_finder');
		$result = $this->DatabaseHandler->Insert($ary);
		return true;
	}
	function finderList( $uid )
	{
		$sql = 'select f.*,p.flag,m.username from ' . TABLE_PREFIX . 'tttuangou_finder f left join ' . TABLE_PREFIX . 'tttuangou_product p on (p.id=f.productid) left join ' . TABLE_PREFIX . 'system_members m on (m.uid=f.buyid) WHERE f.finderid=' . intval($uid) . ' order by f.id desc';
		return dbc(DBCMax)->query($sql)->done();
	}
	function productMySavedMoney()
	{
				$sql = 'SELECT o.orderid,o.productnum,p.price,p.nowprice FROM ' . TABLE_PREFIX . 'tttuangou_order o INNER JOIN ' . TABLE_PREFIX . 'tttuangou_product p ON o.productid = p.id WHERE o.userid = ' . MEMBER_ID;
		$result = $this->DatabaseHandler->Query($sql)->GetAll();
		$return['count'] = count($result);
		$sum = 0;
		for ( $i = 0; $i < count($result); $i ++ )
		{
			$sum += $result[$i]['productnum'] * ($result[$i]['price'] - $result[$i]['nowprice']);
		}
		$return['saves'] = $sum;
		return $return;
	}
	function ticketCreate( $userid, $productid, $orderid )
	{
		Load::logic('product');
		$ProductLogic = new ProductLogic();
		$product = $ProductLogic->productGet($productid, 0, true);
		if ( $product['type'] == 'stuff' )
		{
						return true;
		}
				$rndLength = 12;
		$rndLoop = ceil($rndLength / 3);
		$rndString = '';
		for ( $i = 0; $i < $rndLoop; $i ++ )
		{
			$rndString .= ( string )rand(100, 999);
		}
		$rndString = substr($rndString, 0, $rndLength);
				$ticketNumber = $rndString;
		$ticketPassword = rand('100000', '999999');
		$ary = array(
			'uid' => $userid, 'productid' => $productid, 'orderid' => $orderid, 'number' => $ticketNumber, 'password' => authcode($ticketPassword, 'ENCODE', $this->Config['auth_key']), 'status' => 1
		);
		$this->DatabaseHandler->SetTable(TABLE_PREFIX . 'tttuangou_ticket');
		$result = $this->DatabaseHandler->Insert($ary);
		$sms = ConfigHandler::get('sms');
		if ( $sms['power'] == 'on' )
		{
									$sql = 'SELECT * FROM ' . TABLE_PREFIX . 'system_members WHERE uid=' . $userid;
			$userInfo = $this->DatabaseHandler->Query($sql)->GetRow();
			if ( is_numeric($userInfo['phone']) )
			{
								$sql = '
					SELECT
						p.name, p.perioddate, s.sellerphone, s.selleraddress
					FROM
						' . TABLE_PREFIX . 'tttuangou_product p LEFT join ' . TABLE_PREFIX . 'tttuangou_seller s on p.sellerid=s.id
					WHERE p.id=' . $productid;
				$ticketInfo = $this->DatabaseHandler->Query($sql)->GetRow();
				$smsContent = str_replace(array(
					'{user_name}', '{product_name}', '{ticket_number}', '{ticket_password}', '{perioddate}', '{seller_phone}', '{seller_address}', '{site_name}'
				), array(
					$userInfo['username'], $ticketInfo['name'], $ticketNumber, $ticketPassword, date('Y-m-d', $ticketInfo['perioddate']), $ticketInfo['sellerphone'], $ticketInfo['selleraddress'], $this->Config['site_name']
				), $sms['template']);
				Load::functions('sms');
				$result = sms_send($userInfo['phone'], $smsContent);
				$sql = 'INSERT INTO ' . TABLE_PREFIX . 'tttuangou_sms (id, name, phone, content, mid, state)VALUES(NULL, "' . $userInfo['username'] . '", "' . $userInfo['phone'] . '", "' . $smsContent . '", "' . $result['msgid'] . '", "' . $result['msgstate'] . '")';
								$this->DatabaseHandler->Query($sql);
			}
		}
				Load::logic('order');
		$OrderLogic = new OrderLogic();
		$OrderLogic->orderType($orderid, 9);
		return true;
	}
	function mail( $address, $city, $type )
	{
		if ( ! check_email($address) ) return false;
		if ( $type == 0 )
		{
			$sql = 'delete from ' . TABLE_PREFIX . 'tttuangou_subscribe where type="mail" AND target=\'' . $address . '\'';
			$query = $this->DatabaseHandler->Query($sql);
		}
		else
		{
			if ( $city == '' )
			{
				Load::logic('product');
				$this->ProductLogic = new ProductLogic();
				$city = logic('misc')->City('id');
			}
			;
			$sql = 'select count(*) from ' . TABLE_PREFIX . 'tttuangou_subscribe where type="mail" AND target = \'' . $address . '\'';
			$query = $this->DatabaseHandler->Query($sql);
			$result = $query->GetRow();
			$ary = array(
				'type' => 'mail', 'target' => $address, 'city' => $city, 'time' => time()
			);
			$this->DatabaseHandler->SetTable(TABLE_PREFIX . 'tttuangou_subscribe');
			if ( $result['count(*)'] == 0 )
			{
				$result = $this->DatabaseHandler->Insert($ary);
			}
			else
			{
				$result = $this->DatabaseHandler->Update($ary, ' email = \'' . $address . '\'');
			}
		}
	}
	function ticketCheck( &$ticket )
	{
		if(false == $ticket || TICK_STA_Unused != $ticket) {
			return ;
		}

		$sql = 'select perioddate from ' . TABLE_PREFIX . 'tttuangou_product where id=' . (int) $ticket['productid'];
		$product = $this->DatabaseHandler->Query($sql)->GetRow();
		if ( $product['perioddate'] >= time() )
		{
			return;
		}
				$ary = array(
			'status' => TICK_STA_Overdue
		);
		$this->DatabaseHandler->SetTable(TABLE_PREFIX . 'tttuangou_ticket');
		$this->DatabaseHandler->Update($ary, 'ticketid=' . (int) $ticket['ticketid']);
		$ticket['status'] = TICK_STA_Overdue;
	}
	function SendUseMail( $id )
	{
		$sql = 'select t.*,m.email,m.username,p.name,p.perioddate,s.userid from ' . TABLE_PREFIX . 'tttuangou_ticket t left join ' . TABLE_PREFIX . 'system_members m on t.uid=m.uid left join ' . TABLE_PREFIX . 'tttuangou_product p on t.productid = p.id left join ' . TABLE_PREFIX . 'tttuangou_seller s on p.sellerid = s.id  where t.ticketid = ' . intval($id);
		$query = $this->DatabaseHandler->Query($sql);
		$ticket = $query->GetRow();
		if ( $ticket['userid'] != MEMBER_ID || $ticket == '' ) return false;
		$ary = array(
			'address' => $ticket['email'], 'username' => $ticket['username'], 'title' => __('购物券即将到期提示信息'), 'content' => '温馨提示您购买的产品（' . $ticket['name'] . '）'. TUANGOU_STR . '券即将于' . date('Y-m-d', $ticket['perioddate']) . '到期请尽快消费以免过期，请点<a href="' . $this->Config['site_url'] . '">这里</a>查看您的' .TUANGOU_STR . '券！', 'addtime' => time()
		);
		$keys = $values = '';
		foreach ( $ary as $i => $valuez )
		{
			$a = $i == 'addtime' ? "" : ',';
			$keys .= '`' . $i . '`' . $a;
			$values .= '\'' . $valuez . '\'' . $a;
		}
		$sql = 'insert into ' . TABLE_PREFIX . 'tttuangou_cron (' . $keys . ') VALUES (' . $values . ')';
		$this->DatabaseHandler->Query($sql);
		return true;
	}
	function UserMsg( $ary )
	{ 		$this->DatabaseHandler->SetTable(TABLE_PREFIX . 'tttuangou_usermsg');
		$result = $this->DatabaseHandler->Insert($ary);
		return true;
	}

	function growth($ov = 0){

                $growth= (int)user()->get('totalpay');
        $level = ini('credits.level');
        $level = explode(',', $level);

        $vips = 0;
        for ($i=1; $i <= count($level); $i++) {
        	if ($growth >= $level[$i-1]) {
        		$vips = $i;
        	}
        }
        $distance  = $level[$vips] - $growth;
        $distance < 0 && $distance = 0;
        $html .= <<<EOF
当前等级： <font class='R16' ><b>VIP{$vips}</b></font><br/>
当前成长值: {$growth}<br/>
距离下一等级：{$distance}<br/>
成长值 = 购买金额 - 退款金额
EOF;
		if($ov){
			echo '<b>VIP'.$vips.'</b>';
		}else{
			echo $html;
		}
    }
}

/**
 * 扩充类：用户信息管理
 * @author Moyo <dev@uuland.org>
 */
class MeLogic_User
{
	
	private $uid = null;
	
	private $data = null;
	
	public function __construct($uid)
	{
		$this->uid = $uid ? $uid : 0;
	}
	
	public function get( $field = '' )
	{
		$data = $this->__load_all_fields();
		if ($field == '*' || $field == '')
		{
			return $data;
		}
		elseif (array_key_exists($field, $data))
		{
			return $data[$field];
		}
		else
		{
			return false;
		}
	}
	
	public function set($field, $val)
	{
		$data = array(
			$field => $val
		);
		dbc()->SetTable(table('members'));
		dbc()->Update($data, 'uid = '.$this->uid);
	}
	
	private function __load_all_fields()
	{
		if (is_array($this->data))
		{
			return $this->data;
		}
		$map = array(
			'id' => 'uid',
			'name' => 'username'
		);
		$guest = array(
			'id' => -1,
			'name' => __('游客')
		);
		$sql = 'SELECT * FROM '.table('members').' WHERE `uid`='.(int) $this->uid;
		$query = dbc(DBCMax)->query($sql)->limit(1)->done();
		$data = $query ? $query : $guest;
		if ($data)
		{
			foreach ($map as $new => $old)
			{
				if ( array_key_exists($old, $data) )
				{
					$data[$new] = $data[$old];
									}
			}
		}
		else
		{
			$data = $guest;
		}
		$this->data = $data;
		return $data;
	}
	
	public function field($key, $val = false, $life = 0, $readAll = false)
	{
		$this->__field_clear();
								$uid = $this->uid < 0 ? null : $this->uid;
		if ( is_null($uid) )
		{
						$agent = handler('cookie')->GetVar('fagent');
			if (!$agent)
			{
								$string = $_SERVER['HTTP_USER_AGENT'].$_SERVER['REMOTE_ADDR'];
				$agent = substr(md5($string), 12, 6);
				handler('cookie')->SetVar('fagent', $agent, 86400*365);
			}
			$key = $agent.'_'.$key;
		}
				if (is_null($val))
		{
						return dbc(DBCMax)->delete('metas')->where(array('uid'=>$uid,'key'=>$key))->done();
		}
				if (!$val)
		{
						$row = dbc(DBCMax)->select('metas')->where(array('uid'=>$uid,'key'=>$key))->limit(1)->done();
			if (!$row)
			{
				return false;
			}
			$life = $row['life'];
			if ($life > 0)
			{
								$uptime = $row['uptime'];
				$crtime = time();
				if ($crtime - $uptime > $life)
				{
										dbc(DBCMax)->delete('metas')->where('id='.$row['id'])->done();
					return false;
				}
			}
			return $readAll ? $row : $row['val'];
		}
				if (is_string($life))
		{
						$calc = array(
				'd' => 86400,
				'h' => 3600,
				'm' => 60,
				's' => 1
			);
			list($unit, $size) = explode(':', $life);
			$life = (int)$size * $calc[$unit];
		}
		$old = $this->field($key, false, 0, true);
		if ($old)
		{
						$dbc = dbc(DBCMax)->update('metas')->where(array('id'=>$old['id']));
			$data = array(
				'val' => $val,
				'life' => $life,
				'uptime' => time()
			);
		}
		else
		{
						$dbc = dbc(DBCMax)->insert('metas');
			$data = array(
				'uid' => $uid,
				'key' => $key,
				'val' => $val,
				'life' => $life,
				'uptime' => time()
			);
		}
		$dbc->data($data);
		return $dbc->done();
	}
	
	private function __field_clear()
	{
		$isCheck = rand(1, 13);
		if ($isCheck != 13) return;
		$ckey = 'logic.me.field.clear';
		$lastClear = fcache($ckey, dfTimer('com.meta.expired.clean'));
		if ($lastClear) return;
				dbc(DBCMax)->delete('metas')->where('life != 0 AND uptime+life < '.time())->done();
				fcache($ckey, 'UPS:'.time());
	}
	
	public function isAdminLogin()
	{
				return admin_priv('index');
	}
}



/**
 * 扩充类：金额管理
 * @author Moyo <dev@uuland.org>
 */
class MeLogic_Money
{
	
	public function check($uid, $price) {	
		$uid = (int) $uid;	
		$price = round($price, 2);
		if($uid > 0 && $price >= 0) {
			$money = round($this->count($uid), 2);
			return ($money >= $price);
		} else {
			return false;
		}
	}

	
	public function count( $uid = 0 )
	{
		$uid = ($uid > 0) ? $uid : user()->get('id');
		$sql = 'SELECT money FROM ' . table('members') . ' WHERE uid = ' . $uid;
		$query = dbc(DBCMax)->query($sql)->limit(1)->done();
		return $query['money'];
	}
	
	public function add( $moves, $uid = 0, $log = array() )
	{
		$uid = (int) (($uid > 0) ? $uid : user()->get('id'));
		$moves = doubleval($moves);
		if($uid > 0 && $moves >= 0) {
			$money=floatval(logic('me')->money()->count($uid));

			$sql ='UPDATE ' . table('members').' SET money = money + ' . $moves . ' WHERE uid = ' . $uid;
			$query = dbc(DBCMax)->query($sql)->done();		
			
			$this->logCreate($uid, 'plus', $moves, $log, $money);
		}
		return ($query) ? true : false;
	}
	
	public function less( $moves, $uid = 0, $log = array() )
	{
		$uid = (int) (($uid > 0) ? $uid : user()->get('id'));
		$moves = doubleval($moves);
		$userinfo = dbc(DBCMax)->query('select money from '.table('members').' where uid='.$uid)->limit(1)->done();
		if($userinfo && $userinfo['money'] >= 0 && $moves >= 0 && $moves <= $userinfo['money']) {
			$sql ='UPDATE ' . table('members').' SET money = money - ' . $moves . ' WHERE uid = ' . $uid;
			$query = dbc(DBCMax)->query($sql)->done();
			$money=floatval(logic('me')->money()->count($uid));
			$this->logCreate($uid, 'minus', $moves, $log, $money);
		}
		return ($query) ? true : false;
	}
	
	function pay( $moves, $uid = 0, $log = array() )
	{
		$uid = (int) ($uid > 0) ? $uid : user()->get('id');
		$moves = doubleval($moves);
		if($uid > 0 && $moves >= 0) {
			$money=floatval(logic('me')->money()->count($uid));
			
			$sql = 'UPDATE ' . table('members').' SET money = money - ' . $moves . ', totalpay = totalpay + ' . $moves . ' WHERE uid = ' . $uid;
			$query = dbc(DBCMax)->query($sql)->done();
			$this->logCreate($uid, 'minus', $moves, $log, $money);
		}
		return ($query) ? true : false;
	}
	
	public function logCreate( $uid, $type, $moves, $log, $sourcemoney = 0 )
	{
		$data = array(
			'userid' => $uid,
			'type' => $type,
			'money' => $moves,
			'time' => time()
		);
		if (isset($log['name']))
		{
			$data['name'] = $log['name'];
			$data['class'] = 'usr';
		}
		else
		{
			$data['name'] = basename(__FILE__);
			$data['class'] = 'sys';
		}
		if (isset($log['intro']))
		{
			$data['intro'] = $log['intro'];
			$data['class'] = 'usr';
		}
		else
		{
			$data['intro'] = 'logic.me.money.movs';
			$btAll = function_exists('debug_backtrace') ? debug_backtrace() : false;
			if ($btAll)
			{
				$btLength = count($btAll);
				$btLength > 3 && $btLength = 3;
				$btString = '';
				for ($btI = $btLength; $btI > 0; $btI--)
				{
					$btOne = $btAll[$btI-1];
					$btString .= 'FILE:'.basename($btOne['file']).' - LINE:'.$btOne['line'].' - FUNC:'.$btOne['function'].'<br/>';
				}
				$data['intro'] = $btString;
			}
			$data['class'] = 'sys';
		}

				$data['intro'] .= '<br/>余额：'.$sourcemoney;
		dbc()->SetTable(table('usermoney'));
		return dbc()->Insert($data);
	}
	
	public function log( $uid = 0, $class = 'usr' )
	{
		if ($class == '*')
		{
			$sql_limit_class = '1';
		}
		else
		{
			$sql_limit_class = 'class = "'.$class.'"';
		}
		$uid = ($uid > 0) ? $uid : user()->get('id');
		$sql = 'SELECT *
		FROM
			' . table('usermoney') . '
		WHERE
			userid = ' . $uid . '
		AND
			' .$sql_limit_class. '
		ORDER BY
			id
		DESC';
		$sql = page_moyo($sql);
		return dbc(DBCMax)->query($sql)->done();
	}
	
	public function logSearch($where, $limit = false)
	{
		$query = dbc(DBCMax)->select('usermoney')->where($where);
		$limit && $query->limit($limit);
		return $query->done();
	}
	
	public function logUpdate($where, $data)
	{
		return dbc(DBCMax)->update('usermoney')->where($where)->data($data)->done();
	}
	
	
	public function mlog($uid = 0, $class = '', $type = '') {
		$sql_where_uid = $sql_where_class = $sql_where_type = 1;
		$uid = (int) $uid;
		if($uid > 0) {
			$sql_where_uid = " u.userid='{$uid}' ";
		}
		if($class && in_array($class, array('sys', 'usr'))) {
			$sql_where_class = " u.class IN ('".implode("','", (array) $class)."') ";
		}
		if($type && in_array($type, array('plus', 'minus'))) {
			$sql_where_type = " u.type IN ('".implode("','", (array) $type)."') ";
		}
		$sql = 'SELECT m.uid,m.username,u.*
		FROM
			' . table('usermoney') . ' u
		LEFT JOIN
			' . table('members') . ' m 
			ON m.uid=u.userid
		WHERE
			' . $sql_where_uid . '
		AND
			' . $sql_where_class . '
		AND
			' . $sql_where_type . '
		oRDEr bY
			u.id DESC';
		logic('isearcher')->Linker($sql);
		$sql = page_moyo($sql);
		return dbc(DBCMax)->query($sql)->done();
	}
}

/**
 * 扩充类：角色管理
 * @author Moyo <dev@uuland.org>
 */
class MeLogic_Role
{
	public function GetList()
	{
		return dbc(DBCMax)->select('role')->done();
	}
}

?>
