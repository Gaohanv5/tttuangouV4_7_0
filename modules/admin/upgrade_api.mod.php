<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name upgrade_api.mod.php
 * @date 2014-09-01 17:24:23
 */
 




class ModuleObject extends MasterObject
{
	var $server="";
	
	private static $err_noData_help = '';
	private static $err_Format_help = '';
	private static $err_contactus_help = '';
	function ModuleObject($config)
	{
		$this->MasterObject($config);
		include_once(LIB_PATH.'io.han.php');
$err_contactus_help = self::$err_contactus_help = <<<HELP
<br/>
--------------------------<br/>
如果有疑问，您可以联系在线客服QQ：<a href="#" onclick="javascript:window.open('http://bizapp.qq.com/webc.htm?new=0&sid=800058566&o=tttuangou.net&q=7', '_blank', 'height=544, width=644,toolbar=no,scrollbars=no,menubar=no,status=no');return false;">800058566</a><br/>

<script type="text/javascript">jQuery(document).ready(function(){jQuery.get('index.php?mod=apiz&code=update&op=ServerTest'+jQuery.rnd.stamp(), function(data){jQuery('#update_server_test').html(data)})});</script>
HELP;
self::$err_noData_help = <<<HELP
抱歉，当前无法连接到升级服务器！<br/>
--------------------------<br/>
<font id="update_server_test">正在重新检查...</font>
{$err_contactus_help}
HELP;
self::$err_Format_help = <<<HELP
抱歉，请稍候进行尝试！
{$err_contactus_help}
HELP;
		Load::moduleCode($this);$this->Execute();
	}
	function Execute()
	{
		switch($this->Code)
		{
			case 'check':
				$this->check();
				break;
			case 'download':
				$this->download();
				break;
			case 'signup':
				 $this->Signup();
				 break;
			default:
				$this->envCheck();
				break;
		}
	}
	function envCheck()
	{
		$this->CheckAdminPrivs('upgrade');

				$dir_list=array("api","app","backup","cache","data","uploads","static","errorlog","include","modules","setting","templates","./",);
		foreach ($dir_list as $dir)
		{
			$path=ROOT_PATH.$dir;
			if(is_writable($path)==false)$this->Messager("{$path}目录不可写，请将其属性改成0777", null);
		}
				if(!function_exists("gzopen"))$this->Messager("您的服务器不支持gzopen函数，不能执行升级。", null);
		if(!function_exists("md5_file"))$this->Messager("您的服务器不支持md5_file函数，不能执行升级。", null);
				$_free_space_src = diskfreespace('.');
		if (is_null($_free_space_src) || $_free_space_src <= 0)
		{
			$this->Messager('无法检查磁盘剩余空间！升级前请先确认剩余空间充足（大于10M）<br/><b>否则极有可能导致升级失败！</b><br/><br/>（继续升级请 <a href="admin.php?mod=upgrade_api&code=check">点击此处</a>）', null);
		}
		else
		{
			$_free_space = intval($_free_space_src / (1024 * 1024));
			if ($_free_space < 10)
			{
				$this->Messager('磁盘剩余空间太小（不足10M），无法升级！', null);
			}
		}
				$this->Messager("正在检测...", "admin.php?mod=upgrade_api&code=check");
	}
	function Signup()
	{
		$this->CheckAdminPrivs('upgrade');
		$this->OPC == 'request' && $this->Signup_request();
		$this->checkResponse('acl.denied');
	}
	function Signup_request()
	{
		$account = post('account');
		$password = post('password');
		$result = logic('acl')->Signup($account, $password);
		if ($result != 'ok')
		{
			$this->checkErrorNoDATA($result);
			$this->Messager($result, -1);
		}
		$aclData = logic('acl')->Account();
		$uStop = $aclData['upgrade']['stop'];
		if ($uStop)
		{
			$this->Messager($uStop, null);
		}
				clearcache();
				header('Location: admin.php?mod=upgrade');
	}
	private function checkErrorNoDATA($response)
	{
		if ($response == 'error_nodata')
		{
			$this->Messager(self::$err_noData_help, null);
		}
		if ($response == 'error_format')
		{
			$this->Messager(self::$err_Format_help, null);
		}
	}
	private function checkResponse($response)
	{
		if (!is_string($response)) return $response;
		if (logic('acl')->RPSFailed($response))
		{
			include handler('template')->file('@admin/upgrade_acl_signup');
			exit;
		}
		$this->checkErrorNoDATA($response);
		return $response;
	}

	
	function check()
	{
		$this->CheckAdminPrivs('upgrade');

		$rets = array();
		$ck = 'upgrade_api.check.status';
		if(false == ($rets = fcache($ck, 600)))
		{
			$rets = json_decode($this->request('check'), true);
			fcache($ck, $rets);
		}
		if(is_array($rets['data'])) {
			$url = 'admin.php?mod=upgrade_api&code=download';
			foreach($rets['data'] as $k=>$v) {
				$url .= '&' . $k . '=' . urlencode($v);
			}
			if('ajax' != $this->OPC) {
				$this->Messager('请稍候，正在升级中……', $url, 0);
			} else {
				$av = $rets['data']['version'];
				exit("<a href='admin.php?mod=upgrade_api' title='点此进行API核心文件的升级'><font color='red'>[API版本]{$av}</font></a><img src='{$url}' width='0' />");
			}
		} else {
			$avs = $av = ini('settings.api_version');
			if($av) {
				$avs = '[API版本]' . $av;
			}
			if('ajax' == $this->OPC) {
				exit($avs);
			} else {
				$this->Messager('API核心文件暂时不需要升级 ' . $avs . self::$err_contactus_help, null);
			}
		}
	}
	
