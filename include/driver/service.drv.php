<?php

/**
 * 驱动：服务接口
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package driver
 * @name service.drv.php
 * @version 1.0
 */

class ServiceDriver
{
	
	private $po = array(
		'debug' => '发送测试，邮件并未真正发出',
		'content-empty' => '内容为空，无法发送',
		'send-success' => '发送成功，已经提交到服务器',
		'reponse-empty' => '无法连接到服务器，可能是网络故障或者防火墙屏蔽'
	);
	
    public $conf = array();
	
    public final function load($name)
    {
        $file = dirname(__FILE__).'/service/'.$name.'.php';
	    include_once $file;
	    $className = $name.'ServiceDriver';
	    return new $className();
    }
	
    public function config($cfg)
    {
        $this->conf = $cfg;
    }
	
	public function result_success($msg, $ex = array())
	{
		return array_merge(array(
			'message' => $this->po($msg),
			'status' => 'success'
		), $ex);
	}
	
	public function result_error($msg)
	{
		return array(
			'message' => $this->po($msg),
			'status' => 'failed'
		);
	}
	
	private function po($msg)
	{
		return isset($this->po[$msg]) ? $this->po[$msg] : $msg;
	}
}

?>