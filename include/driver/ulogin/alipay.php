<?php

/**
 * 登录接口：支付宝
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package ulogin
 * @name alipay.php
 * @version 1.0
 */

class alipayUnionLoginDriver extends UnionLoginDriver
{
	
	private $Gateway_ssl = 'https://mapi.alipay.com/gateway.do?';
	
	private $Gateway_com = 'http://notify.alipay.com/trade/notify_query.do?';
	
	private $config = array();
	
	private $adata = array();
	
	public function __construct()
	{
		$alipay = logic('pay')->SrcOne('alipay');
		$cfg = unserialize($alipay['config']);
		$this->config = array(
			'partner' => $cfg['partner'],
			'key' => $cfg['key'],
			'ssl' => $cfg['ssl']
		);
	}
	
	public function linker()
	{
				$post = array(
			'service' => 'alipay.auth.authorize',
			'target_service' => 'user.auth.quick.login',
			'partner' => $this->config['partner'],
			'return_url' => ini('settings.site_url').'/index.php?mod=account&code=login&op=callback&from=alipay',
			'_input_charset' => ini('settings.charset')
		);
		$post['extend_param'] = 'isv^tt11';
		return $this->__BuildForm($this->config, $post);
	}
	
	public function verify()
	{
		$uid = $this->__Return_Verify($this->config);
		if ($uid == 'VERIFY_FAILED')
		{
			return false;
		}
				account('ulogin')->token($uid, get('token'));
				$this->adata || $this->__cddata();
				get('target_url') && account()->loginReferer(get('target_url'), false);
				return $uid;
	}
	
	public function transdata()
	{
		return $this->adata;
	}
	
	private function __cddata()
	{
		if (get('real_name'))
		{
			$this->adata['username'] = get('real_name', 'txt');
		}
		if (get('email'))
		{
			$mail = get('email', 'txt');
			if (preg_match('/^\d+$/', $mail))
			{
								$this->adata['phone'] = $mail;
			}
			else
			{
				$this->adata['mail'] = $mail;
			}
		}
	}
	
	private function __BuildForm($config, $parameter)
	{
		$sign = $this->__CreateSign($config, $parameter);
		$url = $this->Gateway_ssl.'_input_charset='.$parameter['_input_charset'];
		$sHtml = '<form id="submitform" name="alipaysubmit" action="'.$url.'" method="post">';
		foreach ($parameter as $key => $val)
		{
			$sHtml.= '<input type="hidden" name="'.$key.'" value="'.$val.'"/>';
		}
		$sHtml .= '<input type="hidden" name="sign" value="'.$sign.'"/>';
		$sHtml .= '<input type="hidden" name="sign_type" value="MD5"/>';
		$sHtml .= '</form>';
		return $sHtml;
	}
	
	private function __BuildURL($config, $parameter)
	{
		$sign = $this->__CreateSign($config, $parameter);
		$parameter = $this->__arg_sort($parameter);
		$arg = $this->__create_linkstring_urlencode($parameter);
		$url = $this->Gateway_ssl.$arg.'&sign='.$sign.'&sign_type='.'MD5';
		return $url;
	}
	
	private function __Return_Verify($config)
	{
		if($config['ssl'] == 'true')
		{
			$url = $this->Gateway_ssl
				.'service=notify_verify'
				.'&partner='.$config['partner']
				.'&notify_id='.get('notify_id', 'txt');
		}
		else
		{
			$url = $this->Gateway_com
				.'partner='.$config['partner']
				.'&notify_id='.get('notify_id', 'txt');
		}

		$result = $this->__Verify($url);

		$parameter = $this->__para_filter($_GET);
		$parameter = $this->__arg_sort($parameter);
		$sign  = $this->__CreateSign($config, $parameter);

		if (preg_match('/true$/i', $result) && $sign == get('sign', 'txt'))
		{
			return get('user_id', 'number');
		}
		else
		{
			return 'VERIFY_FAILED';
		}
	}
	
	private function __Notify_Verify($config)
	{
		if($config['ssl'] == 'true')
		{
			$url = $this->Gateway_ssl
				.'service=notify_verify'
				.'&partner='.$config['partner']
				.'&notify_id='.post('notify_id', 'txt');
		}
		else
		{
			$url = $this->Gateway_com
				.'partner='.$config['partner']
				.'&notify_id='.post('notify_id', 'txt');
		}

		$result = $this->__Verify($url);

		$parameter = $this->__para_filter($_POST);
		$parameter = $this->__arg_sort($parameter);
		$sign = $this->__CreateSign($config, $parameter);

		if (preg_match('/true$/i', $result) && $sign == post('sign', 'txt'))
		{
			return post('user_id', 'number');
		}
		else
		{
			return 'VERIFY_FAILED';
		}
	}
	
	private function __Verify($url, $time_out = '6')
	{
		$urlArr     = parse_url($url);
		$errNo      = '';
		$errStr     = '';
		$transPorts = '';
		if($urlArr['scheme'] == 'https')
		{
			$transPorts = 'ssl://';
			$urlArr['port'] = '443';
		}
		else
		{
			$transPorts = 'tcp://';
			$urlArr['port'] = '80';
		}
		$fp = msockopen($transPorts . $urlArr['host'], $urlArr['port'], $errNo, $errStr, $time_out);
		if(!$fp)
		{
			zlog('error')->found('error.msockopen');
			die("ERROR: $errNo - $errStr<br />\n");
		}
		else
		{
			fputs($fp, "POST ".$urlArr["path"]." HTTP/1.1\r\n");
			fputs($fp, "Host: ".$urlArr["host"]."\r\n");
			fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
			fputs($fp, "Content-length: ".strlen($urlArr["query"])."\r\n");
			fputs($fp, "Connection: close\r\n\r\n");
			fputs($fp, $urlArr["query"] . "\r\n\r\n");
			while(!feof($fp))
			{
				$info[]=@fgets($fp, 1024);
			}
			fclose($fp);
			$info = implode(",",$info);
			return $info;
		}
	}
	
	private function __CreateSign($config, $parameter)
	{
		$parameter = $this->__para_filter($parameter);
		$parameter = $this->__arg_sort($parameter);
		$string = $this->__create_linkstring($parameter);
		$string .= $config['key'];
		$string = md5($string);
		return $string;
	}
	private function __create_linkstring($array)
	{
		$arg  = '';
		foreach ($array as $key => $val)
		{
			$arg .= $key.'='.$val.'&';
		}
		$arg = substr($arg, 0, count($arg)-2);
		return $arg;
	}
	private function __create_linkstring_urlencode($array)
	{
		$arg  = '';
		foreach ($array as $key => $val)
		{
			if ($key != 'service' && $key != '_input_charset')
			{
				$arg .= $key.'='.urlencode($val).'&';
			}
			else
			{
				$arg .= $key.'='.$val.'&';
			}
		}
		$arg = substr($arg, 0, count($arg)-2);
		return $arg;
	}
	private function __arg_sort($array)
	{
		ksort($array);
		reset($array);
		return $array;
	}
	private function __para_filter($parameter)
	{
		$ignores = array(
			'sign' => 1,
			'sign_type' => 1,
			'mod' => 1,
			'code' => 1,
			'op' => 1,
			'from' => 1
		);
		$para = array();
		foreach ($parameter as $key => $val)
		{
			if(isset($ignores[$key]) || $val == '')
			{
				continue;
			}
			else
			{
				$para[$key] = $val;
			}
		}
		return $para;
	}
}
