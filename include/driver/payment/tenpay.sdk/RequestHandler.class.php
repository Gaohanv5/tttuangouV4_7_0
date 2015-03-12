<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name RequestHandler.class.php
 * @date 2014-10-30 10:42:13
 */
 


class RequestHandler {

	
	var $gateUrl;

	
	var $key;

	
	var $parameters;

	
	var $debugInfo;

	function __construct() {
		$this->RequestHandler();
	}

	function RequestHandler() {
		$this->gateUrl = "https://www.tenpay.com/cgi-bin/v1.0/service_gate.cgi";
		$this->key = "";
		$this->parameters = array();
		$this->debugInfo = "";
	}

	
	function init() {
			}

	
	function getGateURL() {
		return $this->gateUrl;
	}

	
	function setGateURL($gateUrl) {
		$this->gateUrl = $gateUrl;
	}

	
	function getKey() {
		return $this->key;
	}

	
	function setKey($key) {
		$this->key = $key;
	}

	
	function getParameter($parameter) {
		return $this->parameters[$parameter];
	}

	
	function setParameter($parameter, $parameterValue) {
		$this->parameters[$parameter] = $parameterValue;
	}

	
	function getAllParameters() {
		return $this->parameters;
	}

	
	function getRequestURL() {

		$this->createSign();

		$reqPar = "";
		ksort($this->parameters);
		foreach($this->parameters as $k => $v) {
			$reqPar .= $k . "=" . urlencode($v) . "&";
		}

				$reqPar = substr($reqPar, 0, strlen($reqPar)-1);

		$requestURL = $this->getGateURL() . "?" . $reqPar;

		return $requestURL;

	}

	
	function getDebugInfo() {
		return $this->debugInfo;
	}

	
	function doSend() {
		header("Location:" . $this->getRequestURL());
		exit;
	}

	
	function createSign() {
		$signPars = "";
		ksort($this->parameters);
		foreach($this->parameters as $k => $v) {
			if("" != $v && "sign" != $k) {
				$signPars .= $k . "=" . $v . "&";
			}
		}
		$signPars .= "key=" . $this->getKey();
		$sign = strtolower(md5($signPars));
		$this->setParameter("sign", $sign);

				$this->_setDebugInfo($signPars . " => sign:" . $sign);

	}

	
	function _setDebugInfo($debugInfo) {
		$this->debugInfo = $debugInfo;
	}

}

?>