<?php

/**
 * 模块：数据调用
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package module
 * @name apiz.mod.php
 * @version 1.0
 */

class ModuleObject extends MasterObject
{
	function ModuleObject( & $config )
	{
		$this->MasterObject($config);
		Load::moduleCode($this);$this->Execute();
	}
	function Execute()
	{
		if ( 'js' == $this->Code )
		{
			$this->JsResponse();
		}
		if ('comliv' == $this->Code)
		{
			$this->Comlic_Validater();
		}
		if ('sms' == $this->Code && 'ServerTest' == $this->OPC)
		{
			$this->SMS_ServerTest();
		}
		if ('mail' == $this->Code && 'SmtpTest' == $this->OPC)
		{
			$this->MAIL_SmtpTest();
		}
		if ('update' == $this->Code && 'ServerTest' == $this->OPC)
		{
			$this->Update_ServerTest();
		}
		if ('c2phone' == $this->Code)
		{
			$this->C2Phone();
		}
		if ('upgrade' == $this->Code)
		{
			$this->upsCtrlAPI();
		}
	}
	function JsResponse()
	{
		$product = logic('product')->GetFirst();
		include ($this->TemplateHandler->Template('tttuangou_data_show_js'));
	}
	function Comlic_Validater()
	{
		logic('acl')->Comliv();
	}
	function C2Phone()
	{
		ini('coupon.c2phone.enabled') || $this->Messager('系统暂未开放此功能！');
		$op = get('op');
		if (!$op)
		{
			$phone_view = logic('phone')->view();
			$cid = get('cid', 'int');
			include handler('template')->file('apiz_c2phone_confirm');
			exit;
		}
		if ($op = 'done')
		{
			$cid = post('cid', 'int');
			$phone = post('phone');
			if (strlen($phone) != 11)
			{
				$this->Messager('请输入正确的手机号码！', -1);
			}
			if(false !== strpos($phone, '****')) {
				$phone = user()->get('phone');
			} elseif(false != ($ret = logic('phone')->Check($phone, false))) {
				$this->Messager($ret, -1);
			}
						$c = logic('coupon')->SrcOne($cid);
			$c || $this->Messager('无效的' . TUANGOU_STR . '券编号！');
			$c['uid'] == user()->get('id') || $this->Messager('您无权打印此' . TUANGOU_STR . '券！');
			$data = array
			(
				'uid' => $c['uid'],
				'productid' => $c['productid'],
				'orderid' => $c['orderid'],
				'number' => $c['number'],
				'password' => $c['password'],
				'mutis' => $c['mutis'],
				'status' => $c['status']
			);
			$data['product'] = logic('product')->GetOne($data['productid']);
			$data['product']['perioddate'] = date('Y-m-d H:i:s', $data['product']['perioddate']);
			$class = 'logic_coupon_Create';
			$NTDrv = driver('notify');
			$msg = ini('notify.event.'.$class.'.msg.sms');
			$NTDrv->FlagParser($class.'.sms', $data, $msg);
						logic('push')->addi('sms', $phone, array('content'=>$msg));
			$this->Messager('短信已经发送，稍后您便可以收到', '?mod=me&code=coupon');
		}
	}
	function SMS_ServerTest()
	{
		$smslist = array(
			'ls' => 'http://smsls.tttuangou.net:8080/',
			'qxt' => 'http://gd106.tttuangou.net:9000/QxtSms/',
			'wnd' => 'http://sms.weinaduo.net/',
			'zt' => 'http://www.ztsms.cn/login',
			'ums' => 'http://ums.zj165.com/',
			'tyx' => 'http://www.topencrm.com/',
					);
		$flag = get('flag', 'txt');
		if (!isset($smslist[$flag]))
		{
			exit('不支持的短信通道！');
		}
		$url = $smslist[$flag];
		$baidu = 'http://www.baidu.com/';
		$help = ' <a href="http://cenwor.com/thread-6944-1-1.html" target="_blank">查看帮助</a>';

		if (false == msockopen())
		{
			exit('socket相关函数被限制！'.$help);
		}
		if (false != ($file = $this->SMS_ServerTest_connector($url)))
		{
			exit('测试正常，可以访问短信服务器！<a href="javascript:;" onclick="history.go(-1);">点此返回</a>');
		}
		elseif (false != ($file = $this->SMS_ServerTest_connector($baidu)))
		{
			exit('无法连接到短信服务器（'.$url.'），请检查！'.$help);
		}
		else
		{
			exit('无法访问外部服务器！'.$help);
		}
	}
	private function SMS_ServerTest_connector($url)
	{
		if (!$url) return false;
		$html = dfopen($url, 10485760, '', '', true, 10, 'CENWOR.TTTG.SMS.AGENT.'.SYS_VERSION.'.'.SYS_BUILD);
		if ($html)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	function MAIL_SmtpTest()
	{
		$baidu = 'http://www.baidu.com/';
		$help = ' <a href="http://cenwor.com/thread-9525-1-1.html" target="_blank">查看帮助</a>';

		if (false == msockopen())
		{
			exit('socket相关函数被限制！'.$help);
		}
		$oneOK = false;
		$portD = '';
		$portList = array(25 => '普通模式', 465 => 'SSL加密方式');
		foreach ($portList as $port => $desc)
		{
			if (false != $file = $this->MAIL_connector($port))
			{
				$portD .= '可以访问邮件服务器 '.$port.' 端口（'.$desc.'）<br/>';
				$oneOK = true;
			}
			else
			{
				$portD .= '<font color="red">无法</font> 访问邮件服务器 '.$port.' 端口（'.$desc.'）<br/>';
			}
		}
		if ($oneOK)
		{
			exit('测试正常！<br/>'.$portD.'<a href="javascript:;" onclick="window.close();">点此关闭</a>');
		}
		elseif (false != $file = $this->SMS_ServerTest_connector($baidu))
		{
			exit('无法连接到邮件服务器！'.$help);
		}
		else
		{
			exit('无法访问外部服务器！'.$help);
		}
	}
	private function MAIL_connector($port)
	{
		$fp = msockopen('smtp.qq.com', $port, $en, $es, 3);
		$r = $fp ? true : false;
		$fp && fclose($fp);
		return $r;
	}
	function Update_ServerTest()
	{
		$url = 'http://update.cenwor.com/';
		$baidu = 'http://www.baidu.com/';
		$help = ' <a href="http://cenwor.com/thread-9995-1-1.html" target="_blank">查看帮助</a>';

		if (false == msockopen())
		{
			exit('socket相关函数被限制！'.$help);
		}
		if (false != $file = $this->SMS_ServerTest_connector($url))
		{
			exit('测试正常，可以访问升级服务器！');
		}
		elseif (false != $file = $this->SMS_ServerTest_connector($baidu))
		{
			exit('无法连接到升级服务器！'.$help);
		}
		else
		{
			exit('无法访问外部服务器！'.$help);
		}
	}
	function upsCtrlAPI()
	{
				logic('upgrade')->APISelector();
	}
}
?>