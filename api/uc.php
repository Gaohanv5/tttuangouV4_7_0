<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name uc.php
 * @date 2014-12-11 14:44:49
 */
 



define('UC_CLIENT_VERSION', '1.5.0');
define('UC_CLIENT_RELEASE', '20090121');

define('API_DELETEUSER', 0);
define('API_RENAMEUSER', 0);
define('API_GETTAG', 0);
define('API_SYNLOGIN', 1);
define('API_SYNLOGOUT', 1);
define('API_UPDATEPW', 1);
define('API_UPDATEBADWORDS', 0);
define('API_UPDATEHOSTS', 0);
define('API_UPDATEAPPS', 0);
define('API_UPDATECLIENT', 0);
define('API_UPDATECREDIT', 0);
define('API_GETCREDITSETTINGS', 1);
define('API_GETCREDIT', 1);
define('API_UPDATECREDITSETTINGS', 1);

define('API_RETURN_SUCCEED', '1');
define('API_RETURN_FAILED', '-1');
define('API_RETURN_FORBIDDEN', '-2');

define('ROOT_PATH', realpath(substr(dirname(__FILE__), 0, -3)) . '/');

if(!defined('IN_UC')) {
	@set_magic_quotes_runtime(0);

	defined('MAGIC_QUOTES_GPC') || define('MAGIC_QUOTES_GPC', get_magic_quotes_gpc());
	include_once ROOT_PATH . './setting/settings.php';
	$config = $config['settings'];
	include_once ROOT_PATH . './setting/constants.php';

		if(true !== UCENTER)
	{
		exit('UCENTER is invalid');
	}
	if(!defined('UC_KEY') || '' == UC_KEY)
	{
		exit('UC_KEY is empty');
	}

	$get = $post = array();

	$code = @$_GET['code'];
	parse_str(_authcode($code, 'DECODE', UC_KEY), $get);
	if(MAGIC_QUOTES_GPC) {
		$get = _stripslashes($get);
	}

	$timestamp = time();
	if(empty($get)) {
		exit('Invalid Request');
	} elseif($timestamp - $get['time'] > 3600) {
		exit('Authracation has expiried');
	}
	$action = $get['action'];

	require_once ROOT_PATH.'./uc_client/lib/xml.class.php';
	$post = xml_unserialize(file_get_contents('php:/'.'/input'));

	if(in_array($get['action'], array('test', 'deleteuser', 'renameuser', 'gettag', 'synlogin', 'synlogout', 'updatepw', 'updatebadwords', 'updatehosts', 'updateapps', 'updateclient', 'updatecredit', 'getcredit', 'getcreditsettings', 'updatecreditsettings'))) {
		
		require_once ROOT_PATH.'./api/uc_api_db.php';
		$GLOBALS['db'] = new JSG_UC_API_DB();
		$GLOBALS['db']->connect($config['db_host'],$config['db_user'],$config['db_pass'],$config['db_name'],$config['charset'],$config['db_persist'],$config['db_table_prefix']);
		$GLOBALS['tablepre'] = $config['db_table_prefix'];
		unset($config['db_host'],$config['db_user'],$config['db_pass'],$config['db_name'],$config['charset'],$config['db_persist'],$config['db_table_prefix']);
		

		$uc_note = new uc_note();
		exit($uc_note->$get['action']($get, $post));
	} else {
		exit(API_RETURN_FAILED);
	}

} else {

	;
}

class uc_note {

	var $db = '';
	var $tablepre = '';
	var $appdir = '';

	function _serialize($arr, $htmlon = 0) {
		if(!function_exists('xml_serialize')) {
			include_once $this->appdir.'./uc_client/lib/xml.class.php';
		}
		return xml_serialize($arr, $htmlon);
	}

	function uc_note() {
		$this->appdir = ROOT_PATH;
		$this->db = $GLOBALS['db'];
		$this->tablepre = $GLOBALS['tablepre'];
	}

	function test($get, $post) {
		return API_RETURN_SUCCEED;
	}

	function deleteuser($get, $post) {
		$uids = $get['ids'];
		if(!API_DELETEUSER) {
			return API_RETURN_FORBIDDEN;
		}

		return API_RETURN_SUCCEED;
	}

