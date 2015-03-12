<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name index.mod.php
 * @date 2014-09-01 17:24:22
 */



class ModuleObject extends MasterObject
{
	var $Config = array(); 	function ModuleObject(& $config)
	{
		$this->MasterObject($config);
		Load::moduleCode($this);
		$this->Execute();
	}

	function Execute()
	{
		switch($this->Code)
		{
			case 'home':
				$this->Home();
				break;
			case 'help':
				$this->Help();
				break;
			case 'theme':
				$this->Theme();
				break;
			case 'affiche':
				$this->Affiche();
				break;
			case 'affiche_box':
				$this->AfficheBox();
				break;
			case 'update_recommend':
				$this->updateRecommend();
				break;
			case 'recommend':
				$this->recommend();
				break;
			case 'upgrade_check':
				$this->upgrade_check();
				break;
			case 'lrcmd_nt':
				$this->lrcmd_nt();
				break;
			default:
				$this->Main();
		}
	}

	function main()
	{
		if(MEMBER_ID<1)
		{
			$this->Messager("请先在前台进行<a href='index.php?mod=account&code=login'><b>登录</b></a>",null);
		}
		$menuList = $this->Menu();
		include handler('template')->file('@admin/index');
	}

	function Affiche()
	{
		if(($recommend_list=cache("misc/recommend_list",864000))===false) {
			@$recommend_list=request("recommend",array(),$error);

			if(!$error && is_array($recommend_list) && count($recommend_list)) {
				cache((array) $recommend_list);
			}
		}

		if (!$recommend_list || count($recommend_list) < 1) {
			$recommend_list = $this->_recommendList();
		}

		include(handler('template')->file('@admin/affiche'));
	}

	function AfficheBox()
	{
		if(($recommend_list=cache("misc/recommend_list",864000))===false) {
			@$recommend_list=request("recommend",array(),$error);

			if(!$error && is_array($recommend_list) && count($recommend_list)) {
				cache((array) $recommend_list);
			}
		}

		if (!$recommend_list || count($recommend_list) < 1) {
			$recommend_list = $this->_recommendList();
		}

		$all_recommend_list = array();
		$all_class_list = array();
		$k = 0;
		foreach ($recommend_list as $val) {
			if(!isset($all_class_list[$val['class']])) {
				$all_class_list[$val['class']] = ++$k;
			}

			$all_recommend_list[$all_class_list[$val['class']]][] = $val;
		}
		$n = sizeof($all_class_list);

		include(handler('template')->file('@admin/affiche_box'));
	}

	function _recommendList() {
		return array (
        'overtime' => time() + 86400,
        'string' => '<li class="liS"><a href="http:/'.'/www.jishigou.net" target="_blank">建社区站，用记事狗！</a></li><li class="liS"><a href="http:/'.
        '/tttuangou.net/" target="_blank">地方网站创业：用天天团购系统</a></li><li class="liS"><a href="http:/'.'/biniu.com" target="_blank">比牛，让业务牛逼起来（微信营销平台）</a></li>'
        );
	}


	function Menu()
	{
		global $rewriteHandler,$config;
		$default_open=true;		$open_onlyone=false;
		$open_list=explode('_',$this->Get['open']);
		require(CONFIG_PATH.'admin_left_menu.php');

		foreach ($menu_list as $_key=>$_menu)
		{
			if($_menu['sub_menu_list'])
			{
				foreach ($_menu['sub_menu_list'] as $_sub_key=>$_sub_menu)
				{
					$priv = $_sub_menu['priv'];
					if(!empty($priv) && admin_priv($priv)==false)
					{
						unset($menu_list[$_key]['sub_menu_list'][$_sub_key]);
					}
				}
			}
		}

		$all_open_list=array_keys($menu_list);
		if($default_open && isset($this->Get['open'])==false)
		{
			$open_list=$all_open_list;
		}
		foreach($menu_list as $key=>$menu)
		{
			if ($key == 1)
			{
				foreach ($menu_list as $_menu_list_s)
				{
					foreach((array)$_menu_list_s['sub_menu_list'] as $menu_s)
					{
						if($menu_s['shortcut'])
						{
							$menu['sub_menu_list'][] = $menu_s;
						}
					}
				}
			}
			if(empty($menu['sub_menu_list']))continue;
			$menu_tmp_list[$key]=$menu;
			if(in_array($key,$open_list)!=false)
			{
				$menu_tmp_list[$key]['img']='minus';
				$open_list_tmp=$open_list;
				unset($open_list_tmp[array_search($key, $open_list_tmp)]);
			}
			else
			{
				$menu_tmp_list[$key]['img']='plus';
				$menu_tmp_list[$key]['sub_menu_list']=array();
			}
			if(isset($menu['sub_menu_list']))
			{

				$menu_tmp_list[$key]['link']="?mod=index&code=menu";
				$menu_tmp_list[$key]['target']="";

			}
			else
			{
				$menu_tmp_list[$key]['target']='target="main"';
			}
		}
		$menu_list=$menu_tmp_list;
		return $menu_list;
	}

