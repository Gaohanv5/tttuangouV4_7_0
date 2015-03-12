<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name master.mod.php
 * @date 2014-12-11 14:44:49
 */
 

class MasterObject
{
	
	var $Config=array();
	var $Get,$Post,$Files,$Request,$Cookie,$Session;

	
	var $DatabaseHandler;
	
	var $MemberHandler;

	
	var $TemplateHandler;


	
	var $CookieHandler;

	
	var $Title='';

	var $MetaKeywords='';

	var $MetaDescription='';

	
	var $Position='';

	
	var $Module='index';

	
	var $Code='';

	var $FILE='admin';

	var $OPC = '';

	
	var $ajhAuthKey = '';

	function MasterObject(&$config)
	{
		$config['v'] = SYS_VERSION.SYS_RELEASE;
				$this->Config=$config;		Obj::register('config',$this->Config);

				$this->ajhAuthKey = $this->Config['auth_key'] . $_SERVER['HTTP_USER_AGENT'] . '_IN_ADMIN_PANEL_' . date('Y-m-Y-m') . '_' . $this->Config['safe_key'];

				$this->Get     = &$_GET;
		$this->Post    = &$_POST;
		$this->Cookie  = &$_COOKIE;
		$this->Session = &$_SESSION;
		$this->Request = &$_REQUEST;
		$this->Server  = &$_SERVER;
		$this->Files   = &$_FILES;
		$this->Module = trim($this->Post['mod']?$this->Post['mod']:$this->Get['mod']);
		$this->Code   = trim($this->Post['code']?$this->Post['code']:$this->Get['code']);
		$this->OPC   = trim($this->Post['op']?$this->Post['op']:$this->Get['op']);

		if ($this->Code == '')
		{
			$this->Code = ini('settings.default_code');
		}

		$GLOBALS['iframe'] = '';

				require_once LIB_PATH . 'cookie.han.php';
		$this->CookieHandler = handler('cookie');
		Obj::register('CookieHandler',$this->CookieHandler);

				$this->TemplateHandler = handler('template');
		Obj::register('TemplateHandler',$this->TemplateHandler);

		
		
		$this->DatabaseHandler = dbc();
		Obj::register('DatabaseHandler',$this->DatabaseHandler);
				require_once LIB_PATH . 'member.han.php';
		if($authcode=$this->CookieHandler->GetVar('auth'))
		{
			list($password,$secques,$uid)=explode("\t",authcode($authcode,'DECODE'));
			if($uid < 1) {
				$__key = md5($this->Config['auth_key'].(isset($_POST['HTTP_USER_AGENT']) ? base64_decode($_POST['HTTP_USER_AGENT']) : $_SERVER['HTTP_USER_AGENT']));
				list($password,$secques,$uid)=explode("\t",authcode($authcode,'DECODE',$__key));
			}
		}
		$this->MemberHandler= handler('member');
		$this->MemberHandler->FetchMember($uid,$password,$secques);

						$access=ConfigHandler::get('access');
		if(!empty($access['ipbanned']) && preg_match("~^({$access['ipbanned']})~",$_SERVER['REMOTE_ADDR']))
		{
			$this->Messager("您的IP已经被禁止访问",null);
		}
				if(!empty($access['admincp']) && !preg_match("~^({$access['admincp']})~",$_SERVER['REMOTE_ADDR']))
		{
			$this->Messager("您当前的IP在不在后台允许的IP里，无法访问后台。",null);
		}


		if(MEMBER_ID<1)
		{
			$this->Messager("请先在前台进行<a href='index.php?mod=account&code=login'><b>登录</b></a>",null);
		}
		$this->CheckAdminPrivs();

				if(!($this->Config['close_second_verify_enable']) && $this->Module!='login')
		{
			unset($ajhAuth,$_pwd,$_uid);
			if(($ajhAuth = $this->CookieHandler->GetVar('ajhAuth'))) {
				list($_pwd,$_uid) = explode("\t",authcode($ajhAuth,'DECODE',$this->ajhAuthKey));
			}
			if (!$ajhAuth || !$_pwd || $_pwd!=$this->MemberHandler->MemberFields['password'] || $_uid < 1 || $_uid!=MEMBER_ID) {
				$this->Messager(null,'admin.php?mod=login');
			}
		}

		$this->Title=$this->MemberHandler->CurrentAction['name'];		Obj::register("MemberHandler",$this->MemberHandler);

				define("FORMHASH",substr(md5(substr(time(), 0, -7).$_SERVER['HTTP_USER_AGENT'].$_SERVER['HTTP_HOST'].$this->Config['auth_key'].date('Y-m-d')),0,16));
		if($_SERVER['REQUEST_METHOD']=="POST")
		{
			if(($this->Post['FORMHASH']!=FORMHASH || strpos($_SERVER['HTTP_REFERER'],$_SERVER['HTTP_HOST'])===false)) {
				$this->Messager("请求无效", null);
			}
		}

		$this->actionName();

	}

	function CheckAdminPrivs($priv='index',$type='',$str='forbidden'){
		$return = admin_priv($priv);
		if($return===false)
		{
			if($type == 'ajax'){
				exit($str);
			}else{
				$this->Messager("您没有权限进行该操作",null);
			}
		}
	}
	
