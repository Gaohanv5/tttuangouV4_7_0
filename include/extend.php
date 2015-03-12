<?php

/**
 * 扩展函数
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package base
 * @name extend.php
 * @version 1.0
 */



function table($name)
{
	$forSystem = array(
		'failedlogins',
		'log',
		'memberfields',
		'members',
		'onlinetime',
		'report',
		'robot',
		'robot_ip',
		'robot_log',
		'role',
		'role_action',
		'role_module',
		'sessions'
		);
		if (array_search($name, $forSystem))
		{
			return TABLE_PREFIX.'system_'.$name;
		}
		$forBlank = array(
			'task',
			'task_log'
		);
		if (array_search($name, $forBlank))
		{
			return TABLE_PREFIX.$name;
		}
		return TABLE_PREFIX.'tttuangou_'.$name;
}


function get($key, $limit = '')
{
	$res = logic('safe')->Vars('GET', $key, $limit);
	if( !$res && $res !== 0 ){
		return logic('safe')->Vars('POST', $key, $limit);
	}else{
		return $res;
	}
}


function post($key, $limit = '')
{
	return logic('safe')->Vars('POST', $key, $limit);
}


$__T_Page_Moyo_HTML = '';

function page_moyo($sql = '')
{
	global $__T_Page_Moyo_HTML;
	if ($sql == '')
	{
		$css = ui('loader')->css('@moyo.pager');
		return $css.$__T_Page_Moyo_HTML;
	}
	if (isset($_GET[EXPORT_GENEALL_FLAG]) && $_GET[EXPORT_GENEALL_FLAG] == EXPORT_GENEALL_VALUE)
	{
		return $sql;
	}
	$max = page_moyo_max_selector();
	$flag = 'page';

	$sql_count = preg_replace('/select.*?from/is', 'sELECt COUNT(1) aS MCNT fROm', $sql);

	$result = dbc(DBCMax)->query($sql_count)->done();

	if (stripos($sql_count, 'group')) {
		$total = count($result);
	}else{
		$total = $result['0']['MCNT'];
	}


	if ($total <= $max)
	{
		return $sql;
	}
	$pn = isset($_GET[$flag]) ? (int)$_GET[$flag] : (isset($_POST[$flag]) ? $_POST[$flag] : 1);
	if ($pn <= 0) $pn = 1;
	$sql = $sql . ' LIMIT '.($pn-1)*$max.','.$max;

	$url = page_moyo_request_uri($flag);
	$pa = ceil($total/$max);
	page_moyo_summary(array('total' => (int)$total, 'perpage' => (int)$max, 'pageall' => (int)$pa, 'pagenow' => (int)$pn));
	$pre = '';
	if ($pn > 1)
	{
		$pre = '<a href="'.$url.'&'.$flag.'='.($pn-1).'"><font class="page_up">上一页</font></a>';
	}
	$nxt = '';
	if ($pn < $pa)
	{
		$nxt = '<a href="'.$url.'&'.$flag.'='.($pn+1).'"><font class="page_down">下一页</font></a>';
	}
	$pfirst = '<a href="'.$url.' " style="margin-left:0;"><font class="page_first">首页</font></a>';
	$plast = '<a href="'.$url.'&'.$flag.'='.$pa.'"><font class="page_last">尾页</font></a>';
	$plist = '';
	$al = 10;
	if ($pn - $al/2 <= 0) $pfrom = 1;
	else $pfrom = $pn - $al/2 + 1;
	$pend = $pfrom + $al - 1;
	for ($pi = $pfrom; $pi < $pend; $pi++)
	{
		if ($pi > $pa) break;
		if ($pi != $pn)
		$plist .= '<a href="'.$url.'&'.$flag.'='.$pi.'"><font class="page_number">'.$pi.'</font></a>';
		else $plist .= '<font class="page_current">'.$pi.'</font> ';
	}
	$html = $pfirst.''.$pre.''.$plist.''.$nxt.''.$plast;
	$html .= '<div class="page_count">共 '.$total.' 条记录，分为 '.$pa.' 页，每页 '.page_moyo_max_selector($max).' 条</div>';
	$__T_Page_Moyo_HTML = page_moyo_rewrite($html);
	return $sql;
}


