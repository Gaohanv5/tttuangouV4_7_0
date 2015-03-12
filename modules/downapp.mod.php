<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name downapp.mod.php
 * @date 2014-12-11 14:44:49
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
		debug('aa');
		$this->Title = '下载手机版享更多优惠';
		$android_url = ini('settings.site_url').'/get-last-apk.php';
		$iphone = ini('iphone');
		$iphone_url = $iphone['url'];

		$referer_url = referer('?');
		if(false !== strpos($referer_url, '?')) {
			$referer_url .= '&ignore_jump=1';
		} else {
			$referer_url .= '/ignore_jump-1';
		}

		$user_agent = $_SERVER['HTTP_USER_AGENT'];
		if (stripos($user_agent, 'MicroMessenger') === false) {
						if( false != preg_match("/(iphone|android|mobile)/i",$_SERVER['HTTP_USER_AGENT']) ) {
				include handler('template')->file('downapp_mobile');
			} else {
				include handler('template')->file('downapp');	
			}
		} else {
				include handler('template')->file('downapp_weixin');
		}
	}
	
	function down()
	{
		$android_url = ini('settings.site_url').'/get-last-apk.php';
		$iphone = ini('iphone');
		$iphone_url = $iphone['url'];
		if($_SERVER['HTTP_USER_AGENT'] && preg_match("/(iphone|android)/i",$_SERVER['HTTP_USER_AGENT'],$match))
		{
			if(strtolower($match[0]) == 'iphone')
			{
				header('Location: '.$iphone_url);exit;
			}
			else
			{
				if(false === stripos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger')) {
					header('Location: '.$android_url);exit;
				}
			}
		}		
		$this->main();
	}
}
?>