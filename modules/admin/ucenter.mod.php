<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name ucenter.mod.php
 * @date 2014-11-04 13:51:55
 */
 



class ModuleObject extends MasterObject
{

	
	function ModuleObject($config)
	{
		$this->MasterObject($config);

		Load::moduleCode($this);$this->Execute();
	}

	
	function Execute()
	{
		switch($this->Code)
		{
			case 'do_setting':
				$this->DoSetting();
				break;

			case 'merge':
				$this->DoMerge();
				break;

			default:
				$this->Main();
				break;
		}

	}


	

	function Main()
	{
		$this->CheckAdminPrivs('ucenter');
		if(!is_file(UC_CLIENT_ROOT . './client.php')){
			$this->Messager('Ucenter的客户端文件 <b>' . UC_CLIENT_ROOT . './client.php' . "</b> 不存在，请检查",null);
		}
		if(!is_file(ROOT_PATH . './api/uc.php')){
			$this->Messager('Ucenter的api文件 <b>' . UC_CLIENT_ROOT . './client.php' . "</b> 不存在，请检查",null);
		}
				$ucenter = ConfigHandler::get('ucenter');


		$uc_enable_radio = FormHandler::YesNoRadio('ucenter[enable]',(bool) $ucenter['enable']);

		${"uc_connect_{$ucenter['uc_connect']}_checked"} = " checked ";

		include handler('template')->file('@admin/ucenter');
	}

	function DoSetting()
	{
		$this->CheckAdminPrivs('ucenter');
		if(!is_file(UC_CLIENT_ROOT . './client.php')) {
			$this->Messager('Ucenter的客户端文件 <b>' . UC_CLIENT_ROOT . './client.php' . "</b> 不存在，请检查");
		}
		if(!is_file(ROOT_PATH . './api/uc.php')) {
			$this->Messager('Ucenter的api文件 <b>' . UC_CLIENT_ROOT . './uc.php' . "</b> 不存在，请检查");
		}

	    if (trim($_POST['uc_config_string']))
		{
			$_POST['ucenter'] = $this->_get_uc_config($_POST['uc_config_string']);
			if (!$_POST['ucenter'])
			{
				$this->Messager("通过字符串更新UC配置失败，请手工填写具体的配置信息",null);
			}
		}

		$_POST['ucenter']['uc_charset'] = $_POST['ucenter']['uc_db_charset'] = str_replace('-', '', ini('settings.charset'));

		if(!$_POST['ucenter']['uc_key']) {
			$this->Messager("请填写Ucenter通信密钥，建议查看"."帮助文"."档</b>"."</a>");
		}

		if(!$_POST['ucenter']['uc_api']) {
			$this->Messager("请填写Ucenter地址，建议查看"."帮助文"."档</b>"."</a>");
		}

		if(!$_POST['ucenter']['uc_app_id']) {
			$this->Messager("请填写当前应用ID，建议查看"."帮助文"."档</b>"."</a>");
		}

		if('请输入Ucenter的数据库密码' == $_POST['ucenter']['uc_db_password']) {
			$ucenter_config = ConfigHandler::get('ucenter');
			$_POST['ucenter']['uc_db_password'] = $ucenter_config['uc_db_password'];
		}

		if('mysql' == $_POST['ucenter']['uc_connect'])
		{
			$_POST['ucenter']['uc_db_name'] = "`".trim($_POST['ucenter']['uc_db_name'],'`')."`";
			$_POST['ucenter']['uc_db_table_prefix'] = $_POST['ucenter']['uc_db_name'] . '.' . (false !== ($_tmp_pos = strpos($_POST['ucenter']['uc_db_table_prefix'],'.')) ? substr($_POST['ucenter']['uc_db_table_prefix'],$_tmp_pos + 1) : $_POST['ucenter']['uc_db_table_prefix']);

			if((@$dl = mysql_connect($_POST['ucenter']['uc_db_host'],$_POST['ucenter']['uc_db_user'],$_POST['ucenter']['uc_db_password'])) && mysql_query("SHOW COLUMNS FROM {$_POST['ucenter']['uc_db_table_prefix']}members",$dl)) {
				;
			} else {

				$this->Messager("无法连接Ucenter数据库，请检查您填写的Ucenter数据库配置信息");
			}
		}

		$configHandler = new ConfigHandler();
		$configHandler->set('ucenter',$_POST['ucenter']);

		unset($ucenter);
		$ucenter = ConfigHandler::get('ucenter');

		if($ucenter['enable'] && 'mysql' == $ucenter['uc_connect']) {
			include_once(ROOT_PATH.'./api/uc_api_db.php');

			$uc_db = new JSG_UC_API_DB();
			@$uc_db->connect($ucenter['uc_db_host'],$ucenter['uc_db_user'],$ucenter['uc_db_password'],$ucenter['uc_db_name'],$ucenter['uc_db_charset'],1,$ucenter['uc_db_table_prefix']);

			if(!($uc_db->link) || !($uc_db->query("SHOW COLUMNS FROM {$ucenter['uc_db_table_prefix']}members",'SILENT'))) {
				$ucenter['enable'] = 0;
				$configHandler = new ConfigHandler();
				$configHandler->set('ucenter',$ucenter);

				$this->Messager("无法连接Ucenter数据库，请检查您填写的Ucenter数据库配置信息是否正确.");
			}

			$this->Messager("Ucenter配置保存成功,如果您已经对数据库进行过备份了,<a href='admin.php?mod=ucenter&code=merge&confirm=1'><b>请点此进行用户数据整合</b></a>",null);
		}

		$this->Messager("配置成功",'admin.php?mod=ucenter');
	}

