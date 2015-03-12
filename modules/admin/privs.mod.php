<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name privs.mod.php
 * @date 2014-09-01 17:24:23
 */
 



class ModuleObject extends MasterObject
{
	function ModuleObject( $config )
	{
		$this->MasterObject($config);
		$runCode = Load::moduleCode($this);
		$this->$runCode();
	}

	public function main()
	{
		$this->CheckAdminPrivs('privs');
		header('Location: admin.php?mod=index');
	}

	public function edit()
	{
		$this->CheckAdminPrivs('privs');
		$action = 'admin.php?mod=privs&code=save';
		unset($privs_list);
		include(CONFIG_PATH . 'admin_privs.php');
		$uid = get('uid', 'int');
		if($uid == '1'){
			$this->Messager("您不能对此管理员的权限进行任何操作");
		}
		$userinfo = dbc(DBCMax)->query('select uid,username,role_id,role_type,privs from '.table('members').' where uid='.$uid)->limit(1)->done();
		if(!$userinfo){
			$this->Messager("该用户不存在");
		}
		if(!in_array($userinfo['role_type'],array('admin','seller'))){
			$this->Messager("您不能设置该用户的后台操作权限");
		}
		if($userinfo && $privs_list && is_array($privs_list)){
			foreach($privs_list as $key => $val){
				if($val['sub_priv_list'] && is_array($val['sub_priv_list'])){
					$sub_privgroup = array();
					foreach($val['sub_priv_list'] as $k => $v){
						if($userinfo['uid'] == '1' || $userinfo['privs'] == 'all' || in_array($v['priv'],explode(',',$userinfo['privs']))){
							$privs_list[$key]['sub_priv_list'][$k]['check'] = ' checked';
						}
						$sub_privgroup[] = $v['priv'];
					}
					$privs_list[$key]['privgroup'] = implode(',',$sub_privgroup);
				}
			}
		}
		include handler('template')->file('@admin/privs_list');
	}

	
	public function save()
	{
		$this->CheckAdminPrivs('privs');
		$uid = post('uid', 'int');
		if($uid == '1'){
			$this->Messager("您不能对此管理员的权限进行任何操作");
		}
		$userinfo = dbc(DBCMax)->query('select uid,username,role_id,role_type,privs from '.table('members').' where uid='.$uid)->limit(1)->done();
		if(!$userinfo){
			$this->Messager("该用户不存在");
		}
		if(!in_array($userinfo['role_type'],array('admin','seller'))){
			$this->Messager("您不能设置该用户的后台操作权限");
		}
		$privs = post('privs_code');
		if($privs && is_array($privs)){
			$privs[] = 'index';
			$dataprivs = implode(',',$privs);
		}else{
			$dataprivs = '';
		}
		if($dataprivs != $userinfo['privs']){
			dbc(DBCMax)->update('members')->data(array('privs'=>$dataprivs,'role_id'=>'0'))->where('uid='.$uid)->done();
		}
		$this->Messager("权限设置成功");
	}
}
?>