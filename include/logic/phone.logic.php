<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name phone.logic.php
 * @date 2014-12-11 14:44:49
 */
 




class PhoneLogic
{
	public function view($phone = null) {
		$phone = (is_null($phone) ? user()->get('phone') : $phone);
		return ($phone ? substr($phone, 0, 3) . '****' . substr($phone, -4) : '');
	}

	
	public function GetOne($key, $val, $vfd = false)
	{
		if(false == in_array($key, array('id', 'uid', 'phone'))) {
			return false;
		}
		$val = (is_numeric($val) ? $val : 0);
		if(empty($val)) {
			return false;
		}
		$q = dbc(DBCMax)->select('phone')->where(array($key=>$val));
		$vfd && $q->where('vftime>0');
		return $q->order(' `id` DESC ')->limit(1)->done();
	}
	public function Check($phone, $check_exists = true) {
		$phone = (is_numeric($phone) ? (string) $phone : 0);
		if(empty($phone)) {
			return '手机号不能为空';
		}
		if(11 != strlen($phone)) {
			return '请输入11位的手机号码';
		}
		if(false == preg_match('~^1(3|4|5|7|8)[0-9]{9}$~', $phone)) {
			return '请输入正确的手机号码';
		}
		if(false !== $check_exists) {
			$uid = ((is_numeric($check_exists) && $check_exists > 0) ? $check_exists : ((defined('MEMBER_ID') && MEMBER_ID > 0) ? MEMBER_ID : user()->get('id')));
			
			$phone_members = dbc(DBCMax)->select('members')->where(array('phone'=>$phone))->limit(1)->done();
			if($phone_members && $phone_members['phone_validate']) {
				if($uid > 0 && $uid == $phone_members['uid']) {
					;
				} else {
					return '该手机号已经被其他人在使用了';
				}
			}
			$username_members = dbc(DBCMax)->select('members')->where(array('username'=>$phone))->limit(1)->done();
			if($username_members && $username_members['phone_validate']) {
				if($uid > 0 && $uid == $phone_members['uid']) {
					;
				} else {
					return '该手机号已经被其他人使用了';
				}
			}
		}
		return ;
	}
	
	public function VfSend($phone, $uid = 0)
	{
		$phone = (is_numeric($phone) ? (string) $phone : 0);
		if(false != ($ret = self::Check($phone, $uid))) {
			return $ret;
		}

		$res = $this->GetOne('phone', $phone);
		$resID = 0;
		$uid === 0 && $uid = user()->get('id');

		$userData = array('uid' => (int) $uid, 'phone' => $phone);
		if ($res)
		{
			if($res['stime'] + 60 > time())
			{
				return '刚刚已经发送过了，请60秒后再试！';
			}
									if(false !== $uid && $res['uid'] > 0)
			{
				if($uid != $res['uid'])
				{
					return '此手机号码已经被其他用户绑定！';
				}
			}
						$resID = $res['id'];
		}
		else
		{
			$res = dbc(DBCMax)->select('phone')->where($userData)->order(' `id` DESC ')->limit(1)->done();
			if($res)
			{
				if($res['stime'] + 60 > time())
				{
					return '刚刚已经发送过了，请60秒后再试！';
				}
				$resID = $res['id'];
			}
			else
			{
				$resID = dbc(DBCMax)->insert('phone')->data($userData)->done();
			}
		}

		if(false == $resID) {
			return '未知错误，记录写入失败';
		}

				$vfCode = mt_rand(100000, 999999);
		$this->update($resID, array_merge($userData, array('stime'=>time(), 'vfcode'=>$vfCode, 'vftime'=>0, 'vftimes'=>0)));

		

						$sms = '您的验证码为'.$vfCode.'，15分钟内有效。';
				logic('push')->addi('sms', $phone, array('content'=>$sms));

		return ;
	}
	
