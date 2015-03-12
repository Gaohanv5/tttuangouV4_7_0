<?php

/**
 * 模块：产品管理
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package module
 * @name product.mod.php
 * @version 1.0
 */

class ModuleObject extends MasterObject
{
	function ModuleObject( $config )
	{
		$this->MasterObject($config);
		$runCode = Load::moduleCode($this);
		$this->$runCode();
	}
	function Main()
	{
		$this->CheckAdminPrivs('product');
		header('Location: ?mod=product&code=vlist');
	}
	function vList()
	{
		$this->CheckAdminPrivs('product');
		logic('product')->Maintain();
				$filter = '1';
		if(isset($_GET['prosta'])){
			$prosta = get('prosta', 'int');
			is_numeric($prosta) && $filter .= ' AND p.status='.$prosta;
		}
		if(isset($_GET['prodsp'])){
			$prodsp = get('prodsp', 'int');
			is_numeric($prodsp) && $filter .= ' AND p.display='.$prodsp;
		}
				if(MEMBER_ROLE_TYPE == 'seller'){
			$pids = logic('product')->GetUserSellerProduct(MEMBER_ID);
			$asql = 0;
			if($pids){
				$asql = implode(',',$pids);
			}
			$filter .= ' AND p.id IN('.$asql.')';
		}
		$list = logic('product')->GetList(-1, null, $filter);
		logic('product')->AVParser($list);
				$drfCount = logic('product')->GetDraftCount();
				include handler('template')->file('@admin/product_list');
	}
	function Add()
	{
		$this->CheckAdminPrivs('product');
		$p = array();
		$p['successnum'] = ini('product.default_successnum');
		$p['virtualnum'] = ini('product.default_virtualnum');
		$p['oncemax'] = ini('product.default_oncemax');
		$p['oncemin'] = ini('product.default_oncemin');
		$p['fundprice'] = '-1';
		$p['multibuy'] = 'true';
		if(MEMBER_ROLE_TYPE == 'seller'){
			$sinfo = dbc(DBCMax)->query('select id,area from '.table('seller')." where userid='".MEMBER_ID."'")->limit(1)->done();
			if($sinfo){
				$p['sellerid'] = (int)$sinfo['id'];
				$p['city'] = (int)$sinfo['area'];
			}else{
				$this->Messager('您还没有录入商家信息，无法添加产品，请联系网站管理员！');
			}
		}
		include handler('template')->file('@admin/product_mgr');
	}
	function Add_image()
	{
		$this->CheckAdminPrivs('product','ajax');
		$pid = get('pid', 'int');
		$id = get('id', 'int');
		if($this->doforbidden($pid)){
			exit('forbidden');
		}
		$p = logic('product')->SrcOne($pid);
		$imgs = explode(',', $p['img']);
		foreach ($imgs as $i => $iid)
		{
			if ($iid == '' || $iid == 0)
			{
				unset($imgs[$i]);
			}
		}
		$imgs[] = $id;
		$new = implode(',', $imgs);
		logic('product')->Update($pid, array('img'=>$new));
		exit('ok');
	}
	function Edit()
	{
		$this->CheckAdminPrivs('product');
		$id = get('id', 'int');
		$did = get('draftID', 'int');
		$queryID = $did ? $did : $id;
		if($this->doforbidden($queryID)){
			$this->Messager('您不可操作该产品！');
		}
		$p = logic('product')->GetOne($queryID);
		$p || exit('PID Invalid');
		$p['id'] = $id;
		$draft = logic('product')->CheckProductDraft($id);
		include handler('template')->file('@admin/product_mgr');
	}
	function Save()
	{
		$this->CheckAdminPrivs('product');
		$data = array();
		$data['name'] = post('name', 'txt');
		$data['flag'] = post('flag', 'txt');
		$data['flag'] || $data['flag'] = $data['name'];
		$data['city'] = post('city', 'int');
		$data['display'] = post('display', 'int');
		$data['sellerid'] = post('sellerid', 'int');
		$data['intro'] = post('intro');		$data['order'] = post('order', 'int');
		$data['content'] = post('content');
		$data['cue'] = post('cue');
		$data['theysay'] = post('theysay');
		$data['wesay'] = post('wesay');
		$data['price'] = max(0, post('price', 'float'));
		$data['nowprice'] = max(0, post('nowprice', 'float'));
		if(isset($_POST['fundprice']) && is_numeric($_POST['fundprice']) && $_POST['fundprice'] >=0){
			$data['fundprice'] = post('fundprice', 'float');
		}else{
			$data['fundprice'] = -1;
		}
		$data['score'] = max(0,post('score', 'int'));
		$data['maxnum'] = max(0, post('maxnum', 'int'));
		$data['begintime'] = strtotime(post('begintime'));
		$data['overtime'] = strtotime(post('overtime'));
		$data['type'] = post('type', 'txt');
		$data['perioddate'] = strtotime(post('perioddate'));
		$data['allinone'] = post('allinone', 'txt');
		$data['weight'] = post('weight', 'int');
		$data['weight'] *= (post('weightunit', 'txt') == 'g') ? 1 : 1000;
		$data['successnum'] = post('successnum', 'int');
		$data['successnum'] < 1 && $data['successnum'] = 1;
		$data['virtualnum'] = post('virtualnum', 'int');
		$data['oncemax'] = max(0, post('oncemax', 'int'));
		$data['oncemin'] = max(1, post('oncemin', 'int'));
		$data['multibuy'] = post('multibuy', 'txt');
		$data['is_countdown'] = post('is_countdown', 'int');
				$data['saveHandler'] = post('saveHandler', 'txt') == 'draft' ? 'draft' : 'normal';
		$isDraft = $data['saveHandler'] == 'draft';
		$draftID = post('draftID', 'int');
		$data['draft'] = $isDraft ? $draftID : 0;
				$noNULL = $isDraft ?  array() : array(
			'name' => '产品名',
			'city' => '产品投放城市',
			'sellerid' => '产品所属商家',
			'price' => '产品原价',
			'nowprice' => '产品现价'
		);
		foreach ($noNULL as $key => $name)
		{
			if ($key == 'nowprice' && is_numeric($data[$key])) continue;
			if (!$data[$key])
			{
				$this->Messager('【'.$name.'】不能为空！', -1);
			}
		}
				if (post('imgs') != '')
		{
			$data['img'] = substr(post('imgs', 'txt'), 0, -1);
		}
				logic('catalog')->ProUpdate($data);
		
		logic('city')->product_on_save($data);
				if ($data['type'] == 'prize')
		{
						$data['successnum'] = $data['successnum'] > $data['virtualnum'] ? $data['virtualnum'] : $data['successnum'];
						$data['multibuy'] = 'false';
		}
				$id = post('id', 'int');
		if ($id == 0)
		{
			$data['addtime'] = time();
			$data['status'] = PRO_STA_Normal;
			$id = logic('product')->Publish($data);
						if($id > 0) {
				logic('product_tag')->save($id, post('tag_ids'));
			}
		}
		else
		{
			if($this->doforbidden($id)){
				$this->Messager('您不可操作该产品！');
			}
						$data['@extra'] = array(
				'category' => post('__catalog_subclass', 'int') > 0 ? post('__catalog_subclass', 'int') : post('__catalog_topclass', 'int'),
				'hideseller' => post('hideseller', 'txt'),
				'irebates' => post('irebates', 'txt'),
				'expresslist' => post('expresslist'),
				'specialPayment' => post('specialPayment', 'txt'),
				'specialPaymentSel' => post('specialPaymentSel') ? (implode(',', post('specialPaymentSel')).',') : ''
			);
			$allow2CSaveHandler = logic('product')->allowCSaveHandler($id, $data['saveHandler']);
			if ($allow2CSaveHandler)
			{
				logic('product')->Update($id, $data);
			}
			else
			{
				zlog('product')->saveError($id, '非法的草稿保存请求！');
				$alert = '保存失败！! 草稿源数据已被删除或者非法的草稿保存请求！';
				if ($isDraft)
				{
					exit(jsonEncode(array('status'=>'failed','msg'=> $alert)));
				}
				{
					$this->Messager($alert, -1);
				}
			}
		}
		if(MEMBER_ROLE_TYPE == 'seller'){
			$sinfo = dbc(DBCMax)->query('select id,area from '.table('seller')." where userid='".MEMBER_ID."'")->limit(1)->done();
			$data['sellerid'] = (int)$sinfo['id'];
			$data['city'] = (int)$sinfo['area'];
		}
				$hideSeller = post('hideseller', 'txt');
		if ($hideSeller == 'true')
		{
			meta('p_hs_'.$id, 'yes');
		}
		else
		{
			meta('p_hs_'.$id, null);
		}
				$inviteRebates = post('irebates', 'txt');
		if ($inviteRebates == 'true')
		{
			meta('p_ir_'.$id, 'yes');
		}
		else
		{
			meta('p_ir_'.$id, null);
		}
				if (post('expresslist', 'trim') != '')
		{
			meta('expresslist_of_'.$id, post('expresslist'));
		}
		else
		{
			meta('expresslist_of_'.$id, null);
		}
				$specialPayment = post('specialPayment', 'txt');
		if ($specialPayment == 'true')
		{
			$paymentSel = post('specialPaymentSel');
			if ($paymentSel)
			{
				$listString = '';
				foreach ($paymentSel as $i => $pCode)
				{
					$listString .= $pCode.',';
				}
				meta('paymentlist_of_'.$id, $listString);
			}
		}
		else
		{
			meta('paymentlist_of_'.$id, null);
		}
				$notifyType = post('notifyType', 'txt');
		if ($notifyType != '-1')
		{
			if (ini('notify.api.'.$notifyType.'.enabled'))
			{
				meta('p_nt_'.$id, $notifyType);
			}
			else
			{
				meta('p_nt_'.$id, null);
			}
		}
		else
		{
			meta('p_nt_'.$id, null);
		}
				logic('product')->PresellSubmit($id);
				logic('attrs')->ProductSubmit($id);
				logic('gps')->seller_linker($data['sellerid'], $id);
				$isDraft || logic('product')->Maintain($id);
		$isDraft && exit(jsonEncode(array('status'=>'ok','pid'=>$id)));
		logic('product')->ClearDraft($id, $draftID);
		$this->Messager('产品数据更新完成！', '?mod=product&code=vlist');
	}
	function Save_intro()
	{
		$this->CheckAdminPrivs('product','ajax');
		$id = get('id', 'int');
		$intro = get('intro', 'txt');
		if($this->doforbidden($id)){
			exit('forbidden');
		}
		logic('upload')->Field($id, 'intro', $intro);
		exit('ok');
	}
	function Draft_restore()
	{
		$this->CheckAdminPrivs('product','ajax');
		$pid = get('pid', 'int');
		$did = get('did', 'int');
		if($this->doforbidden($pid)){
			exit('forbidden');
		}
		logic('product')->ClearDraft($pid, $did, $did);
		exit('admin.php?mod=product&code=edit&id='.$pid.'&draftID='.$did.'&~iiframe=yes');
	}
	function Draft_list()
	{
		$this->CheckAdminPrivs('product');
		$list = logic('product')->GetDraftList();
		include handler('template')->file('@admin/product_draft_list');
	}
	function Draft_del()
	{
		$this->CheckAdminPrivs('product');
		$this->Draft_clear(false);
		$this->Messager('已经删除！');
	}
	function Draft_clear($exit = true)
	{
		$this->CheckAdminPrivs('product','ajax');
		$pid = get('pid', 'int');
		$did = get('did', 'int');
		if($this->doforbidden($pid)){
			exit('forbidden');
		}
		logic('product')->ClearDraft($pid, $did);
		$exit && exit('ok');
	}
	function Del()
	{
		$this->CheckAdminPrivs('product');
		$id = get('id', 'int');
		if($this->doforbidden($id)){
			$this->Messager('您不可操作该产品！');
		}
		logic('product')->Delete($id);
		$this->Messager('产品成功删除！', '?mod=product&code=vlist');
	}
	function Del_image()
	{
		$this->CheckAdminPrivs('product','ajax');
		$pid = get('pid', 'int');
		$id = get('id', 'int');
		$p = logic('product')->SrcOne($pid);
		if ($p['img'] == '')
		{
						logic('upload')->Delete($id);
		}
		else
		{
			$imgs = explode(',', $p['img']);
			foreach ($imgs as $i => $iid)
			{
				if ($iid == $id)
				{
					logic('upload')->Delete($id);
					unset($imgs[$i]);
				}
			}
			$new = implode(',', $imgs);
			logic('product')->Update($pid, array('img'=>$new));
		}
		exit('ok');
	}
	function Quick_listCity()
	{
		$cid = get('icity', int);
		$list = array(
			array(
				'cityid' => 0,
				'cityname' => '请选择城市',
				'shorthand' => '__#__'
			)
		);
		$list = array_merge($list, logic('misc')->CityList());
		foreach ($list as $i => $one)
		{
			$sel = '';
			if ($one['cityid'] == $cid)
			{
				$sel = ' selected="selected"';
			}
			echo '<option value="'.$one['cityid'].'"'.$sel.'>'.$one['cityname'].'</option>';
		}
		exit;
	}
	function Quick_addCity()
	{
		$this->CheckAdminPrivs('city','ajax');
		$name = get('name', 'txt');
		$flag = get('flag', 'txt');
		$data = array(
			'cityname' => $name,
			'shorthand' => $flag,
			'display' => 1
		);
		dbc()->SetTable(table('city'));
		$r = dbc()->Insert($data);
		exit($r ? (string)$r : '添加失败！');
	}
	function Quick_addSeller()
	{
		$this->CheckAdminPrivs('seller','ajax');
				$username = get('username', 'txt');
		$password = '123456';
		$rr = logic('seller')->Register($username, $password);
		$rr['error'] && exit($rr['result']);
		$uid = $rr['result'];
				$city = get('city', 'int');
		$sellername = get('sellername', 'txt');
		$sid = logic('seller')->Add($city, $uid, $sellername);
				exit($sid ? (string)$sid : '添加失败！');
	}
	private function doforbidden($productid){
		$return = false;
		if(MEMBER_ROLE_TYPE == 'seller'){
			$pids = logic('product')->GetUserSellerProduct(MEMBER_ID);
			if(!in_array($productid,$pids)){
				$return = true;
			}
		}
		return $return;
	}

