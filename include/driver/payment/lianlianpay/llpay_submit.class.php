<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name llpay_submit.class.php
 * @date 2014-10-30 10:42:13
 */
 



require_once ("llpay_core.function.php");
require_once ("llpay_md5.function.php");

class LLpaySubmit {

	var $llpay_config;
	
	var $llpay_gateway_new = 'https://yintong.com.cn/payment/bankgateway.htm';

	function __construct($llpay_config) {
		$this->llpay_config = $llpay_config;
	}
	function LLpaySubmit($llpay_config) {
		$this->__construct($llpay_config);
	}

	
	function buildRequestMysign($para_sort) {
						$prestr = createLinkstring($para_sort);
				$mysign = "";
		switch (strtoupper(trim($this->llpay_config['sign_type']))) {
			case "MD5" :
				$mysign = md5Sign($prestr, $this->llpay_config['key']);
				break;
			default :
				$mysign = "";
		}
		true === DEBUG_LIANLIANPAY && file_put_contents(ROOT_PATH . "errorlog/pay.lianlianpay.".date("Ym").".log","签名:".$mysign."\n", FILE_APPEND);
		return $mysign;
	}

	
	function buildRequestPara($para_temp) {
				$para_filter = paraFilter($para_temp);
				$para_sort = argSort($para_filter);
				$mysign = $this->buildRequestMysign($para_sort);
				$para_sort['sign'] = $mysign;
		$para_sort['sign_type'] = strtoupper(trim($this->llpay_config['sign_type']));
		foreach ($para_sort as $key => $value) {
			$para_sort[$key] = $value;
		}
		if('3' == $this->llpay_config['app_request']) {
						if('api' == WEB_BASE_ENV_DFS::$APPNAME) {
				return json_encode($para_sort);
			} else {
				return urldecode(json_encode($para_sort));
			}
		} else {
			return $para_sort;
		}
		
	}

	
	function buildRequestParaToString($para_temp) {
				$para = $this->buildRequestPara($para_temp);

				$request_data = createLinkstringUrlencode($para);

		return $request_data;
	}

	
	function buildRequestForm($para_temp, $method, $button_name) {
				$para = $this->buildRequestPara($para_temp);
		if(false == $para) {
			exit('llpaysubmit.buildRequestForm.$para is empty');
		}
				$sHtml = "<form id='llpaysubmit' name='llpaysubmit' action='" . $this->llpay_gateway_new . "' method='" . $method . "'>";
		if('3' == $this->llpay_config['app_request'] && is_string($para)) {
			$sHtml .= "<input type='hidden' name='req_data' value='" . $para . "'/>";
		} elseif (is_array($para)) {
			$sHtml .= "<input type='hidden' name='version' value='" . $para['version'] . "'/>";
			$sHtml .= "<input type='hidden' name='oid_partner' value='" . $para['oid_partner'] . "'/>";
			$sHtml .= "<input type='hidden' name='user_id' value='" . $para['user_id'] . "'/>";
			$sHtml .= "<input type='hidden' name='timestamp' value='" . $para['timestamp'] . "'/>";
			$sHtml .= "<input type='hidden' name='sign_type' value='" . $para['sign_type'] . "'/>";
			$sHtml .= "<input type='hidden' name='sign' value='" . $para['sign'] . "'/>";
			$sHtml .= "<input type='hidden' name='busi_partner' value='" . $para['busi_partner'] . "'/>";
			$sHtml .= "<input type='hidden' name='no_order' value='" . $para['no_order'] . "'/>";
			$sHtml .= "<input type='hidden' name='dt_order' value='" . $para['dt_order'] . "'/>";
			$sHtml .= "<input type='hidden' name='name_goods' value='" . $para['name_goods'] . "'/>";
			$sHtml .= "<input type='hidden' name='info_order' value='" . $para['info_order'] . "'/>";
			$sHtml .= "<input type='hidden' name='money_order' value='" . $para['money_order'] . "'/>";
			$sHtml .= "<input type='hidden' name='notify_url' value='" . $para['notify_url'] . "'/>";
			$sHtml .= "<input type='hidden' name='url_return' value='" . $para['url_return'] . "'/>";
			$sHtml .= "<input type='hidden' name='userreq_ip' value='" . $para['userreq_ip'] . "'/>";
			$sHtml .= "<input type='hidden' name='url_order' value='" . $para['url_order'] . "'/>";
			$sHtml .= "<input type='hidden' name='valid_order' value='" . $para['valid_order'] . "'/>";
			$sHtml .= "<input type='hidden' name='bank_code' value='" . $para['bank_code'] . "'/>";
			$sHtml .= "<input type='hidden' name='pay_type' value='" . $para['pay_type'] . "'/>";
			$sHtml .= "<input type='hidden' name='no_agree' value='" . $para['no_agree'] . "'/>";
			$sHtml .= "<input type='hidden' name='shareing_data' value='" . $para['shareing_data'] . "'/>";
			$sHtml .= "<input type='hidden' name='risk_item' value='" . $para['risk_item'] . "'/>";
			$sHtml .= "<input type='hidden' name='id_type' value='" . $para['id_type'] . "'/>";
			$sHtml .= "<input type='hidden' name='id_no' value='" . $para['id_no'] . "'/>";
			$sHtml .= "<input type='hidden' name='acct_name' value='" . $para['acct_name'] . "'/>";
			$sHtml .= "<input type='hidden' name='flag_modify' value='" . $para['flag_modify'] . "'/>";
			$sHtml .= "<input type='hidden' name='card_no' value='" . $para['card_no'] . "'/>";
			$sHtml .= "<input type='hidden' name='back_url' value='" . $para['back_url'] . "'/>";
		}
				$sHtml = $sHtml . "<input type='submit' value='" . $button_name . "'></form>";
		$sHtml = $sHtml."<script>document.forms['llpaysubmit'].submit();</script>";
		return $sHtml;
	}

	
	function buildRequestHttp($para_temp) {
		$sResult = '';

				$request_data = $this->buildRequestPara($para_temp);

				$sResult = getHttpResponsePOST($this->llpay_gateway_new, $this->llpay_config['cacert'], $request_data, trim(strtolower($this->llpay_config['input_charset'])));

		return $sResult;
	}

	
	function buildRequestHttpInFile($para_temp, $file_para_name, $file_name) {

				$para = $this->buildRequestPara($para_temp);
		$para[$file_para_name] = "@" . $file_name;

				$sResult = getHttpResponsePOST($this->llpay_gateway_new, $this->llpay_config['cacert'], $para, trim(strtolower($this->llpay_config['input_charset'])));

		return $sResult;
	}

	
	function query_timestamp() {
		$url = $this->llpay_gateway_new . "service=query_timestamp&partner=" . trim(strtolower($this->llpay_config['partner'])) . "&_input_charset=" . trim(strtolower($this->llpay_config['input_charset']));
		$encrypt_key = "";

		$doc = new DOMDocument();
		$doc->load($url);
		$itemEncrypt_key = $doc->getElementsByTagName("encrypt_key");
		$encrypt_key = $itemEncrypt_key->item(0)->nodeValue;

		return $encrypt_key;
	}
}
?>