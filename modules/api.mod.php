<?php

/**
 * 模块：终端设备打印API
 * @copyright (C)2014 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package module
 * @name api.mod.php
 * @version 1.0
 */

class ModuleObject extends MasterObject
{
	function ModuleObject( $config )
    {
        $this->MasterObject($config);
		$this->Execute();
    }
	function Execute()
	{
		$getaction = get('action') ? strtolower(get('action')) : strtolower(get('Action'));
		switch($getaction){
			case 'login':
				$this->Login();
				break;
			case 'query':
				$this->Query();
				break;
			case 'validate':
				$this->Validate();
				break;
			case 'print':
				$this->Printdata();
				break;
			case 'cencus':
				$this->Cencus();
				break;
			case 'check':
				$this->Check();
				break;
			default:
				$this->main();
				break;
		}
	}
	public function main()
	{
		$data = array('state' => 'failure','message' => '参数错误');
		$this->data($data);
	}
	
	public function Login()
	{
		$username = get('username');
		$password = get('password');
		$check = handler('member')->CheckMember($username, $password);
		if ($check == -1){
			$data = array('state' => 'failure','message' => '密码错误','sid' => '');
		}elseif ($check == 0){
			$data = array('state' => 'failure','message' => '帐号错误','sid' => '');
		}else{
			$user = handler('member')->GetMemberFields();
			$data = array('state' => 'success','message' => '','sid' => $user['uid']);
		}
		$this->data($data);
	}
	
	public function Query()
	{
		$code = get('code');
		$code_a = explode('#',$code);
		$number = $code_a[0];
		$password = $code_a[1];
		$sid = get('sid');
		if(!$sid || !$code){
			$data = array('state' => 'failure','message' => '券号不能为空','detail' => '');
		}else{
			$coupon = logic('coupon')->TicketGet($number);
			if($coupon){
				if(!($coupon['uid'] == $sid || $coupon['selleruid'] == $sid)){
					$data = array('state' => 'failure','message' => '您没有权限','detail' => '');
				}elseif($password != $coupon['password']){
					$data = array('state' => 'failure','message' => '密码错误','detail' => '');
				}else{
					$detail = $this->getdetail($coupon['ticketid'],2);
					$data = array('state' => 'success','message' => '','detail' => $detail);
				}
			}else{
				$data = array('state' => 'failure','message' => '券号错误','detail' => '');
			}
		}
		$this->data($data);
	}
	
	public function Validate()
	{
		$code = get('code');
		$code_a = explode('#',$code);
		$number = $code_a[0];
		$password = $code_a[1];
		$sid = get('sid');
		if(!$sid || !$code){
			$data = array('state' => 'failure','message' => '券号不能为空','detail' => '');
		}else{
			$result = logic('coupon')->MakeUsed($number, $password, $sid);
			if($result['error']){
				switch ($result['errcode']){
				case 'not-found' :
					$data = array('state' => 'failure','message' => '券号错误','detail' => '');
					break;
				case 'be-used' :
					$data = array('state' => 'failure','message' => '该券已消费','detail' => '');
					break;
				case 'be-overdue' :
					$data = array('state' => 'failure','message' => '该券已过期','detail' => '');
					break;
				case 'be-invalid' :
					$data = array('state' => 'failure','message' => '该券已失效','detail' => '');
					break;
				case 'access-denied' :
					$data = array('state' => 'failure','message' => '您没有权限','detail' => '');
					break;
				case 'password-wrong' :
					$data = array('state' => 'failure','message' => '密码错误','detail' => '');
					break;
				}
			}else{
				$coupon = logic('coupon')->TicketGet($number);
				$detail = $this->getdetail($coupon['ticketid']);
				$data = array('state' => 'success','message' => '','detail' => $detail,'amount' => $coupon['mutis'],'code'=>$coupon['number']);
			}
		}
		$this->data($data);
	}

	
	public function Printdata()
	{
		$username = get('username');
		$password = get('password');
		$code = get('code');
		$code_a = explode('#',$code);
		$number = $code_a[0];
		$tpassword = $code_a[1];
		$sid = get('sid');
		if(!$sid || !$code || !$username || !$password){
			$data = array('state' => 'failure','message' => '数据不完整','detail' => '');
		}else{
			$check = handler('member')->CheckMember($username, $password);
			if ($check == -1){
				$data = array('state' => 'failure','message' => '密码错误','detail' => '');
			}elseif ($check == 0){
				$data = array('state' => 'failure','message' => '帐号错误','detail' => '');
			}else{
				$user = handler('member')->GetMemberFields();
				if($user['uid'] != $sid){
					$data = array('state' => 'failure','message' => '您没有权限','detail' => '');
				}else{
					$coupon = logic('coupon')->TicketGet($number);
					if($coupon){
						if(!($coupon['uid'] == $sid || $coupon['selleruid'] == $sid)){
							$data = array('state' => 'failure','message' => '您没有权限','detail' => '');
						}elseif($tpassword != $coupon['password']){
							$data = array('state' => 'failure','message' => '券密码错误','detail' => '');
						}else{
							$detail = $this->getdetail($coupon['ticketid'],1);
							$data = array('state' => 'success','message' => '','detail' => $detail);
						}
					}else{
						$data = array('state' => 'failure','message' => '券号错误','detail' => '');
					}
				}
			}
		}
		$this->data($data);
	}

	
	public function Cencus()
	{
		$begintime = get('begintime') ? get('begintime') : get('beginTime');
		$endtime = get('endtime') ? get('endtime') : get('endTime');
		$sid = get('sid');
		if(!$sid){
			$data = array('state' => 'failure','message' => '券号不能为空','list' => '');
		}else{
			$coupon = $this->getlist($sid,$begintime,$endtime);
			$data = array('state' => 'success','message' => '');	
			if($coupon){
				foreach($coupon as $key => $val){
					$data['list'][] = array('code'=>$val['number'],'time'=>$val['usetime']);
				}
			}else{
				$data['list'] = '';
			}
		}
		$this->data($data);
	}

	
	public function check()
	{
		$version = get('version');
		$s_version = file_get_contents("apk/version.txt");
		if(empty($version)){
			$data = array('state' => 'failure','message' => '未接收到版本号数据','isdown' => '0','loadurl'=>'');
		}elseif(empty($s_version)){
			$data = array('state' => 'failure','message' => '未获取到版本号数据','isdown' => '0','loadurl'=>'');
		}else{
			if($version == $s_version){
				$data = array('state' => 'success','message' => '','isdown' => '0','loadurl'=>'');
			}else{
				$data = array('state' => 'success','message' => '','isdown' => '1','loadurl'=>ini('settings.site_url').'/apk/Terminal.apk');
			}
		}
		$this->data($data);
	}

