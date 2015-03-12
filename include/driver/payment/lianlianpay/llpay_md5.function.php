<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name llpay_md5.function.php
 * @date 2014-10-30 10:42:13
 */
 




function md5Sign($prestr, $key) {
	$prestr = $prestr ."&key=". $key;
	true === DEBUG_LIANLIANPAY && file_put_contents(ROOT_PATH . "errorlog/pay.lianlianpay.".date("Ym").".log","签名原串:".$prestr."\n", FILE_APPEND);
	return md5($prestr);
}


function md5Verify($prestr, $sign, $key) {
	$prestr = $prestr ."&key=". $key;
	true === DEBUG_LIANLIANPAY && file_put_contents(ROOT_PATH . "errorlog/pay.lianlianpay.".date("Ym").".log","prestr:".$prestr."\n", FILE_APPEND);
	$mysgin = md5($prestr);
	true === DEBUG_LIANLIANPAY && file_put_contents(ROOT_PATH . "errorlog/pay.lianlianpay.".date("Ym").".log","mysgin:".$mysgin."\n", FILE_APPEND);
	if($mysgin == $sign) {
		return true;
	}
	else {
		return false;
	}
}
?>