	function linklist()
	{
		$this->CheckAdminPrivs('product');
		if(MEMBER_ROLE_TYPE == 'seller'){
			$sellerid = logic('seller')->U2SID(MEMBER_ID);
		}
		$list = logic('product')->get_link_list($sellerid);
		include handler('template')->file('@admin/product_link_list');
	}

	function addlink()
	{
		$this->CheckAdminPrivs('product');
		if(MEMBER_ROLE_TYPE == 'seller'){
			$sellerid = logic('seller')->U2SID(MEMBER_ID);
		}else{
			$step = 'seller';
		}
		if($step == 'seller'){
			$citys = dbc(DBCMax)->query("SELECT cityid,cityname FROM `".table('city')."` WHERE cityid IN(SELECT DISTINCT `area` FROM ".table('seller').")")->done();
		}
		if(post('sidpost')){
			$sellerid = post('sidpost');
			$step = '';
		}
		if($sellerid){
			$list = logic('product')->GetOwnerLink($sellerid);
		}
		include handler('template')->file('@admin/product_link');
	}

	function editlink()
	{
		$this->CheckAdminPrivs('product');
		$linkid = get('id','int');
		$linkinfo = logic('product')->get_link_product($linkid);
		if($linkinfo){
			$sellerid = $linkinfo['sellerid'];
			if(MEMBER_ROLE_TYPE == 'seller' && $sellerid != logic('seller')->U2SID(MEMBER_ID)){
				$this->Messager('您没有权限进行该操作！', '?mod=product&code=linklist');
			}
		}else{
			$this->Messager('该套餐不存在！', '?mod=product&code=linklist');
		}
		if($sellerid){
			$list = logic('product')->GetOwnerLink($sellerid);
		}
		include handler('template')->file('@admin/product_link');
	}

