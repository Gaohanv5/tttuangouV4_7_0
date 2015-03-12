<?php

/**
 * 支付方式：网银直连
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package payment
 * @name bankdirect.php
 * @version 1.0
 */

class bankdirectPaymentDriver
{
	private $__proxy_api = null;
	public function __construct()
	{
		$file = DATA_PATH.'bd.'.md5(ini('settings.site_url')).'.php';
		
		if (is_file($file))
		{
			@require_once $file;
			if (class_exists('mirror_of_bankdirect_driver', false))
			{
				$this->__proxy_api = new mirror_of_bankdirect_driver();
			}
			else
			{
				$this->__proxy_api = new inner_bankdirect_object_bucket();
			}
		}
		else
		{
			$this->__proxy_api = new inner_bankdirect_object_bucket();
		}
		$this->tool = new inner_bankdirect_tools();
		return $this;
	}
	public function inner_sys_order()
	{
		return $this->__proxy_api->inner_sys_order();
	}
	public function inner_ext_html()
	{
		return $this->__proxy_api->inner_ext_html();
	}
	public function __get($name)
	{
		return $this->__proxy_api->$name;
	}
	public function __call($name, $arguments)
	{
		return call_user_func_array(array($this->__proxy_api, $name), $arguments);
	}
}

class inner_bankdirect_object_bucket
{
	public $__is_bucket = true;
	public function inner_sys_order() { return 999; }
	public function inner_ext_html() { return ''; }
	public function CreateLink() { return '<a href="'.ihelper('bdp.lic.no').'">错误：支付接口未授权</a>'; }
}

class inner_bankdirect_tools
{
	public function service($api)
	{
		$token = md5(microtime());
		meta('epay_auth_domain_only',$token,60);
		$server = base64_decode('aHR0cDovL3BheW1lbnQudHR0dWFuZ291Lm5ldA==').$api.'?charset='.ini('settings.charset').'&';
		$url = $server."url=".ini('settings.site_url')."/index.php?mod=bankdirect&token={$token}";
		try{
			$res = dfopen($url, 10485760, '', '', true, 5, 'CENWOR.TTTG.AUTH.BDT.AGENT.'.SYS_VERSION.'.'.SYS_BUILD);
			if ($res)
			{
				if ($api == '/merchant/api-kernel-file')
				{
					if (preg_match('/\r?\n\w+\r?\n/', $res))
					{
						$res = preg_replace('/\r?\n\w+\r?\n/', '', $res);
					}
					return array('file.b64' => $res, 'file.md5' => md5($res));
				}
				$load = new Load();
				$load->lib('servicesJSON');
				$json = new servicesJSON(16);
				$res = $json->decode($res);
			}
			else
			{
				return __('网络错误');
			}
		} catch (Exception $e) {
		    return __('未知错误');
		}
		if ($res['status'] == 'ok') {
			return $res['data'];
		}else{
			$data = array(
				'client.url.found.no'	=>__('URL地址格式不正确'),
				'client.idx.found.no'	=>__('未在数据库中找到对应的URL地址记录'),
				'client.url.illegal'	=>__('URL地址和数据库中的记录不匹配'),
				'server.http.error'		=>__('服务器HTTP请求失败'),
				'client.http.error'		=>__('用户站点请求失败'),
				'client.token.error'	=>__('Token校验失败'),
				'client.license.overdue'=>__('授权已经过期 '),
				'client.license.disabled'=>__('授权已经禁用'),
				'api.file.missing' => __('接口文件未找到')
				);
			return $data[$res['errcode']];
		}
	}
	public function rd3service($api)
	{
		$url = base64_decode('aHR0cDovL3NlcnZlci50dHR1YW5nb3UubmV0LzNyZC9wYXltZW50cy9hcGkucGhw').'?r='.$api.'&charset='.ini('settings.charset');
		try{
			$res = dfopen($url, 10485760, '', '', true, 5, 'CENWOR.TTTG.AUTH.BDT.AGENT.'.SYS_VERSION.'.'.SYS_BUILD);
			return $res;
		} catch (Exception $e) {
		    return 'error';
		}
	}
	public function api_kernel_file($fileb64, $filemd5)
	{
		$file = DATA_PATH.'bd.'.md5(ini('settings.site_url')).'.php';
		if (@file_put_contents($file, $fileb64))
		{
			$fmd5 = md5_file($file);
			if ($fmd5 != $filemd5)
			{
				file_put_contents($file, '<?php ?>');
				return 'error';
			}
			else
			{
				return $fmd5;
			}
		}
		else
		{
			return 'error';
		}
	}
	public function callbackMod(){
		$token = logic('safe')->Vars('GET', 'token', 'txt');
		$token || $token = md5((string)microtime(true));
		if (meta('epay_auth_domain_only') == $token)
		{
			meta('epay_auth_domain_only',null);
			return "tsp;token:{$token};end";
		}
		return 'false';

	}
}

?>