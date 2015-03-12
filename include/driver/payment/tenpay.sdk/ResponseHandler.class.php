<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name ResponseHandler.class.php
 * @date 2014-10-30 10:42:13
 */
 



class ResponseHandler  {

	
	var $key;

	
	var $parameters;

	
	var $debugInfo;

	function __construct() {
		$this->ResponseHandler();
	}

	function ResponseHandler() {
		$this->key = "";
		$this->parameters = array();
		$this->debugInfo = "";

		
		foreach($_GET as $k => $v) {
			$this->setParameter($k, $v);
		}
		
		foreach($_POST as $k => $v) {
			$this->setParameter($k, $v);
		}
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

	
	function isTenpaySign() {
		$signPars = "";
		ksort($this->parameters);
		foreach($this->parameters as $k => $v) {
			if("sign" != $k && "mod" != $k && "pid" != $k && "" != $v) {
				$signPars .= $k . "=" . $v . "&";
			}
		}
		$signPars .= "key=" . $this->getKey();

		$sign = strtolower(md5($signPars));

		$tenpaySign = strtolower($this->getParameter("sign"));

				$this->_setDebugInfo($signPars . " => sign:" . $sign .
				" tenpaySign:" . $this->getParameter("sign"));

		return $sign == $tenpaySign;

	}

	
	function getDebugInfo() {
		return $this->debugInfo;
	}

	
	function doShow($show_url) {
		$strHtml = "<html><head>\r\n" .
			"<meta name=\"TENCENT_ONLINE_PAYMENT\" content=\"China TENCENT\">" .
			"<script language=\"javascript\">\r\n" .
				"window.location.href='" . $show_url . "';\r\n" .
			"</script>\r\n" .
			"</head><body></body></html>";

		echo $strHtml;

		exit;
	}

	
	function _isTenpaySign($signParameterArray) {

		$signPars = "";
		foreach($signParameterArray as $k) {
			$v = $this->getParameter($k);
			if("sign" != $k && "" != $v) {
				$signPars .= $k . "=" . $v . "&";
			}
		}
		$signPars .= "key=" . $this->getKey();

		$sign = strtolower(md5($signPars));

		$tenpaySign = strtolower($this->getParameter("sign"));

				$this->_setDebugInfo($signPars . " => sign:" . $sign .
				" tenpaySign:" . $this->getParameter("sign"));

		return $sign == $tenpaySign;


	}

	
	function _setDebugInfo($debugInfo) {
		$this->debugInfo = $debugInfo;
	}

}


?>