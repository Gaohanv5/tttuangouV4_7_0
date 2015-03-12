<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name ClientResponseHandler.class.php
 * @date 2014-10-30 10:42:13
 */
 



class ClientResponseHandler  {

	
	var $key;

	
	var $parameters;

	
	var $debugInfo;

		var $content;

	function __construct() {
		$this->ClientResponseHandler();
	}

	function ClientResponseHandler() {
		$this->key = "";
		$this->parameters = array();
		$this->debugInfo = "";
		$this->content = "";
	}

	
	function getKey() {
		return $this->key;
	}

	
	function setKey($key) {
		$this->key = $key;
	}

			function setContent($content) {
		$this->content = $content;

		$xml = simplexml_load_string($this->content);
		$encode = $this->getXmlEncode($this->content);

		if($xml && $xml->children()) {
			foreach ($xml->children() as $node){
								if($node->children()) {
					$k = $node->getName();
					$nodeXml = $node->asXML();
					$v = substr($nodeXml, strlen($k)+2, strlen($nodeXml)-2*strlen($k)-5);

				} else {
					$k = $node->getName();
					$v = (string)$node;
				}

				if($encode!="" && $encode != "UTF-8") {
					$k = iconv("UTF-8", $encode, $k);
					$v = iconv("UTF-8", $encode, $v);
				}

				$this->setParameter($k, $v);
			}
		}
	}

			function setContent_backup($content) {
		$this->content = $content;
		$encode = $this->getXmlEncode($this->content);
		$xml = new SofeeXmlParser();
		$xml->parseFile($this->content);
		$tree = $xml->getTree();
		unset($xml);
		foreach ($tree['root'] as $key => $value) {
			if($encode!="" && $encode != "UTF-8") {
				$k = mb_convert_encoding($key, $encode, "UTF-8");
				$v = mb_convert_encoding($value[value], $encode, "UTF-8");
			}
			else
			{
				$k = $key;
				$v = $value[value];
			}
			$this->setParameter($k, $v);
		}
	}



		function getContent() {
		return $this->content;
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

	
	function getDebugInfo() {
		return $this->debugInfo;
	}

		function getXmlEncode($xml) {
		$ret = preg_match ("/<?xml[^>]* encoding=\"(.*)\"[^>]* ?>/i", $xml, $arr);
		if($ret) {
			return strtoupper ( $arr[1] );
		} else {
			return "";
		}
	}

	
	function _setDebugInfo($debugInfo) {
		$this->debugInfo = $debugInfo;
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

}


?>