function page_moyo_rewrite($content)
{
	global $rewriteHandler;
	return $rewriteHandler ? $rewriteHandler->output($content, true) : $content;
}


function page_moyo_max_selector($max = null)
{
	if (is_null($max))
	{
		$smax = page_moyo_max_io();
		if (is_numeric($smax))
		{
			return $smax;
		}
		$int = handler('cookie')->GetVar('moyo_pm_int');
		$max = $int ? (int)$int : 12;
		$max = $max ? $max : 12;
		return $max;
	}
	return $max;

	$html = '';
	$html = '<select onchange="alert(this.value)">';
	$pfrom = $max/2 + 2;
	$pend = $max + $max/2 - 1;
	for ($pi = $pfrom; $pi < $pend; $pi++)
	{
		if ($pi <= 0) break;
		if ($pi != $max)
		$html .= '<option value="'.$pi.'">'.$pi.'</option>';
		else
		$html .= '<option value="'.$pi.'" selected="selected">'.$pi.'</option>';
	}
	$html .= '</select>';
	return $html;
}


function page_moyo_request_uri($flag = 'page')
{
	$u = '?';
	foreach ($_GET as $k => $v)
	{
		if ($k != $flag) $u .= thtmlspecialchars($k).'='.urlencode($v).'&';
	}
	return substr($u, 0, -1);
}


function page_moyo_max_io($perpage = null)
{
	static $ppn = false;
	if (is_null($perpage))
	{
		return $ppn;
	}
	else
	{
		return $ppn = $perpage;
	}
}


function page_moyo_summary($summary = null)
{
	static $cache = array();
	if (is_null($summary))
	{
		return $cache;
	}
	else
	{
		return $cache = $summary;
	}
}


function cached($key, $val = null)
{
	$cd = &STATIC_OBJ_STORE::$storageCached;
	if (is_null($val))
	{
		return isset($cd[$key]) ? $cd[$key] : false;
	}
	$cd[$key] = $val;
	return $val;
}


function fcache($key, $mixed, $path = false)
{
	$path || $path = CACHE_PATH.'fcache/';
	if (is_numeric($mixed))
	{
		return driver('cache')->path($path)->read($key, $mixed);
	}
	else
	{
		return driver('cache')->path($path)->write($key, $mixed);
	}
}


$__S_lock_driver = null;

function locked($name, $lock = null)
{

	global  $__S_lock_driver;
	$lck = &$__S_lock_driver;
	if (is_null($lck))
	{
		$lck = driver('lock');
	}
	if ($lock === null)
	{
		return $lck->islocked($name);
	}
	return $lck->locks($name, $lock);
}


function moSpace( $SID, &$Storage = null )
{
	$obj = &STATIC_OBJ_STORE::$objsMoSpace;
	if ( ! is_null($Storage) )
	{
		$obj[$SID] = &$Storage;
	}
	if ( ! isset($obj[$SID]) )
	{
		return false;
	}
	return $obj[$SID];
}


function loadInstance($SID, $className)
{
	$obj = moSpace($SID);
	if ( ! $obj )
	{
		$obj = moSpace($SID, (new $className()));
	}
	return $obj;
}


function mocod($mocod = null)
{
	static $mocodS;
	if (is_null($mocod))
	{
		if (!$mocodS)
		{
			$mod = isset($_GET['mod']) ? $_GET['mod'] : $_POST['mod'];
			if ($mod == '') $mod = 'index';
			$code = isset($_GET['code']) ? $_GET['code'] : $_POST['code'];
			if ($code == '') $code = 'main';
			$mocodS = $mod.'.'.$code;
		}
	}
	else
	{
		$mocodS = $mocod;
	}
	return $mocodS;
}