	function addlinksave()
	{
		$this->CheckAdminPrivs('product');
		$data = array();
		$linkid = max(0, post('linkid','int'));
		$sellerid = max(0, post('sellerid','int'));
		$link_product_ids = post('link_product_ids');
		$link_product_names = post('link_product_names');
		if($sellerid && $link_product_ids && $link_product_names){
			foreach($link_product_ids as $key => $val){
				if($val && trim($link_product_names[$key])){
					$data[] = array('pid'=>$val,'name'=>$link_product_names[$key]);
				}
			}
		}
		if(count($data) > 1){
			if($linkid > 0){
				$return = logic('product')->updatelink($linkid,$data);
				$this->Messager('套餐编辑成功！', '?mod=product&code=linklist');
			}else{
				$return = logic('product')->linksave($sellerid,$data);
				$this->Messager('套餐添加成功！', '?mod=product&code=linklist');
			}
		}else{
			$this->Messager('套餐数据不符合要求，添加失败！', '?mod=product&code=linklist');
		}
	}

	function dellink()
	{
		$this->CheckAdminPrivs('product');
		$id = get('id','int');
		if(logic('product')->check_link_byid($id)){
			logic('product')->deletelink($id);
			$this->Messager('套餐删除成功！', '?mod=product&code=linklist');
		}else{
			$this->Messager('您没有权限进行操作！', '?mod=product&code=linklist');
		}
	}
}

?>