	function download()
	{
		$this->CheckAdminPrivs('upgrade');

		$upgrade_data_dir = DATA_PATH.'upgrade/';
		is_dir($upgrade_data_dir) || @tmkdir($upgrade_data_dir);

		$tmp_file = $upgrade_data_dir."tttg.api.zip";
		$tmp_exists = is_file($tmp_file);
		if($tmp_exists)
		{
			$tmp_md5 = md5_file($tmp_file);
		}

		$param = $_GET;
		unset($param['mod'], $param['code']);
		$url = 'admin.php?mod=upgrade_api&code=download';
		foreach($param as $k=>$v) {
			$url .= '&' . $k . '=' . urlencode($v);
		}

		if(false == $tmp_exists || $tmp_md5 != get('file_md5'))
		{
			$data = $this->request('download', $param);
			if(!$data)
			{
				$this->Messager("请求失败，请稍候在试。",null);
			}

						$fp = fopen($tmp_file,"wb");
			if($fp==false)
			{
				$this->Messager($tmp_file."文件无法写入",null);
			}
			$write_length=fwrite($fp, $data);
			fclose($fp);
		}

		if (false == file_exists($tmp_file))
		{
			$this->Messager("升级包已经不存在，请重新下载", null);
		}

		$upgrade_tmp_dir = CACHE_PATH . '/tttg_api_tmp/';
		is_dir($upgrade_tmp_dir) || @tmkdir($upgrade_tmp_dir);
		$files = logic('upgrade')->zip2web($tmp_file, $upgrade_tmp_dir);
		
		$error_found = logic('upgrade')->web2upgrade($upgrade_tmp_dir, ROOT_PATH);
		if ($error_found != 'ok')
		{
			$msg = '<div style="width:700px;text-align:left;">备份或者升级网站文件时出错，程序无法继续执行！<hr/>';
			$msg .= $error_found;
			$msg .= '<hr/>请您检查相应文件权限后，<a href="'.$url.'">点击此处</a> 重新升级';
			$msg .= '</div>';
			$this->Messager($msg, null);
		}

				ini('settings.api_version', $param['version']);
				@unlink($tmp_file);
				logic('upgrade')->upgrade2finish();
				$msg = "升级已经完成！ ";

		$this->Messager($msg, 'admin.php?mod=api', 0);

	}

	function request($action, $param = array()) {
		settype($param, "array");

		$aclData = logic('acl')->Account();
		$param['acl_account'] = $aclData['account'];
		$param['acl_token'] = $aclData['token'];
		$param['url'] = ini('settings.site_url');
		$param['version'] = (is_file(INCLUDE_PATH.'api/func/loader.php') ? ini('settings.api_version') : 0);
		$param['charset'] = strtolower(str_replace('-', '', ini('settings.charset')));
		$param['sys_info'] = SYS_VERSION . '~' . SYS_BUILD . '~' . SYS_RELEASE . '~' . SYS_PATH;  
		$server_url = base64_decode('aHR0cDovL3NlcnZlci50dHR1YW5nb3UubmV0L2FwaS91cGdyYWRlLnBocD9mbGFnPXR0dGdfYXBpJmFjdGlvbj0=') . $action;
		foreach($param as $k=>$v) {
			$server_url .= '&' . $k . '=' . urlencode($v);
		}

		return @dfopen($server_url);
	}

}

?>