function imager($id, $size = IMG_Original, $height = 0)
{
	$file404 = ini('settings.site_url').'/static/images/imager.404.jpg';
	if ($size > 0)
	{
		$width = $size;
	}
	elseif ($size == IMG_Original)
	{
		$width = 0;
		$height = 0;
	}
	elseif ($size == IMG_Tiny)
	{
		$width = 80;
		$height = 60;
	}
	elseif ($size == IMG_Small)
	{
		$width = 200;
		$height = 121;
	}
	elseif ($size == IMG_Normal)
	{
		$width = 450;
		$height = 268;
	}
	$file = logic('upload')->GetOne($id);
	if ( !$file || ($file && !is_file($file['path'])) )
	{
		return $file404;
	}
	if ($width == 0 && $height == 0)
	{
		return $file['url'];
	}
	if ($file['extra'] == '')
	{
		$extra = handler('image')->Info($file['path']);
		$data['extra'] = serialize(array(
			'width' => $extra['width'],
			'height' => $extra['height']
		));
		logic('upload')->Update($id, $data);
	}
	else
	{
		$extra = unserialize($file['extra']);
	}
	$srcWidth = $extra['width'];
	$srcHeight = $extra['height'];
	if (abs($srcWidth-$width) * abs($srcHeight-$height) < 1)
	{
		return $file['url'];
	}
	$upd = UPLOAD_PATH;
	$upt = UPLOAD_PATH.'thumb/'.$width.'x'.$height.'/';
	$thumb = str_replace($upd, $upt, $file['path']);
	if (is_file($thumb))
	{
		$file = $thumb;
	}
	else
	{
		$file = logic('image')->thumb($file['path'], $thumb, $width, $height);
	}
	$file = ini('settings.site_url').str_replace('./', '/', $file);
	return $file;
}


function timebefore($time, $nosign = false)
{
	if ($time <= 0)
	{
		return '-----';
	}
	$now = time();
	if ($time > $now)
	{
		return '还未开始！';
	}
	return __timeUnit($now - $time).($nosign ? '' : ' 前');
}


function timeless($time, $sign = null)
{
	if ($time <= 0)
	{
		return '-----';
	}
	$now = time();
	if ($time < $now)
	{
		return '已经结束！';
	}
	return '剩余 '.__timeUnit($now - $time);
}


function __timeUnit($ss, $uc = 1)
{
	$timeCalc = array(
		'天' => 86400,
		'小时' => 3600,
		'分' => 60,
		'秒' => 1
	);
	$return = '';
	foreach ($timeCalc as $name => $secs)
	{
		if ($ss >= $secs)
		{
			$sc = floor($ss / $secs);
			$return .= $sc.' '.$name;
			$ss -= $sc * $secs;
			$uc --;
		}
		if ($uc == 0) break;
	}
	return $return;
}


define('ENC_IS_GBK', (strtolower(ini('settings.charset')) == 'gbk'));