	private function data($data)
	{
		$this->xml_output($data);
	}

	private function getlist($sid,$begin='',$finish=''){
		$ts_begin = $begin ? date('Ymd',strtotime($begin)) : 0;
		$ts_finish = $finish ? (date('Ymd',strtotime($finish)) + 1) : 0;
		$sql_where = '';
		if($ts_begin || $ts_finish){
			$ts = array();
			$ts[] = $ts_begin ? 'usetime >= '.$ts_begin : 'usetime >0 ';
			$ts_finish && $ts[] = 'usetime <= '.$ts_finish;
			$where = ' AND '.implode(' AND ', $ts);
		}
		$sql = "SELECT * FROM ".table('ticket')." WHERE uid='".$sid."' AND status='".TICK_STA_Used."'".$where." ORDER BY usetime DESC";
		return dbc(DBCMax)->query($sql)->done();
	}
	private function getdetail($ticketid,$agin = 0){
		$sql = "SELECT t.ticketid,t.orderid,t.number,t.password,t.usetime,t.mutis,t.productid,t.status,o.productprice,o.totalprice,p.sellerid,p.flag,s.sellername FROM ".table('ticket')." t LEFT JOIN ".table('order')." o ON t.orderid = o.orderid LEFT JOIN ".table('product')." p ON t.productid = p.id LEFT JOIN ".table('seller')." s ON p.sellerid = s.id WHERE t.ticketid = '".$ticketid."'";
		$d = dbc(DBCMax)->query($sql)->limit(1)->done();
		$html = $d['sellerid']." ".$d['sellername']."[br]订单编号:".$d['orderid']."[br]订单金额:".$d['totalprice']."[br][br]".$d['productid']." ".$d['flag']." x ".$d['mutis']."[br][br]团购券:".$d['number']." 密码:".$d['password']."[br]消费时间:".$d['usetime']."[br]亿团价:".$d['productprice']."元[br]";
		if($agin == 1){
			$html .= "重新打印:".date('Y-m-d H:i:s',time())."[br]";
		}
		if($agin == 2){
			$html .= "查询结果:";
			switch ($d['status']){
				case TICK_STA_Unused:
					$html .= "优惠券可以消费";break;
				case TICK_STA_Used:
					$html .= "优惠券已被消费";break;
				case TICK_STA_Overdue:
					$html .= "优惠券已经过期";break;
				case TICK_STA_Invalid:
					$html .= "优惠券已经失效";break;
			}
		}else{
			$html .= "验证结果:优惠券消费成功";
		}
		return ENC_IS_GBK ? ENC_G2U($html) : $html;;
	}

	private function xml_output($stream)
	{
		header("Content-type: application/xml;charset=utf-8");
		$xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\r\n<response>\r\n";
		if(is_array($stream)){
			foreach($stream as $key => $val){
				if(is_string($val)){
					$xml .= "<".$key.">".$val."</".$key.">\r\n";
				}elseif(is_array($val)){
					$xml .= "<".$key.">\r\n";
					foreach($val as $ke => $va){
						if($key == 'detail' && is_string($va)){
							$xml .= "<".$ke.">".$va."</".$ke.">\r\n";
						}elseif($key == 'list' && is_array($va)){
							$xml .= "<data>\r\n";
							foreach($va as $k => $v){
								if(is_string($v)){
									$xml .= "<".$k.">".$v."</".$k.">\r\n";
								}
							}
							$xml .= "</data>\r\n";
						}
					}
					$xml .= "</".$key.">\r\n";
				}
			}
		}
		$xml .= "</response>";
		exit($xml);
	}
}
?>