	public function Vfcode($phone, $vfcode, $uid = 0)
	{
		$uid === 0 && $uid = user()->get('id');
		$phone = (is_numeric($phone) ? (string) $phone : 0);
		if(false != ($ret = self::Check($phone, false))) {
			return $ret;
		}

		if(empty($vfcode))
		{
			return '手机验证码不能为空';
		}

		$res = $this->GetOne('phone', $phone);
		if (!$res)
		{
			return '此手机号码还未发送过验证码 ';
		}

				$this->update($res['id'], array(
				'vftimes' => $res['vftimes'] + 1,
			));

		if($res['stime'] > 0 && $res['stime'] + 900 < time())
		{
			return '验证码输入超时，请重新发起验证 ';
		}
		if($res['vftimes'] > 10)
		{
			return '验证码输入错误次数过多，请重新发起验证 ';
		}
		
		if ($res['vfcode'] != $vfcode)
		{
			return '验证码输入错误，请重试 ';
		}

				$this->update($res['id'], array(
				'uid' => $uid,
				'vftime' => time(),
				'vfcode' => '',
			));

		if($uid > 0) {
			return self::bind($phone, $uid);
		}

		return ;
	}

	public function update($id, $data = array())
	{
		$ret = false;
		$id = (is_numeric($id) ? $id : 0);
		if($id > 0 && $data) {
			$row = $this->GetOne('id', $id);
			if($row) {
				$ret = dbc(DBCMax)->update('phone')->data($data)->where(array('id' => $id))->done();
			}
		}
		return $ret;
	}

	
	public function bind($phone, $uid, $check = true) {
		$uid = (is_numeric($uid) ? $uid : 0);
		$phone = (is_numeric($phone) ? $phone : 0);

		if($phone && $uid > 0) {
			if(false != $check && false != ($ret = self::Check($phone, $uid))) {
				return $ret;
			}

			$where = array('phone'=>$phone, 'uid'=>$uid,);
			$rets = dbc(DBCMax)->select('phone')->where($where)->done();
			$data = array_merge($where, array('btime'=>time()));
			if(1 === count($rets) && $rets[0]['id'] > 0)  {
				$this->update($rets[0]['id'], $data);
			} else {
				$this->unbind($phone, $uid);
				dbc(DBCMax)->insert('phone')->data($data)->done();
			}

			dbc(DBCMax)->update('members')->data(array('phone' => '', 'phone_validate' => 0))->where(array('phone' => $phone))->done();
			dbc(DBCMax)->update('members')->data(array('phone' => $phone, 'phone_validate' => 1))->where(array('uid' => $uid))->done();
		}

		return ;
	}

	
	public function unbind($phone, $uid = 0) {
		$phone = (is_numeric($phone) ? $phone : 0);
		dbc(DBCMax)->delete('phone')->where(array('phone' => $phone))->done();

		dbc(DBCMax)->update('members')->data(array('phone' => '', 'phone_validate' => 0))->where(array('phone' => $phone))->done();

		$uid = (is_numeric($uid) ? $uid : 0);
		if($uid > 0) {
			dbc(DBCMax)->delete('phone')->where(array('uid' => $uid))->done();

			dbc(DBCMax)->update('members')->data(array('phone' => '', 'phone_validate' => 0))->where(array('uid' => $uid))->done();
		}

		dbc(DBCMax)->delete('phone')->where(" `uid`='0' AND `stime`<'" . (time() - 1000) . "' ")->done();
	}

	public function rebind2($uid = 0) {
		return self::rebind($uid, 'phone');
	}
	public function dorebind2($oldphone, $phone, $vfcode, $uid = 0) {
		return self::dorebind($oldphone, $phone, $vfcode, $uid, 'phone');
	}

