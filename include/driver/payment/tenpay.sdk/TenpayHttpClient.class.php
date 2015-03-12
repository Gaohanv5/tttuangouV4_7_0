<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name TenpayHttpClient.class.php
 * @date 2014-10-30 10:42:13
 */
 




class TenpayHttpClient {
		var $reqContent;
		var $resContent;
		var $method;

		var $certFile;
		var $certPasswd;
		var	$certType;

		var $caFile;

		var $errInfo;

		var $timeOut;

		var $responseCode;

	function __construct() {
		$this->TenpayHttpClient();
	}


	function TenpayHttpClient() {
		$this->reqContent = "";
		$this->resContent = "";
		$this->method = "post";

		$this->certFile = "";
		$this->certPasswd = "";
		$this->certType = "PEM";

		$this->caFile = "";

		$this->errInfo = "";

		$this->timeOut = 120;

		$this->responseCode = 0;

	}


		function setReqContent($reqContent) {
		$this->reqContent = $reqContent;
	}

		function getResContent() {
		return $this->resContent;
	}

		function setMethod($method) {
		$this->method = $method;
	}

		function getErrInfo() {
		return $this->errInfo;
	}

		function setCertInfo($certFile, $certPasswd, $certType="PEM") {
		$this->certFile = $certFile;
		$this->certPasswd = $certPasswd;
		$this->certType = $certType;
	}

		function setCaInfo($caFile) {
		$this->caFile = $caFile;
	}

		function setTimeOut($timeOut) {
		$this->timeOut = $timeOut;
	}

		function call() {
				$ch = curl_init();

				curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeOut);

				curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);

				curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1);


		$arr = explode("?", $this->reqContent);
		if(count($arr) >= 2 && $this->method == "post") {
						curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_URL, $arr[0]);
						curl_setopt($ch, CURLOPT_POSTFIELDS, $arr[1]);

		}else{
			curl_setopt($ch, CURLOPT_URL, $this->reqContent);
		}

				if($this->certFile != "") {
			curl_setopt($ch, CURLOPT_SSLCERT, $this->certFile);
			curl_setopt($ch, CURLOPT_SSLCERTPASSWD, $this->certPasswd);
			curl_setopt($ch, CURLOPT_SSLCERTTYPE, $this->certType);
		}

				if($this->caFile != "") {
						curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
			curl_setopt($ch, CURLOPT_CAINFO, $this->caFile);
		} else {
						curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		}

				$res = curl_exec($ch);
		$this->responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		if ($res == NULL) {
		   $this->errInfo = "call http err :" . curl_errno($ch) . " - " . curl_error($ch) ;
		   curl_close($ch);
		   return false;
		} else if($this->responseCode  != "200") {
			$this->errInfo = "call http err httpcode=" . $this->responseCode  ;
			curl_close($ch);
			return false;
		}

		curl_close($ch);
		$this->resContent = $res;


		return true;
	}

	function getResponseCode() {
		return $this->responseCode;
	}

}
?>