	function DoMerge()
	{
		$this->CheckAdminPrivs('ucenter');
		$start = max(0,(int) $this->Get['start']);
		$limit = 500;

		$ucenter = ConfigHandler::get('ucenter');

		if(!$ucenter['enable'] || !$this->Get['confirm'] || 'mysql' != $ucenter['uc_connect'])
		{
			$this->Messager("你的配置不正确，或者已经进行过用户数据整合了",null);
		}

		include_once(ROOT_PATH.'./api/uc_api_db.php');

		$db = new JSG_UC_API_DB();
		$db->connect($this->Config['db_host'],$this->Config['db_user'],$this->Config['db_pass'],$this->Config['db_name'],$this->Config['charset'],$this->Config['db_persist'],$this->Config['db_table_prefix']);
		$query = $db->query("select * from ".TABLE_PREFIX."system_members where ucuid=0 limit {$limit}");
		if($db->num_rows($query) < 1)
		{
			$this->Messager("用户数据合并成功",null);
		}

		$uc_db = new JSG_UC_API_DB();
		$uc_db->connect($ucenter['uc_db_host'],$ucenter['uc_db_user'],$ucenter['uc_db_password'],$ucenter['uc_db_name'],$ucenter['uc_db_charset'],1,$ucenter['uc_db_table_prefix']);
		while ($data = $db->fetch_array($query))
		{
			$ucuid = -1;
			$salt = rand(100000, 999999);
			$password = md5($data['password'].$salt);
			$data['username'] = addslashes($data['username']);

			$uc_user = $uc_db->fetch_first("SELECT * FROM {$ucenter['uc_db_table_prefix']}members WHERE username='{$data[username]}'"); 			if(!$uc_user) 			{
				$uc_db->query("INSERT LOW_PRIORITY INTO {$ucenter['uc_db_table_prefix']}members SET username='$data[username]', password='$password',email='$data[email]', regip='$data[regip]', regdate='$data[regdate]', salt='$salt'", 'SILENT');
				$ucuid = $uc_db->insert_id();
				$uc_db->query("INSERT LOW_PRIORITY INTO {$ucenter['uc_db_table_prefix']}memberfields SET uid='$ucuid'",'SILENT');
			}
			else
			{
				if($uc_user['password'] == md5($data['password'].$uc_user['salt'])) 				{
					$ucuid = $uc_user['uid'];
				}
				else 				{
					$uc_db->query("REPLACE INTO {$ucenter['uc_db_table_prefix']}mergemembers SET appid='".UC_APPID."', username='$data[username]'", 'SILENT');
				}
			}

			$db->query("update ".TABLE_PREFIX."system_members set ucuid={$ucuid} where uid={$data['uid']}");
		}

		$next = ($start + $limit);
		$this->Messager("[{$start}-{$next}]正在进行用户数据的合并中，请稍候……",'admin.php?mod=ucenter&code=merge&confirm=1&start='.$next);
	}
    function _get_uc_config($str)
	{
		$str = trim(stripslashes($str));

		$ms = false;
		$uc_config = array();

		preg_match_all('~define\s*\(\s*[\'\"](UC\_\w+?)[\'\"]\s*\,\s*[\'\"]([^\'\"]+?)[\'\"]\s*\)\;~i',$str,$ms,2);
		if (is_array($ms) && count($ms))
		{
			foreach ($ms as $k=>$v)
			{
				$uc_config[strtolower($v[1])] = $v[2];
			}
			$uc_config['enable'] = $_POST['ucenter']['enable'];
			$uc_config['uc_db_host'] = $uc_config['uc_dbhost'];
			$uc_config['uc_db_user'] = $uc_config['uc_dbuser'];
			$uc_config['uc_db_password'] = $uc_config['uc_dbpw'];
			$uc_config['uc_db_name'] = $uc_config['uc_dbname'];
			$uc_config['uc_db_charset'] = $uc_config['uc_dbcharset'];
			$uc_config['uc_db_table_prefix'] = $uc_config['uc_dbtablepre'];
			$uc_config['uc_key'] = $uc_config['uc_key'];
			$uc_config['uc_api'] = $uc_config['uc_api'];
			$uc_config['uc_ip'] = $uc_config['uc_ip'];
			$uc_config['uc_app_id'] = $uc_config['uc_appid'];
						$uc_config['uc_connect'] = ($uc_config['uc_connect'] == 'mysql') ? 'mysql' : 'fsock';
		}
		return $uc_config;
	}

}

?>