	public function rebind($uid = 0, $checkby = 'email') {
		$uid = (is_numeric($uid) ? $uid : 0);
		$uid === 0 && $uid = user()->get('id');
		if($uid < 1) {
			return '用户UID不能为空';
		}

		$user = user($uid)->get();
		if(empty($user)) {
			return '用户已经不存在了';
		}

		if(empty($user['phone']) || false == $user['phone_validate']) {
			return '您还未绑定手机号码';
		}

		$member = $this->__member($uid);
		if(false == $member) {
			return '用户已经不存在了!';
		}

		if('phone' == $checkby) {
			return ;
		}

		if(empty($user['email']) || false == $user['checked']) {
			return '您的邮箱未通过验证，请联系网站管理员';
		}

		$timestamp=time();
        if ($member['authstr']) {
            list($dateline, $operation, $idstring) = explode("|", $member['authstr']);
            $inteval=300;            if ($dateline+$inteval>$timestamp) {
                return "请不要重复恶意发送，您的请求已经发送到您的邮箱中，如有问题，请与网站管理员联系。";
            }
        }

        $mvfcode = random(8, 1);
        $member['authstr'] = "$timestamp|1|$mvfcode";
        $member['auth_try_times'] = 0;
        dbc()->SetTable(TABLE_PREFIX.'system_memberfields');
        $result=dbc()->Update($member,"uid={$member['uid']}");
        if ($result==false)
        {
            dbc()->Insert($member);
        }
        $onlineip=$_SERVER['REMOTE_ADDR'];
        $settings = ini('settings');
                $email_message="您在 {$settings['site_name']} 的验证码 $mvfcode ，在30分钟内输入有效 （请勿将验证码泄漏给其他人）
<br/>
本请求提交者的 IP 为 $onlineip<br/>
此致<br/><br/>
{$settings['site_url']}";
        $subject="[{$settings['site_name']}] 验证码";

        logic('service')->mail()->Send($member['email'], $subject, $email_message);

		return ;
	}

	public function dorebind($mvfcode, $phone, $vfcode, $uid = 0, $checkby = 'email') {
		$uid = (is_numeric($uid) ? $uid : 0);
		$uid === 0 && $uid = user()->get('id');
		if($uid < 1) {
			return '用户UID不能为空';
		}

		$phone = (is_numeric($phone) ? (string) $phone : 0);
		if(false != ($ret = self::Check($phone, false))) {
			return $ret;
		}

		if(empty($vfcode)) {
			return '手机验证码不能为空';
		}

		$user = user($uid)->get();
		if(empty($user)) {
			return '用户已经不存在了';
		}

		if(empty($user['phone']) || false == $user['phone_validate']) {
			return '您还未绑定手机号码';
		}

		if($user['phone'] == $phone) {
			return '两个手机号码一样的，没有变化啊';
		}

		$member = $this->__member($uid);
		if(false == $member) {
			return '用户已经不存在了!';
		}

		if('phone' == $checkby) {
			if($mvfcode != $user['phone']) {
				return '原先绑定的手机号码输入错误';
			}
		} else {
			if(empty($mvfcode)) {
				return '邮件验证码不能为空';
			}

			if(empty($user['email']) || false == $user['checked']) {
				return '您的邮箱未通过验证，请联系网站管理员';
			}

			$member['auth_try_times'] = (max(0, (int) $member['auth_try_times']) + 1);
	        dbc(DBCMax)->Update('memberfields')->data(array('auth_try_times'=>$member['auth_try_times']))->where(array('uid'=>$uid))->done();
	        if($member['auth_try_times']>=10) {
	        	return '您尝试的错误次数太多了，请重新发起请求!';
	        }

	        $timestamp=time();
	        list($dateline, $operation, $idstring) = explode("|", $member['authstr']);
	        if($dateline < $timestamp - 1000 || $operation != 1 || $idstring != $mvfcode) {
	        	return '邮件验证码错误或已经过期了，请重新发起请求!';
	        }
	    }

        $ret = $this->Vfcode($phone, $vfcode, $uid);
        if($ret) {
        	return $ret;
        }

        self::unbind($user['phone']);

        if('email' == $checkby) {
        	dbc(DBCMax)->Update('memberfields')->data(array('auth_try_times'=>0, 'authstr' => ''))->where(array('uid'=>$uid))->done();
        }

        return ;
	}

	private function __member($uid) {
		$uid = (is_numeric($uid) ? $uid : 0);
		if($uid < 1) {
			return false;
		}

		$sql="
        SELECT
            M.uid,M.username,M.email,M.email2,M.secques,MF.uid as fuid,MF.authstr,MF.auth_try_times
        FROM
            ".TABLE_PREFIX. 'system_members'." M LEFT JOIN ".TABLE_PREFIX.'system_memberfields'." MF ON(M.uid=MF.uid)
        WHERE
            BINARY M.uid='$uid'";
		return dbc(DBCMax)->query($sql)->limit(1)->done();
	}

}

?>