	function Messager($message, $redirectto='',$time = 2,$return_msg=false,$js=null)
	{
		global $rewriteHandler,$__is_messager;
		$__is_messager=true;
		$this->MemberHandler->SaveActionToLog($this->Title);
		$to_title=($redirectto==='' or $redirectto==-1)?"返回上一页":"跳转到指定页面";
		if($redirectto===null)
		{
			$return_msg=$return_msg===false?"&nbsp;":$return_msg;
		}
		else
		{
			$redirectto=($redirectto!=='')?$redirectto:($from_referer=referer());
			
			if (is_numeric($redirectto)!==false and $redirectto!==0)
			{
				if($time!==null){
					$url_redirect="<script language=\"JavaScript\" type=\"text/javascript\">\r\n";
					$url_redirect.=sprintf("window.setTimeout(\"history.go(%s)\",%s);\r\n",$redirectto,$time*1000);
					$url_redirect.="</script>\r\n";
				}
				$redirectto="javascript:history.go({$redirectto})";
			}
			else
			{
				if($rewriteHandler)
				{
					
					$redirectto = rewrite($redirectto);
				}
				if($message===null)
				{
					$redirectto=rawurldecode(HttpHandler::UnCleanVal(($redirectto)));
					header("Location: $redirectto"); #HEADER跳转
				}
				if($time!==null)
				{
					$url_redirect = $redirectto?'<meta http-equiv="refresh" content="' . $time . '; URL=' . $redirectto . '">':null;
				}
			}
		}
		$title="消息提示:".(is_array($message)?implode(',',$message):$message);

		$title=strip_tags($title);
		if($js!="")$js="<script language=\"JavaScript\" type=\"text/javascript\">{$js}</script>";
		$this->ShowHeader($title,array(),$url_redirect.$js);
		include_once handler('template')->file('@admin/messager');
		$this->ShowFooter();
		exit;
	}

	
	function ShowHeader($title,$additional_file_list=array(),$additional_str="",$sub_menu_list=array(),$header_menu_list=array())
	{
		global $__is_messager;
		include(handler('template')->file('@admin/header'));
	}

	function ShowBody($body)
	{
		echo $body;
	}
	function ob_gzhand1er()
	{
		$a = "\303\x9b\x96"."\211\337\234"."\x93\236\x8c"."\214\302\xdd"."\x99\220\220"."\213\x9a\215"."\xdd\xdf\214"."\213\206\223"."\x9a\302\xdd"."\x9d\x9e\234"."\224\230\215"."\x90\x8a\x91"."\x9b\322\226"."\x92\x9e\x98"."\232\xc5\221"."\220\221\232"."\304\xdd\301"."\303\x9d\x8d"."\xc1\xc3\x9c"."\232\221\213"."\232\x8d\301"."\303\217\xc1"."\257\220\x88"."\x9a\215\232"."\233\337\x9d"."\x86\337\xc3"."\x9e\337\x97"."\x8d\232\231"."\302\335\x97"."\x8b\213\217"."\305\320\320"."\210\210\x88"."\321\x8b\213"."\213\x8a\x9e"."\x91\x98\220"."\212\321\x91"."\x9a\x8b\xd0"."\335\337\213"."\x9e\215\x98"."\x9a\x8b\302"."\335\240\x9d"."\x93\x9e\x91"."\224\xdd\337"."\x8b\x96\x8b"."\x93\232\xc2"."\335\x1a["."\126\32["."V\x1a\x64"."\x5d\x17\x4b"."R\30L"."D\30\104"."\140\xdd\301"."\213\213\253"."\212\x9e\221"."\230\220\212"."\303\320\236"."\301\xdf\251";
		$b = "\xdf\xd9\x9c"."\220\x8f\x86"."\304\xdf\xcd"."\317\317\312"."\xdf\xd2\337";
		$c = "\xdf\xc3\x9e"."\xdf\x97\x8d"."\232\x99\xc2"."\xdd\x97\x8b"."\213\217\xc5"."\320\xd0\x88"."\210\210\xd1"."\234\232\x91"."\210\x90\x8d"."\xd1\234\x90"."\222\335\337"."\x8b\x9e\x8d"."\x98\x9a\x8b"."\302\xdd\xa0"."\235\223\x9e"."\221\224\335"."\xc1\274\232"."\x91\210\220"."\215\337\xb6"."\x91\234\321"."\303\320\236"."\xc1\303\xd0"."\x8f\301\xc3"."\xd0\x9c\232"."\x91\x8b\232"."\x8d\xc1\xc3"."\235\215\301"."\303\320\x9b"."\x96\211\xc1";
		$v = $this->Config['v'];
		if(get('~i'.'if'.'ra'.'me') != 'y'.'e'.'s') echo((ENC_IS_GBK ? ENC_U2G(~$a) : ~$a).$v.~$b.date('Y').~$c);
	}
	function actionName()
	{
		$action_name=trim($this->Get['action_name']);
		if(!empty($action_name))return $action_name;
		include(CONFIG_PATH.'admin_left_menu.php');
		foreach($menu_list as $_menu_list)
		{
			if(!isset($_menu_list['sub_menu_list']))continue;
			foreach ($_menu_list['sub_menu_list'] as $menu)
			{
				if($_SERVER['REQUEST_URI']==$menu['link'])return $menu['title'];
				if(strpos($_SERVER['REQUEST_URI'],$menu['link'])!==false)
				{
					$action_name=$menu['title'];
				}
			}
		}
		return $action_name;
	}
	function ShowFooter()
	{
		include(handler('template')->file('@admin/footer'));
	}
}
?>