define('X_IS_AJAX', (strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' || strtolower($_POST['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' || strtolower($_GET['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'));


function jsonEncode($value)
{
	if (ENC_IS_GBK)
	{
		if (is_array($value))
		{
			array_walk_recursive($value, '__enc_for_jsonEncode');
		}
		elseif (is_string($value))
		{
			__enc_g2u($value);
		}
	}
	return json_encode($value);
}
function __enc_for_jsonEncode(&$value, &$key)
{
	if (is_string($key))
	{
		$key = ENC_G2U($key);
	}
	if (is_string($value))
	{
		$value = ENC_G2U($value);
	}
}


function ENC_G2U($value)
{
	return __enc_g2u($value);
}
function __enc_g2u(&$value)
{
	$backup = $value;
	$value = iconv('GBK', 'UTF-8/'.'/IGNORE', $value);
	if (empty($value))
	{
		$value = $backup;
	}
	return $value;
}


function ENC_U2G($value)
{
	return __enc_u2g($value);
}
function __enc_u2g(&$value)
{
	$backup = $value;
	$value = iconv('UTF-8', 'GBK/'.'/IGNORE', $value);
	if (empty($value))
	{
		$value = $backup;
	}
	return $value;
}


if(!function_exists('json_encode'))
{
	function json_encode($value)
	{
		if(!class_exists('servicesJSON'))
		{
			$load = new Load();
			$load->lib('servicesJSON');
		}
		$json = new servicesJSON();
		return $json->encode($value);
	}
}

if(!function_exists('json_decode'))
{
	function json_decode($json_value, $bool = false)
	{
		if(!class_exists('servicesJSON'))
		{
			$load = new Load();
			$load->lib('servicesJSON');
		}
		$assoc = ($bool) ? 16 : 32;
		$json = new servicesJSON($assoc);
		return $json->decode($json_value);
	}
}


function ihelper($w)
{
	return 'http://cenwor.com/go.php?w='.$w;
}


function ifaq($kw, $dsp = null)
{
	is_null($dsp) && $dsp = $kw;
	return '<a href="http://t.jishigou.net/search/topic/topic-'.urlencode($kw).'" target="_blank">'.$dsp.'</a>';
}


function dfTimer($w)
{
	$list = array(
		'com.notify.mf.cache' => 3600,
		'com.push.queue.clean' => 3600,
		'system.upgrade.check' => 259200,
		'com.catalog.procount.sync' => 86400
	);
	return isset($list[$w]) ? $list[$w] : 86400;
}


function msockopen($hostname = false, $port = -1, &$errno = null, &$errstr = null, $timeout = 3)
{
	$bin = MSOCKOPEN_Adaptor::selector();
	if ($hostname)
	{
		if ($bin)
		{
			return MSOCKOPEN_Adaptor::$bin(array(
				'hostname' => $hostname,
				'port' => $port,
				'errno' => $errno,
				'errstr' => $errstr,
				'timeout' => $timeout
			));
		}
		return false;
	}
	return $bin;
}

/**
 * msockopen 函数适配器
 * @author Moyo <dev@uuland.org>
 * @version 1.0
 */
class MSOCKOPEN_Adaptor
{
	public static $adaptor = null;
	public static function selector()
	{
		$__msockopen_bin = &self::$adaptor;
		if (is_null($__msockopen_bin))
		{
			if (function_exists('fsock'.'open'))
			{
				$__msockopen_bin = 'fsock_mx_open';
			}
			elseif (function_exists('pfsock'.'open'))
			{
				$__msockopen_bin = 'pfsock_mx_open';
			}
			elseif (function_exists('stream_socket_client'))
			{
				$__msockopen_bin = 'stream_socket_mx_client';
			}
			else
			{
				$__msockopen_bin = false;
			}
		}
		return $__msockopen_bin;
	}
	public static function fsock_mx_open($arg)
	{
		$bin = 'fsock'.'open';
		return $bin($arg['hostname'], $arg['port'], $arg['errno'], $arg['errstr'], $arg['timeout']);
	}
	public static function pfsock_mx_open($arg)
	{
		$bin = 'pfsock'.'open';
		return $bin($arg['hostname'], $arg['port'], $arg['errno'], $arg['errstr'], $arg['timeout']);
	}
	public static function stream_socket_mx_client($arg)
	{
		$bin = 'stream_socket_client';
		return $bin($arg['hostname'].':'.$arg['port'], $arg['errno'], $arg['errstr'], $arg['timeout']);
	}
}


function productCurrentView($product = null)
{
	static $p;
	if (is_null($product))
	{
		return $p ? $p : array();
	}
	else
	{
		$p = $product;
		return $p;
	}
}


function admin_priv($priv_str)
{
	if(MEMBER_ID > 0 && true === AIJUHE_FOUNDER) return true;
	$is_admin_users = in_array(user()->get('role_type'),array('admin','seller'));
	$user_priv = user()->get('privs');
	if($user_priv && $is_admin_users){
		if ($user_priv == 'all'){
			return true;
		}
		if (strpos(','.$user_priv.',', ','.$priv_str.',') === false){
			return false;
		}else{
			return true;
		}
	}else{
		return false;
	}
}
?>