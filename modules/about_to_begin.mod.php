<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name about_to_begin.mod.php
 * @date 2014-12-11 14:44:49
 */
 




class ModuleObject extends MasterObject
{
	var $sms_enabled = false;
	var $mail_enabled = false;

	function ModuleObject( $config )
	{
		$this->MasterObject($config);

		$this->sms_enabled = ini('notify.event.user_product_notify.hook.sms.enabled');
		$this->mail_enabled = ini('notify.event.user_product_notify.hook.mail.enabled');

				$runCode = Load::moduleCode($this);
		$this->$runCode();
	}
	public function main(){		
		$product = logic('product')->GetList(logic('misc')->City('id'), NULL, ' p.begintime > '.time());
				$usePager = get('page', 'int');
		if (ini('ui.igos.dsper') && $mutiView && count($product) > 1)
		{
			logic('product')->reSort($product);
		}
		if($product){
			foreach($product as &$v){
				if( $v['begintime'] > time() ){
					$lasttime = $v['begintime'] - time();
					if( $lasttime > 2 * 60 *60 ){
						$v['begin_date'] = date('Y-m-d H:i:s',$v['begintime']);
					}else{
						$v['limit_time'] = $lasttime;
					}
				}
				if( $v['maxnum']==0 ){
					$v['num']=999;
				}else{
					$v['num'] = $v['maxnum'] - $v['sells_count'] + $v['virtualnum'];
				}
				if( $v['num']<0 ){
					$v['num']=0;
				}
				$v['pic'] = imager($v['imgs'][0],IMG_Original);
				$v['overtime'] = date('Y-m-d H:i:s', $v['overtime']);
			}
		}
		$this->Title = "即将开始";
		include handler('template')->file('about_to_begin_main');
	}

	public function notify() {
		if(!$this->sms_enabled && !$this->mail_enabled) {
			$this->Messager('后台未开启开团提醒的功能，请联系管理员');
		}

		$user = user()->get();
		if($user['id'] < 1) {
			$this->Messager('游客不能执行该操作，请先登录', '?mod=account&code=login');
		}

		$id = get('id', int);
		if($id < 1 || false == ($product = logic('product')->GetOne($id))) {
			$this->Messager('指定的产品已经不存在了');
		}
		if($product['begintime'] < time()) {
			$this->Messager('该产品已经开团了', '?view=' . $id);
		}

		if(false == logic('product_notify')->check($id)) {
			$this->Messager('该产品不用设置提醒了');
		}

		$sms = logic('phone')->view($user['phone']);
		$mail = $user['email'];

		$this->Title = "开团提醒";
		$referer = referer('index.php?mod=about_to_begin');
		include handler('template')->file('about_to_begin_notify');		
	}

	public function notify_Save() {
		if(!$this->sms_enabled && !$this->mail_enabled) {
			$this->Messager('后台未开启开团提醒的功能，请联系管理员');
		}

		$user = user()->get();
		if($user['id'] < 1) {
			$this->Messager('游客不能执行该操作，请先登录', '?mod=account&code=login');
		}

		$id = post('id', int);
		if($id < 1 || false == ($product = logic('product')->GetOne($id))) {
			$this->Messager('指定的产品已经不存在了');
		}
		if($product['begintime'] < time()) {
			$this->Messager('该产品已经开团了', '?view=' . $id);
		}

		if(false == logic('product_notify')->check($id)) {
			$this->Messager('该产品不用设置提醒了');
		}

		$notify_type = post('notify_type');
		if(empty($notify_type)) {
			$this->Messager('请选择要提醒的项');
		}
		$data = array('uid'=>$user['id'], 'product_id'=>$id, 'notify_id'=>'user_product_notify', 'notify_type'=>'');
		if($this->sms_enabled && in_array('sms', $notify_type)) {
			$sms = $user['phone_validate'] ? $user['phone'] : post('sms');
			if(false != ($ret = logic('phone')->Check($sms))) {
				$this->Messager($ret);
			}
			$data['notify_type'] = 'sms';
			$data['notify_to'] = $sms;
			logic('product_notify')->save($data);
		}
		if($this->mail_enabled && in_array('mail', $notify_type)) {
			$mail = $user['checked'] ? $user['email'] : post('mail');
			if(false == check_email($mail)) {
				$this->Messager('邮箱输入错误，请重试');
			}
			$data['notify_type'] = 'mail';
			$data['notify_to'] = $mail;
			logic('product_notify')->save($data);
		}

		$referer = referer('index.php?mod=about_to_begin');
		$this->Messager('保存成功了', $referer);
	}
}