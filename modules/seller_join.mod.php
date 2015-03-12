<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name seller_join.mod.php
 * @date 2014-09-01 17:24:23
 */
 




class ModuleObject extends MasterObject
{
	private $uid = 0;
	private $sid = 0;

	private function iniz()
	{
		$this->uid = user()->get('id');
		if ($this->uid < 0)
		{
			$this->Messager('请先登录！', '?mod=account&code=login');
		}
		if(!$this->Config['selleropen']){
			$this->Messager('网站未开启商家自动申请功能！', 'index.php');
		}
		$this->sid = logic('seller')->U2SID($this->uid);
		if ($this->sid > 0)
		{
			$this->Messager('您已经是商家或者已经申请过商家了，请不要重复申请！', '?mod=seller');
		}
	}
	function ModuleObject( $config )
	{
		$this->MasterObject($config);
		$this->iniz();
		$runCode = Load::moduleCode($this);
		$this->$runCode();
	}
	public function main()
	{
		header('Location: '.rewrite('?mod=seller_join&code=info'));
	}

	public function info()
	{
		$rebate = logic('rebate')->Get_Rebate_setting();
		$profit_pre = 0;
		$profit_id = 0;
		$city = logic('misc')->CityList();
		include handler('template')->file('seller_join_table');
	}
	function addmap(){
		extract($this->Get);
		extract($this->Post);
		$x='11728000';
		$y='4320000';
		$z=3;
		if($id!=''){
			$xyz=explode(',',$id);
			$x=$xyz[0];
			$y=$xyz[1];
			$z=$xyz[2];
		}
		include(handler('template')->file('@admin/tttuangou_googlemap'));
	}
	function save(){
		$fields = array('area','sellername','selleraddress','sellerphone','sellerurl','sellermap','profit_id');
		$data = array();
		foreach($fields as $f){
			$data[$f] = $_POST[$f];
		}
		$data['userid'] = user()->get('id');
				if (isset($_FILES['id_card']['name']) && $_FILES['id_card']['error'] == 0){
			$data['id_card'] = 'uploads/images/seller/idcard/'.$data['userid'].'_'.time().'.gif';
			logic('upload')->Save('id_card', ROOT_PATH.$data['id_card']);
		}
		if (isset($_FILES['zhizhao']['name']) && $_FILES['zhizhao']['error'] == 0){
			$data['zhizhao'] = 'uploads/images/seller/zhizhao/'.$data['userid'].'_'.time().'.gif';
			logic('upload')->Save('zhizhao', ROOT_PATH.$data['zhizhao']);
		}		
		$sid = logic('seller')->Join($data);
		if (!$sid) $this->Messager('提交失败！请重试', -1);
		$this->Messager('申请成功', '?mod=seller');
	}
}
?>