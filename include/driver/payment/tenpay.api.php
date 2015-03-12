<?php

/**
 * 财付通支付接口
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package payment
 * @name tenpay.api.php
 * @version 1.0
 */

class exTenpayAPIDriver
{
	private $config = array();
	private $hds = array();
	
    public function config($config)
    {
    	$this->config = $config;
		return $this;
    }
    
    public function hd($type)
    {
    	if (!isset($this->hds[$type]))
    	{
    		$maps = array(
				'request' => array(
					'class' => 'RequestHandler',
					'file' => 'RequestHandler.class.php'
				),
				'request.notify' => array(
					'class' => 'RequestHandler',
					'file' => 'RequestHandler.class.php'
				),
				'response' => array(
					'class' => 'ResponseHandler',
					'file' => 'ResponseHandler.class.php'
				),
				'http' => array(
					'class' => 'TenpayHttpClient',
					'file' => 'TenpayHttpClient.class.php'
				),
				'client' => array(
					'class' => 'ClientResponseHandler',
					'file' => 'ClientResponseHandler.class.php'
				)
			);
			if (isset($maps[$type]))
			{
				include DRIVER_PATH.'payment/tenpay.sdk/'.$maps[$type]['file'];
				$this->hds[$type] = new $maps[$type]['class']();
				$api = &$this->hds[$type];
				if ($type == 'request')
				{
					$api->init();
					$api->setKey($this->config['key']);
					$api->setGateUrl("https://gw.tenpay.com/gateway/pay.htm");
				}
				if ($type == 'request.notify')
				{
					$api->init();
					$api->setKey($this->config['key']);
					$api->setGateUrl("https://gw.tenpay.com/gateway/simpleverifynotifyid.xml");
					$api->setParameter('partner', $this->config['bargainor']);
					$api->setParameter('notify_id', $this->hd('response')->getParameter("notify_id"));
				}
				if ($type == 'response')
				{
					$api->setKey($this->config['key']);
				}
				if ($type == 'http')
				{
					$api->setTimeOut(5);
				}
				if ($type == 'client')
				{
					$api->setKey($this->config['key']);
				}
			}
			else
			{
				$this->hds[$type] = false;
			}
    	}
    	return $this->hds[$type];
    }
}

?>