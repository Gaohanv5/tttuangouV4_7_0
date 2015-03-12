<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name role.mod.php
 * @date 2014-09-01 17:24:23
 */
 



class ModuleObject extends MasterObject
{
	
	var $ID = 0;

	
	function ModuleObject($config)
	{
		$this->MasterObject($config);
		$this->ID = (int)$this->Get['id']?(int)$this->Get['id']:(int)$this->Post['id'];
		Load::moduleCode($this);$this->Execute();
	}

	
	function Execute()
	{
		switch($this->Code)
		{
			case 'list':
				$this->Main();
				break;
			case 'add':
				$this->Add();
				break;
			case 'doadd':
				$this->DoAdd();
				break;
			case 'delete':
				$this->DoDelete();
				break;
			case 'modify':
				$this->Modify();
				break;
			case 'domodify':
				$this->DoModify();
				break;
			default:
				$this->Main();
				break;
		}
	}

	
	function Main()
	{
		$this->CheckAdminPrivs('rolemanage');
		exit('hello,how are you!');
		$sql="SELECT * FROM	".TABLE_PREFIX.'system_role';
		$query = $this->DatabaseHandler->Query($sql);
		$role_list=array();
		while($row = $query->GetRow())
		{
			$role_list[] = $row;
		}
		include handler('template')->file('@admin/role_list');
	}

	
	 function Add()
	 {
		 $this->CheckAdminPrivs('rolemanage');
		 exit('hello,how are you!');
		 $action="admin.php?mod=role&code=doadd";
		 $title="添加";
		 unset($privs_list);
		 include(CONFIG_PATH . 'admin_privs.php');
		 if($privs_list && is_array($privs_list)){
			foreach($privs_list as $key => $val){
				if($val['sub_priv_list'] && is_array($val['sub_priv_list'])){
					$sub_privgroup = array();
					foreach($val['sub_priv_list'] as $k => $v){
						$sub_privgroup[] = $v['priv'];
					}
					$privs_list[$key]['privgroup'] = implode(',',$sub_privgroup);
				}
			}
		}
		 include handler('template')->file('@admin/role_info');
	 }

	
	 function DoAdd()
	 {
		 $this->CheckAdminPrivs('rolemanage');
		 exit('hello,how are you!');
		 $rname = trim($this->Post['rolename']);
		 if(!$rname){
			 $this->Messager("角色名称不能为空！");
		 }
		 $roleinfo = dbc(DBCMax)->query('select * from '.table('role').' where name="'.$rname.'"')->limit(1)->done();
		 if($roleinfo){
			 $this->Messager("该角色已经存在!");
		 }
		 $privs = post('privs_code');
		 if($privs && is_array($privs)){
			 $privs[] = 'index';
			 $dataprivs = implode(',',$privs);
		 }else{
			$dataprivs = '';
		 }
		 $data=array('name'=>$rname,'privs'=>$dataprivs);
		 $this->DatabaseHandler->SetTable(TABLE_PREFIX.'system_role');
		 $result=$this->DatabaseHandler->Insert($data);
		 if($result!=false)
		 {
			 $this->Messager("添加成功",'admin.php?mod=role');
		 }
		 else
		 {
		 	$this->Messager("添加失败");
		 }
	 }

	 
	 function DoDelete()
	 {
		$this->CheckAdminPrivs('rolemanage');
		exit('hello,how are you!');
		$return = dbc(DBCMax)->query('DELETE FROM '.table('role').' where id='.$this->ID)->done();
		$return && dbc(DBCMax)->update('members')->data("role_id=0")->where('role_id='.$this->ID)->done();
		$this->Messager("角色删除成功","admin.php?mod=role");
	 }

	
	 function Modify()
	 {
		 $this->CheckAdminPrivs('rolemanage');
		 exit('hello,how are you!');
		 $roleinfo = dbc(DBCMax)->query('select * from '.table('role').' where id='.$this->ID)->limit(1)->done();
		 if(!$roleinfo){
			 $this->Messager("您要编辑的角色不存在!");
		 }
		 $action="admin.php?mod=role&code=domodify";
		 $title="修改";
		 unset($privs_list);
		 include(CONFIG_PATH . 'admin_privs.php');
		 if($privs_list && is_array($privs_list)){
			foreach($privs_list as $key => $val){
				if($val['sub_priv_list'] && is_array($val['sub_priv_list'])){
					$sub_privgroup = array();
					foreach($val['sub_priv_list'] as $k => $v){
						if(in_array($v['priv'],explode(',',$roleinfo['privs']))){
							$privs_list[$key]['sub_priv_list'][$k]['check'] = ' checked';
						}
						$sub_privgroup[] = $v['priv'];
					}
					$privs_list[$key]['privgroup'] = implode(',',$sub_privgroup);
				}
			}
		}
		 include handler('template')->file('@admin/role_info');
	 }


	
	 function DoModify()
	 {
		 $this->CheckAdminPrivs('rolemanage');
		 exit('hello,how are you!');
		 $roleinfo = dbc(DBCMax)->query('select * from '.table('role').' where id='.$this->ID)->limit(1)->done();
		 if(!$roleinfo){
			 $this->Messager("您要编辑的角色不存在!");
		 }
		 $rname = trim($this->Post['rolename']);
		 if(!$rname){
			 $this->Messager("角色名称不能为空！");
		 }
		 $privs = post('privs_code');
		 if($privs && is_array($privs)){
			 $privs[] = 'index';
			 $dataprivs = implode(',',$privs);
		 }else{
			$dataprivs = '';
		 }
		 $data=array('name'=>$rname,'privs'=>$dataprivs);
		 dbc(DBCMax)->update('role')->data($data)->where('id='.$this->ID)->done();
		 dbc(DBCMax)->update('members')->data(array('privs'=>$dataprivs))->where('role_id='.$this->ID)->done();
		 $this->Messager("编辑成功");
	 }
}
?>