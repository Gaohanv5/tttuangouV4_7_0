<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name unionnotify.php
 * @date 2014-12-11 14:44:49
 */
 

include 'setting/settings.php';
$url = $config["settings"]['site_url'].'/index.php?mod=callback&pid=unionpaymobile';
post($url,$_POST);
function post($url, $content = null){
	if(function_exists("curl_init")){
		$curl = curl_init();
		if(is_array($content)){
			$content = http_build_query($content);
		}
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_TIMEOUT, 60);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		$ret_data = curl_exec($curl);
		$ret_erro = curl_errno($curl);
		curl_close($curl);
		if(!$ret_erro){
			print_r($ret_data);
		}else{
			print_r($ret_erro);
		}
	}
}
?>