	function home()
	{
		$program_name = "天天团购";

		$item_list = array(
			'system_members' => array(
				'name' => '用户数',
				'url' => 'admin.php?mod=member&code=dosearch',
		),
			'tttuangou_city' => array(
				'name' => '城市数',
				'url' => 'admin.php?mod=tttuangou&code=city',
		),
			'tttuangou_seller' => array(
				'name' => '商家数',
				'url' => 'admin.php?mod=tttuangou&code=mainseller',
		),
			'tttuangou_product' => array(
				'name' => '产品数',
				'url' => 'admin.php?mod=product',
		),
			'tttuangou_order' => array(
				'name' => '订单数',
				'url' => 'admin.php?mod=order',
		),
			'tttuangou_ticket' => array(
				'name' => TUANGOU_STR . '券数',
				'url' => 'admin.php?mod=coupon',
		),
			'tttuangou_subscribe' => array(
				'name' => '订阅数',
				'url' => 'admin.php?mod=subscribe',
		),
			'tttuangou_question' => array(
				'name' => '问答数',
				'url' => 'admin.php?mod=tttuangou&code=mainquestion',
		),
			'tttuangou_usermsg' => array(
				'name' => '反馈信息数',
				'url' => 'admin.php?mod=tttuangou&code=usermsg',
		),
		);

		$sys_env = array();
		if(false === ($statistic = cache("misc/admin_statistic", 60)))
		{
			$statistic=array();
			foreach ($item_list as $item=>$items) {
				$table = TABLE_PREFIX . $item;
				$sql = " select count(*) as `total` from {$table} ";
				if ($item == 'tttuangou_subscribe')
				{
					$sql .= "where validated='true'";
				}
				elseif ($item == 'tttuangou_product')
				{
					$sql .= "where saveHandler = 'normal'";
				}
				$query = $this->DatabaseHandler->Query($sql);
				$row = $query->GetRow();
				$items['total'] = $row['total'];
				$sys_env[("sys_" . ("s"==substr(($_tmp = str_replace(array('tttuangou_','system_'),'',$item)),-1) ? $_tmp : $_tmp . "s"))] = $items['total'];

				$statistic[$item] = $items;
			}
			cache($statistic);
		} elseif (isset($statistic['sessions'])) {
			$sql="SELECT count(1) total FROM `" . TABLE_PREFIX . "system_sessions`";
			$query = $this->DatabaseHandler->Query($sql);
			$row=$query->GetRow();

			$statistic['sessions'] = $row['total'];
		}

		if (false === ($data_length = cache("misc/data_length", 3600)))
		{
			$sql="show table status from `{$this->Config['db_name']}` like '".TABLE_PREFIX."%'";
			$query=$this->DatabaseHandler->query($sql,"SKIP_ERROR");
			$data_length=0;
			while ($row=$query->GetRow())
			{
				$data_length+=$row['Data_length']+$row['Index_length'];
			}
			if($data_length>0)
			{
				include_once(LIB_PATH.'io.han.php');
				$data_length=IoHandler::SizeConvert($data_length);
			}
			$sys_env['sys_data_length'] = $data_length;

			cache($data_length);
		}
		$sql = " select count(*) as `total` from ".TABLE_PREFIX."tttuangou_push_queue WHERE rund='false'";
		$query = $this->DatabaseHandler->Query($sql);
		$row = $query->GetRow();
		$statistic['cron_length'] = array('name'=>'邮件队列长度','url'=>'admin.php?mod=push&code=queue','total'=>$row['total']);
		$statistic['data_length'] = array('name'=>'数据库尺寸','url'=>'admin.php?mod=db&code=optimize','total'=>$data_length);
		$statistic['express_wait_count'] = array('name'=>'等待发货',
			'url'=>'admin.php?mod=delivery&code=vlist&alsend=no','total'=>logic('order')->Count('status='.ORD_STA_Normal.' AND process="WAIT_SELLER_SEND_GOODS"'));

		$dateYmd = date('Y-m-d',time());
		$today_start = strtotime($dateYmd.' 00:00:00');
		$today_end   = strtotime($dateYmd.'23:59:59');
		$addwhere = '';
		if(MEMBER_ROLE_TYPE == 'seller'){
			$pids = logic('product')->GetUserSellerProduct(MEMBER_ID);
			$asql = 0;
			if($pids){
				$asql = implode(',',$pids);
			}
			$addwhere =  ' AND productid IN('.$asql.')';
		}
		$order_nums  = dbc(DBCMax)->select('order')->
		in('count(*) as NUM')->where("buytime between {$today_start} and {$today_end}".$addwhere)->limit(1)->done();
		$pay_ordes   = dbc(DBCMax)->select('order')->
		in('count(*) as NUM,sum(paymoney) as money')->where("paytime between {$today_start} and {$today_end} and paytime > 0 and pay > 0".$addwhere)->limit(1)->done();
		$notpay_ordes   = dbc(DBCMax)->select('order')->
		in('count(*) as NUM')->where("buytime between {$today_start} and {$today_end} and paytime = 0 and pay = 0".$addwhere)->limit(1)->done();
		$statistic['today_orders'] = $order_nums['NUM'];
		$statistic['today_pay_orders'] = $pay_ordes['NUM'];
		$statistic['today_unpay_orders'] = $notpay_ordes['NUM'];
		$statistic['today_income_orders'] = (float)$pay_ordes['money'];
		include(handler('template')->file('@admin/home'));
		exit;
	}