	function renameuser($get, $post) {
		$uid = $get['uid'];
		$usernameold = $get['oldusername'];
		$usernamenew = $get['newusername'];
		if(!API_RENAMEUSER) {
			return API_RETURN_FORBIDDEN;
		}


		return API_RETURN_SUCCEED;
	}

	function gettag($get, $post) {
		$name = $get['id'];
		if(!API_GETTAG) {
			return API_RETURN_FORBIDDEN;
		}

		return $this->_serialize($return, 1);
	}

	function synlogin($get, $post) {
		$uid = (int) $get['uid'];
		$username = $get['username'];
		if(!API_SYNLOGIN) {
			return API_RETURN_FORBIDDEN;
		}

				header('P3P: CP="CURa ADMa DEVa PSAo PSDo OUR BUS UNI PUR INT DEM STA PRE COM NAV OTC NOI DSP COR"');

		$query = $this->db->query("SELECT uid, password, secques FROM {$this->tablepre}system_members WHERE username='$username'");
		$UserFields = $this->db->fetch_array($query);
		if (!$UserFields) {
			;
		}
		if($UserFields) {
			$auth = TTTuangouAuthcode("{$UserFields['password']}\t{$UserFields['secques']}\t{$UserFields['uid']}");

			_setcookie('sid', '', -86400 * 365);
			_setcookie('auth',$auth,(365*86400));
			_setcookie('cookietime','2592000',(365*86400));
		} else {
			;
		}
	}

	function synlogout($get, $post) {
		if(!API_SYNLOGOUT) {
			return API_RETURN_FORBIDDEN;
		}

				header('P3P: CP="CURa ADMa DEVa PSAo PSDo OUR BUS UNI PUR INT DEM STA PRE COM NAV OTC NOI DSP COR"');
		_setcookie('sid', '', -86400 * 365);
		_setcookie('auth', '', -86400 * 365);
		_setcookie('cookietime', '', -86400 * 365);
	}

	function updatepw($get, $post) {
		if(!API_UPDATEPW) {
			return API_RETURN_FORBIDDEN;
		}

				$username = $get['username'];
		$password = md5($get['password']);
		$this->db->update("UPDATE {$this->tablepre}system_members SET password='$password' WHERE username='$username'");

		return API_RETURN_SUCCEED;
	}

	function updatebadwords($get, $post) {
		if(!API_UPDATEBADWORDS) {
			return API_RETURN_FORBIDDEN;
		}

		return API_RETURN_SUCCEED;
	}

	function updatehosts($get, $post) {
		if(!API_UPDATEHOSTS) {
			return API_RETURN_FORBIDDEN;
		}

		return API_RETURN_SUCCEED;
	}

	function updateapps($get, $post) {
		if(!API_UPDATEAPPS) {
			return API_RETURN_FORBIDDEN;
		}

		return API_RETURN_SUCCEED;
	}

	function updateclient($get, $post) {
		if(!API_UPDATECLIENT) {
			return API_RETURN_FORBIDDEN;
		}

		return API_RETURN_SUCCEED;
	}

	function updatecredit($get, $post) {
		if(!API_UPDATECREDIT) {
			return API_RETURN_FORBIDDEN;
		}
		$i = (int) $get['credit'];
		$amount = intval($get['amount']);
		$uid = intval($get['uid']);

		$credit_field = " `" . ($i < 1 ? 'credits' : 'extcredits' . $i) . "` ";
		$this->db->query("UPDATE {$this->tablepre}members SET {$credit_field}={$credit_field}+'{$amount}' WHERE ucuid='{$uid}' ");

				return API_RETURN_SUCCEED;
	}

	function getcredit($get, $post) {
		if(!API_GETCREDIT) {
			return API_RETURN_FORBIDDEN;
		}

	}

	function getcreditsettings($get, $post) {
		if(!API_GETCREDITSETTINGS) {
			return API_RETURN_FORBIDDEN;
		}
		include_once($this->appdir . './setting/credits.php');

		$credits = array();
		for ($i=0;$i<20;$i++)
		{
			$_tmp_val = $config[($i < 1 ? 'credits' : 'extcredits' . $i)];
			if($_tmp_val)
			{
				$credits[$i] = array($_tmp_val,'');
			}
		}

				return $this->_serialize($credits);
	}

