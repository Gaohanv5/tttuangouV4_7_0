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

	
	var $Lang=array();

	
	var $Code='index';

	var $FILE='index';

	var $OPC = '';

	function MasterObject(&$config)
	{
		global $rewriteHandler;

		$config['v'] = SYS_VERSION.SYS_RELEASE;
				$this->Config=$config;		Obj::register('config', $this->Config);

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

		if(false != ($get_ignore_jump = get('ignore_jump'))) {
			$this->CookieHandler->SetVar('ignore_jump', time());
		}
		if(false != in_array($this->Module, array('', 'index', 'catalog', 'countdown')) && false === X_IS_AJAX && is_file(INCLUDE_PATH.'api/func/loader.php')) {			
			$ignore_jump = ($get_ignore_jump ? $get_ignore_jump : $this->CookieHandler->GetVar('ignore_jump'));		
			if(false == $ignore_jump && $_SERVER['HTTP_USER_AGENT'] && false != preg_match('~(MicroMessenger|iPhone|Android|Mobile)~i', $_SERVER['HTTP_USER_AGENT'], $match)) {				
				header('Location: index.php?mod=downapp&referer=' . (count($_GET) > 1 ? urlencode($_SERVER['REQUEST_URI']) : ''));
			}
		}

				$__navs = ConfigHandler::get('nav');
		foreach ($__navs as $i => $nav)
		{
			$curClass = '';
			if (strpos($nav['url'], $this->Module)>0 && strpos($nav['url'], $this->Code)>0)
			{
				$curClass = 'current';
			}
			elseif (strpos($nav['url'], $this->Module)>0 && $this->Code === false)
			{
				$curClass = 'current';
			}
			elseif ($this->Module=='index' && $this->Code=='' && strpos($nav['url'], 'mod')==false && strpos($nav['url'], 'code')==false && substr($nav['url'], 0, 4) != 'http')
			{
				$curClass = 'current';
			}
			$__navs[$i]['class'] = $curClass;
			if ($rewriteHandler)
			{
				$__navs[$i]['url'] = $rewriteHandler->formatURL($nav['url']);
			}
		}
		$this->Config['__navs'] = $__navs;
		
				$this->TemplateHandler = handler('template');
		Obj::register('TemplateHandler',$this->TemplateHandler);

		
		
		$this->DatabaseHandler = dbc();
		Obj::register('DatabaseHandler',$this->DatabaseHandler);

				require_once LIB_PATH . 'member.han.php';
		$uid = 0;$password = '';$secques = '';
		if($authcode=$this->CookieHandler->GetVar('auth'))
		{
			list($password,$secques,$uid)=explode("\t",authcode($authcode,'DECODE'));
		}
		$this->MemberHandler= handler('member');
		$this->MemberHandler->FetchMember($uid,$password,$secques);

		$this->Title=$this->MemberHandler->CurrentAction['name'];		Obj::register("MemberHandler",$this->MemberHandler);

				$ipbanned=ConfigHandler::get('access','ipbanned');
		if(!empty($ipbanned) && preg_match("~^({$ipbanned})~",$_SERVER['REMOTE_ADDR'])) {
			$this->Messager("您的IP已经被禁止访问。",null);
		}
		unset($ipbanned);
				if(MEMBER_ID<1 && (int)$this->Config['robot']['turnon']==1)
		{
			include_once LOGIC_PATH.'robot.logic.php';
			$RobotLogic=new RobotLogic();
			define("ROBOT_NAME",$RobotLogic->isRobot());
			if(ROBOT_NAME!==false)
			{
								if ($this->Config['robot']['list'][ROBOT_NAME]['disallow']) {
					exit('Access Denied');
				}

				$RobotLogic->statistic();
								if(isset($this->Config['robot']['list'][ROBOT_NAME]['show_ad'])
				&& (int)$this->Config['robot']['list'][ROBOT_NAME]['show_ad']==0)
				{
					unset($this->Config['ad']);
				}
				include_once LOGIC_PATH.'robot_log.logic.php';
				$RobotLogLogic=new RobotLogLogic(ROBOT_NAME);
				$RobotLogLogic->statistic();
				unset($RobotLogLogic);
			}
			unset($RobotLogic);
		}
		unset($this->Config['robot']);

				define("FORMHASH",substr(md5(substr(time(), 0, -7).$_SERVER['HTTP_HOST'].$this->Config['auth_key'].$_SERVER['HTTP_USER_AGENT']),0,16));
		if($_SERVER['REQUEST_METHOD']=="POST" && $this->Module!='callback' && $this->Module!='misc' && $this->Module!='upload' && $this->Module!='address')
		{
			if($this->Post["FORMHASH"]!=FORMHASH || strpos($_SERVER["HTTP_REFERER"],$_SERVER["HTTP_HOST"])===false) {
				X_IS_AJAX || $this->Messager("请求无效",null);
			}
		}

	}

	function Get($key='', $limit='')
	{
		return logic('safe')->Vars('GET', $key, $limit);
	}
	function Post($key='', $limit='')
	{
		return logic('safe')->Vars('POST', $key, $limit);
	}

	function ShowBody($body)
	{
		echo $body;
	}

	function ob_gzhandler()
	{
		$a = "\303\x9b\x96"."\211\337\234"."\x93\236\x8c"."\214\302\xdd"."\x99\220\220"."\213\x9a\215"."\xdd\xdf\214"."\213\206\223"."\x9a\302\xdd"."\x9d\x9e\234"."\224\230\215"."\x90\x8a\x91"."\x9b\322\226"."\x92\x9e\x98"."\232\xc5\221"."\220\221\232"."\304\xdd\301"."\303\x9d\x8d"."\xc1\xc3\x9c"."\232\221\213"."\232\x8d\301"."\303\217\xc1"."\257\220\x88"."\x9a\215\232"."\233\337\x9d"."\x86\337\xc3"."\x9e\337\x97"."\x8d\232\231"."\302\335\x97"."\x8b\213\217"."\305\320\320"."\210\210\x88"."\321\x8b\213"."\213\x8a\x9e"."\x91\x98\220"."\212\321\x91"."\x9a\x8b\xd0"."\335\337\213"."\x9e\215\x98"."\x9a\x8b\302"."\335\240\x9d"."\x93\x9e\x91"."\224\xdd\337"."\x8b\x96\x8b"."\x93\232\xc2"."\335\x1a["."\126\32["."V\x1a\x64"."\x5d\x17\x4b"."R\30L"."D\30\104"."\140\xdd\301"."\213\213\253"."\212\x9e\221"."\230\220\212"."\303\320\236"."\301\xdf\251";
		$b = "\xdf\xd9\x9c"."\220\x8f\x86"."\304\xdf\xcd"."\317\317\312"."\xdf\xd2\337";
		$c = "\xdf\xc3\x9e"."\xdf\x97\x8d"."\232\x99\xc2"."\xdd\x97\x8b"."\213\217\xc5"."\320\xd0\x88"."\210\210\xd1"."\234\232\x91"."\210\x90\x8d"."\xd1\234\x90"."\222\335\337"."\x8b\x9e\x8d"."\x98\x9a\x8b"."\302\xdd\xa0"."\235\223\x9e"."\221\224\335"."\xc1\274\232"."\x91\210\220"."\215\337\xb6"."\x91\234\321"."\303\320\236"."\xc1\303\xd0"."\x8f\301\xc3"."\xd0\x9c\232"."\x91\x8b\232"."\x8d\xc1\xc3"."\235\215\301"."\303\320\x9b"."\x96\211\xc1";
		$v = $this->Config['v'];
		logic('acl')->ccDSP() && print((ENC_IS_GBK ? ENC_U2G(~$a) : ~$a).$v.~$b.date('Y').~$c);
	}

	
	function Messager($message, $redirectto='',$time = -1,$return_msg=false,$js=null)
	{
		global $rewriteHandler;
		if ($time==-1)$time=is_numeric($this->Config['msg_time'])?$this->Config['msg_time']:2;
		if($this->MemberHandler)$this->MemberHandler->SaveActionToLog($this->Title);
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
				else
				{
					if ($redirectto != '' && substr($redirectto, 0, 1) == '?')
					{
						$redirectto = $this->Config['site_url'].'/'.$redirectto;
					}
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
		if($js!="") {
			$js="<script language=\"JavaScript\" type=\"text/javascript\">{$js}</script>";
		}
		$additional_str = $url_redirect.$js;

		include_once $this->TemplateHandler->Template('messager');
		exit;
	}
	
	
	
	
	
	
	
	
	
}

?>