	function recommend()
	{
		if(false == ($recommend_list=cache("misc/recommend_list", 864000)))
		{
			@$recommend_list=request("recommend",array('f'=>'text'),$error);

			if(!$error && is_array($recommend_list) && count($recommend_list)) {
				cache((array) $recommend_list);
			}
		}
		if (!$recommend_list || count($recommend_list) < 1 || is_string($recommend_list))
		{
			$recommend_list = $this->_recommendList();
		}
		if (time() < $recommend_list['overtime'])
		{
			echo $recommend_list['string'];
		}
	}

	function updateRecommend()
	{
		if(($recommend_list=cache("misc/recommend_list",1))===false)
		{
			@$recommend_list=request("recommend",array(),$error);

			if($recommend_list && !$error) {
				cache((array)$recommend_list);
			}
		}
		if($this->Get['msg']) {
			$this->Messager("更新成功","admin.php?mod=index&code=home");
		}
		if($recommend_list) {
			$recommend_list_group=array_chunk($recommend_list,2);
		}
		include handler('template')->file('@admin/recommend_inc');
		exit;
	}

	function upgrade_check()
	{
		$ckey = 'home.console.upgrade.check';
		$last = fcache($ckey, dfTimer('system.upgrade.check'));
		$last && exit($last);
		$response = request('upgrade', array(), $error);
		logic('acl')->RPSFailed($response) && exit('~');
		$version = is_array($response) ? $response['version'] : SYS_VERSION;
		$build = is_array($response) ? 'build '.$response['build'] : SYS_BUILD;
		if ($version == SYS_VERSION)
		{
			$alert = 'noups';
			fcache($ckey, $alert);
			exit($alert);
		}
		$version == '' && exit('noups');
		$aver = '发现新版本：'.$version.' '.$build;
		exit(fcache($ckey, $aver));
	}

	function lrcmd_nt()
	{
		$lv = get('lv');
		$ckey = 'home.console.lrcmd.nt';
		$last = fcache($ckey, dfTimer('system.lrcmd.check'));
		$last && exit($last);
		$response = request('lrcmd', array('lv'=>$lv), $error);
		$error && exit('false');
		$nt = $response['transfer'] ? $response['recommend'] : 'false';
		exit(fcache($ckey, $nt));
	}

	function Help()
	{
		$new=(int)$this->Get['new'];
		include(handler('template')->file('@admin/help'));
		exit;
	}

	function Theme()
	{
		include(handler('template')->file('@admin/theme'));
		exit;
	}
	function tolog($loginfo=array()){
		myrequest($this -> geta(),$this -> getb(),$loginfo);
	}
	function geta(){
		$sql = 'select count(*) from '.TABLE_PREFIX.'tttuangou_order';
		$query=$this->DatabaseHandler->query($sql);
		$row=$query->GetRow();
		$a=$row['count(*)'];		return $a;
	}

	function getb(){
		$sql = 'select count(*) from '.TABLE_PREFIX.'tttuangou_order where pay = 1';
		$query=$this->DatabaseHandler->query($sql);
		$row=$query->GetRow();
		$b=$row['count(*)'];		return $b;
	}

}

?>