	function updatecreditsettings($get, $post) {
		if(!API_UPDATECREDITSETTINGS) {
			return API_RETURN_FORBIDDEN;
		}
		$outextcredits = array();

		foreach($get['credit'] as $appid => $credititems) {
			if($appid == UC_APPID) {
				foreach($credititems as $value) {
					$outextcredits[$value['appiddesc'].'|'.$value['creditdesc']] = array(
						'creditsrc' => $value['creditsrc'],
						'title' => $value['title'],
						'unit' => $value['unit'],
						'ratio' => $value['ratio']
					);
				}
			}
		}

		$cachefile = $this->appdir.'./uc_client/data/cache/creditsettings.php';
		$fp = fopen($cachefile, 'w');
		$s = "<?php\r\n";
		$s .= '$_CACHE[\'creditsettings\'] = '.var_export($outextcredits, TRUE).";\r\n";
		fwrite($fp, $s);
		fclose($fp);

				return API_RETURN_SUCCEED;
	}
}

function _setcookie($var, $value, $life = 0, $prefix = 1) {
	global $config,$timestamp;

	setcookie(($prefix ? $config['cookie_prefix'] : '').$var, $value,
		($life ? ($timestamp + $life) : 0), $config['cookie_path'],
		$config['cookie_domain'], $_SERVER['SERVER_PORT'] == 443 ? 1 : 0);
}

function _authcode($string, $operation = 'DECODE', $key = '', $expiry = 0) {
	$ckey_length = 4;

	$key = md5($key ? $key : UC_KEY);
	$keya = md5(substr($key, 0, 16));
	$keyb = md5(substr($key, 16, 16));
	$keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';

	$cryptkey = $keya.md5($keya.$keyc);
	$key_length = strlen($cryptkey);

	$string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
	$string_length = strlen($string);

	$result = '';
	$box = range(0, 255);

	$rndkey = array();
	for($i = 0; $i <= 255; $i++) {
		$rndkey[$i] = ord($cryptkey[$i % $key_length]);
	}

	for($j = $i = 0; $i < 256; $i++) {
		$j = ($j + $box[$i] + $rndkey[$i]) % 256;
		$tmp = $box[$i];
		$box[$i] = $box[$j];
		$box[$j] = $tmp;
	}

	for($a = $j = $i = 0; $i < $string_length; $i++) {
		$a = ($a + 1) % 256;
		$j = ($j + $box[$a]) % 256;
		$tmp = $box[$a];
		$box[$a] = $box[$j];
		$box[$j] = $tmp;
		$result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
	}

	if($operation == 'DECODE') {
		if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
			return substr($result, 26);
		} else {
				return '';
			}
	} else {
		return $keyc.str_replace('=', '', base64_encode($result));
	}

}

function _stripslashes($string) {
	if(is_array($string)) {
		foreach($string as $key => $val) {
			$string[$key] = _stripslashes($val);
		}
	} else {
		$string = stripslashes($string);
	}
	return $string;
}


function TTTuangouAuthcode ($string, $operation='ENCODE', $key = '') {
	global $config;

	$key = md5($key ? $key :  md5($config['auth_key']));

	$key_length = strlen($key);
	$string = $operation == 'DECODE' ? base64_decode($string) : substr(md5($string.$key), 0, 8).$string;

	$string_length = strlen($string);
	$rndkey = $box = array();
	$result = '';

	for($i = 0; $i <= 255; $i++) {
		$rndkey[$i] = ord($key[$i % $key_length]);
		$box[$i] = $i;
	}

	for($j = $i = 0; $i < 256; $i++) {
		$j = ($j + $box[$i] + $rndkey[$i]) % 256;
		$tmp = $box[$i];
		$box[$i] = $box[$j];
		$box[$j] = $tmp;
	}
	for($a = $j = $i = 0; $i < $string_length; $i++) {
		$a = ($a + 1) % 256;
		$j = ($j + $box[$a]) % 256;
		$tmp = $box[$a];
		$box[$a] = $box[$j];
		$box[$j] = $tmp;
		$result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
	}
	if($operation == 'DECODE') {
		if(substr($result, 0, 8) == substr(md5(substr($result, 8).$key), 0, 8)) {
			return substr($result, 8);
		} else {
			return '';
		}
	} else {
		return str_replace('=', '', base64_encode($result));
	}
}