<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name master.mod.php
 * @date 2014-09-01 17:24:22
 */
 

class MasterObject
{
	
	var $Config=array();
	var $Get;
	var $Post;
	var $Cookie;
	var $Session;

	
	var $DatabaseHandler;
	
	var $MemberHandler;

	
	var $TemplateHandler;

	
	var $CookieHandler;


	
	var $Title='';

	
	var $Module='index';

	
	var $Code='';

	var $FILE='ajax';

	var $OPC = '';

	function MasterObject(&$config)
	{

		$config['v'] = SYS_VERSION;
		$this->Config=$config;
		

		$this->Get     =  &$_GET;

		$this->Post    =  &$_POST;

		$this->Cookie  =  &$_COOKIE;

		$this->Session =  &$_SESSION;

		$this->Request =  &$_REQUEST;

		$this->Server  = &$_SERVER;

		$this->Files   =   &$_FILES;

		$this->Module = $this->Post['mod']?$this->Post['mod']:$this->Get['mod'];
		$this->Code   = $this->Post['code']?$this->Post['code']:$this->Get['code'];
		$this->OPC   = trim($this->Post['op']?$this->Post['op']:$this->Get['op']);

		$GLOBALS['iframe'] = '';

				$ipbanned=ConfigHandler::get('access','ipbanned');
		if(!empty($ipbanned) && preg_match("~^({$ipbanned})~",$_SERVER['REMOTE_ADDR']))
		{
			die(__("您的IP已经被禁止访问。"));
		}

		$this->TemplateHandler=new TemplateHandler($config);
		Obj::register('TemplateHandler',$this->TemplateHandler);

		

		$this->CookieHandler = handler('cookie');

		
		$this->DatabaseHandler = dbc();

		Obj::register('DatabaseHandler',$this->DatabaseHandler);
		Obj::register('CookieHandler',$this->CookieHandler);
		Obj::register('config',$this->Config);

	}

	function initMemberHandler()
	{
		include_once LIB_PATH.'member.han.php';
		list($password,$secques,$uid)=explode("\t",authcode($this->CookieHandler->GetVar('auth'),'DECODE'));
		$this->MemberHandler=new MemberHandler($this);
		$member=$this->MemberHandler->FetchMember($uid,$password,$secques);
		Obj::register("MemberHandler",$this->MemberHandler);
		return $member;
	}

}
?>