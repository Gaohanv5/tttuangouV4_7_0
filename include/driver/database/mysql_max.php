<?php

/**
 * 数据库驱动：Mysql
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package driver
 * @name mysql_max.php
 * @version 1.7
 */

class mysql_maxDatabaseDriver
{
		private $_config_default = array(
		'debug' => false,
		'host' => 'localhost:3306',
		'username' => 'root',
		'password' => '',
		'database' => 'mysql',
		'prefix' => '',
		'charset' => 'utf-8',
		'cached' => 'file://{root}/query_cache/'
	);
	public $CACHE_HASH_SALT = 'sql.cache.uuland.org';
	public $CLIENT_MULTI_RESULTS = 131072;
		private $_config = array();
	private $_debug = true;
	private $_host = '';
	private $_username = '';
	private $_password = '';
	private $_database = '';
	private $_prefix = '';
	private $_charset = '';
		private $_dbc_handle = null;
		private $_trace = array();
		private static $_DBOperator = array();

		public function __destruct()
	{
				$this->close();
	}

		public function config( $config )
	{
		$this->trace('public::config::load');
		$this->_config = $config;
				$this->init();
	}

		private function init()
	{
		$this->trace('private::config::init_default');
				foreach ( $this->_config as $key => $val )
		{
			$mkey = '_' . $key;
			$this->$mkey = isset($this->_config[$key]) ? $this->_config[$key] : $this->_config_default[$key];
		}
				if ($this->_charset)
		{
			$this->_charset = str_replace('-', '', $this->_charset);
		}
				if ( ! $this->_debug ) unset($this->_trace);
		unset($this->_config);
	}

		private function connect()
	{
		$this->trace('public::server::connect');
				$this->_dbc_handle = @mysql_connect($this->_host, $this->_username, $this->_password, true, $this->CLIENT_MULTI_RESULTS);
		if ( ! $this->_dbc_handle )
		{
			$this->alert('Can\'t connect to Server [ ' . $this->_username . '@' . $this->_host . ' ]');
			return false;
		}
				if ( ! mysql_select_db($this->_database, $this->_dbc_handle) )
		{
			$this->alert('Can\'t select database [' . $this->_database . ']');
			return false;
		}
		$version = mysql_get_server_info($this->_dbc_handle);
				if ( $version >= '4.1' )
		{
						mysql_query('SET NAMES "' . $this->_charset . '"', $this->_dbc_handle);
		}
				if ( $version > '5.0.1' )
		{
			mysql_query('SET SQL_Mode=""', $this->_dbc_handle);
		}
		return true;
	}

		private function free($dbo)
	{
		$this->trace('public::query::free');
		if ( $dbo->_query_handle && $dbo->_operate == 'SELECT' )
		{
			mysql_free_result($dbo->_query_handle);
		}
		unset(self::$_DBOperator[$dbo->SID]);
		return true;
	}

	
	private function DBOperator()
	{
		$this->trace('private::operator::create');
		$iBase = count(self::$_DBOperator);
		$nxObjID = 'mysql_max_dbo.'.(string)($iBase+1);
		$obj = new mysql_maxDatabaseOperator($nxObjID);
		$obj->master = &$this;
		self::$_DBOperator[$nxObjID] = &$obj;
		return $obj;
	}

		private function close()
	{
		if ( $this->_dbc_handle )
		{
			$this->trace('public::server::close');
			mysql_close($this->_dbc_handle);
			unset($this->_dbc_handle);
		}
	}

			public function select( $column )
	{
		$dbo = $this->DBOperator();
		$dbo->_operate = 'SELECT';
		$dbo->_column = $column;
		return $dbo;
	}
		public function update( $column )
	{
		$dbo = $this->DBOperator();
		$dbo->_operate = 'UPDATE';
		$dbo->_column = $column;
		return $dbo;
	}
		public function insert( $column )
	{
		$dbo = $this->DBOperator();
		$dbo->_operate = 'INSERT';
		$dbo->_column = $column;
		return $dbo;
	}
		public function delete( $column )
	{
		$dbo = $this->DBOperator();
		$dbo->_operate = 'DELETE';
		$dbo->_column = $column;
		return $dbo;
	}
		public function query($sql)
	{
		$dbo = $this->DBOperator();
		$dbo->_operate = 'UNKNOWN';
		$dbo->_sql = $sql;
		return $dbo;
	}

		public function sql($dbo, $cls = true)
	{
		$this->trace('private::sql::iniz');
								$column = table($dbo->_column);
		$sql = '';
				$dbo->_operate_ori && $dbo->_operate = $dbo->_operate_ori;
				switch ( $dbo->_operate )
		{
			case 'SELECT' :
								if ( $dbo->_field )
				{
					$field = $dbo->_field;
				}
				else
				{
					$field = '*';
				}
				$sql = 'sELECt ' . $field . ' fROm `' . $column . '`' . $this->pack_where($dbo) . $this->pack_group($dbo) . $this->pack_order($dbo) . $this->pack_limit($dbo);
				break;
			case 'UPDATE' :
				$sql = 'uPDATe `' . $column . '`' . $this->pack_data($dbo) . $this->pack_where($dbo);
				break;
			case 'INSERT' :
				$sql = 'iNSERt iNTo `' . $column . '`' . $this->pack_data($dbo);
				break;
			case 'DELETE' :
				$sql = 'dELETe fROm `' . $column . '`' . $this->pack_where($dbo);
				break;
			default :
				if ($dbo->_sql != '')
				{
					$sql = $dbo->_sql;
					$sps = explode(' ', $sql);
					$dbo->_operate_ori = $dbp->_operate;
					$dbo->_operate = strtoupper($sps[0]);
					in_array($dbo->_operate, array('SHOW','DESCRIBE')) && $dbo->_operate = 'SELECT';
				}
				break;
		}
		$cls && $this->free($dbo);
		return $sql;
	}

		public function done($dbo, $__status = array())
	{
				$dbo->sql = $sql = $this->sql($dbo, false);
				if ( ! $this->_dbc_handle ) $this->connect();
				$cr = $this->is_sql_safe($sql);
				$this->trace('public::query::sql[' . $sql . ']');
				$dbo->_query_handle = ($dbo->_limit == 1) ? mysql_unbuffered_query($sql, $this->_dbc_handle) : mysql_query($sql, $this->_dbc_handle);
		if ( ! $dbo->_query_handle )
		{
			return $this->error_fix($dbo, $__status);
		}
		if ( $dbo->_operate == 'SELECT' )
		{
			$dbo->_result = array();
			if ($dbo->_limit == 1)
			{
								$dbo->_result[] = mysql_fetch_assoc($dbo->_query_handle);
			}
			else
			{
				if ( mysql_num_rows($dbo->_query_handle) > 0 )
				{
										while ( false !== $one_row = mysql_fetch_assoc($dbo->_query_handle) )
					{
						$dbo->_result[] = $one_row;
					}
					mysql_data_seek($dbo->_query_handle, 0);
				}
			}
						$return = ($dbo->_limit == 1) ? $dbo->_result[0] : $dbo->_result;
						if ( $this->free($dbo) ) return $return ? $return : null;
		}
		elseif ( $dbo->_operate == 'INSERT' )
		{
			$return = mysql_insert_id($this->_dbc_handle);
						if ( $this->free($dbo) ) return $return;
		}
		else
		{
			$return = mysql_affected_rows($this->_dbc_handle);
						if ( $this->free($dbo) ) return $return;
		}
	}

	
	private function error_fix($dbo, $__status)
	{
		$this->trace('private::error_fix::analyze');
				$errNum = mysql_errno($this->_dbc_handle);
		$errMsg = mysql_error($this->_dbc_handle);
				$errType = 'unknown';
		if (in_array($errNum, array(2006, 2013)))
		{
			$errType = 'server.lost';
			$errCNT = &$__status['errCNT'][$errType];
			$errCNT = (int)$errCNT;
			$errCNT < 2 && $this->close();
		}
		elseif (in_array($errNum, array(1040)))
		{
			$errType = 'server.busy';
			$errCNT = &$__status['errCNT'][$errType];
			$errCNT = (int)$errCNT;
			usleep(500000 * $errCNT);
		}
		elseif (in_array($errNum, array(1062)))
		{
			$errType = 'data.key.duplicate';
			$this->trace('private::error_fix::found(:'.$errType.':)');
			return false;
		}
		elseif (in_array($errNum, array(1064)))
		{
			$errType = 'sql.error';
			$dbo->pack_data_escape = true;
			$errCNT = &$__status['errCNT'][$errType];
			$errCNT || $errCNT = 1;
			is_array($dbo->_data[0]) || $errCNT = 2;
		}
		else
		{
			$errCNT = 3;
		}
		$errCNT ++ ;
		if ($errCNT < 3)
		{
			$this->trace('private::error_fix::re(:'.$errType.':)');
			return $this->done($dbo, $__status);
		}
		return $this->alert('SQL run error.', $dbo);
	}

		private function pack_limit($dbo)
	{
		if ( $dbo->_limit == '' ) return '';
		if ( is_numeric($dbo->_limit) )
		{
			return ' lIMIt 0,' . $dbo->_limit;
		}
		elseif ( is_string($dbo->_limit) )
		{
			return ' lIMIt ' . $dbo->_limit;
		}
	}
		private function pack_where($dbo)
	{
		if ( ! $dbo->_where ) return '';
		$sql_where = ' wHERe ';
		$sql_where_add = '';
		foreach ( $dbo->_where as $where )
		{
			if ( is_array($where) )
			{
				foreach ( $where as $key => $val )
				{
					$kvs = '';
					if ( is_numeric($val) )
					{
						$kvs = '`'.$key.'`' . '=' . $val;
					}
					elseif ( is_string($val) )
					{
						$kvs = '`'.$key.'`' . '="' . $val . '"';
					}
					elseif ( is_array($val) )
					{
						$kvs = '`'.$key.'`' . ' ' . $val[0] . ' ' . $val[1];
					}
					elseif ( is_null($val) )
					{
						$kvs = '`'.$key.'`' . '=NULL';
					}
					elseif( is_bool($val) || empty($val) )
					{
						$kvs = '`'.$key.'`' . '="' . $val . '"';
					}
					$kvs && $sql_where_add .= $kvs.' aNd ';
				}
			}
			elseif ( is_string($where) )
			{
								
				$where && $sql_where_add .= $where.' aNd ';
			}
		}
		if ( ! $sql_where_add ) return '';
		return substr($sql_where.$sql_where_add, 0, - 5);
	}

		private function pack_group($dbo)
	{
		if ($dbo->_group)
		{
			return ' gROUp bY '.$dbo->_group;
		}
		else
		{
			return '';
		}
	}

		private function pack_order($dbo)
	{
		if ( ! $dbo->_order ) return '';
		$sql_order = ' oRDEr bY ';
		$sql_order_add = '';
		foreach ( $dbo->_order as $order )
		{
			if ( is_array($order) )
			{
				foreach ( $order as $key => $type )
				{
					$sql_order_add .= '`'.$key.'`' . ' ' . $type . ', ';
				}
			}
			elseif ( is_string($order) )
			{
				$ords = explode(',', $order);
				foreach ( $ords as $one_ord )
				{
					$sql_order_add .= str_replace('.', ' ', $one_ord) . ', ';
				}
			}
		}
		if ( ! $sql_order_add ) return '';
		return substr($sql_order.$sql_order_add, 0, - 2);
	}

		private function pack_data($dbo)
	{
		if ( ! $dbo->_data ) return '';
		$sql_data = ' sEt ';
		$sql_data_add = '';
		foreach ( $dbo->_data as $data )
		{
			if ( is_array($data) )
			{
				foreach ( $data as $key => $val )
				{
					$noData = false;
					if ( is_numeric($val) )
					{
						$sql_data_add .= '`'.$key.'`' . '=' . $val;
					}
					elseif ( is_string($val) )
					{
						$sql_data_add .= '`'.$key.'`' . '="' . ($dbo->pack_data_escape ? mysql_real_escape_string($val) : $val) . '"';
					}
					else
					{
						$noData = true;
					}
					$noData || $sql_data_add .= ', ';
				}
			}
			elseif ( is_string($data) )
			{
								
				$data && $sql_data_add .= $data.', ';
			}
		}
		if ( ! $sql_data_add ) return '';
		return substr($sql_data.$sql_data_add, 0, - 2);
	}

					public function alert( $message, $dbo = false )
	{
				$errNum = @mysql_errno($this->_dbc_handle);
		$errMsg = @mysql_error($this->_dbc_handle);
				$dbo && zlog('error')->found('mysql', '['.$errNum.']: '.$errMsg.'<pre>'.thtmlspecialchars($dbo->sql).'</pre>');
				if (!$this->_debug)
		{
			$dbo && $this->free($dbo);
			return false;
		}
				if (!logic('misc')->siteInstalled())
		{
			return false;
		}
				echo '<div style="border:2px solid #000;margin:10px;padding:10px;">';
		echo $message;
		if ($this->_debug)
		{
			$errMsg && print('<hr/>'.$errMsg);
			$dbo->sql && print('<hr/>'.$dbo->sql);
		}
		$btAll = function_exists('debug_backtrace') ? debug_backtrace() : false;
		if ($btAll)
		{
			$btLength = count($btAll);
			$btLength > 7 && $btLength = 7;
			$btString = '';
			$btIII = 0;
			for ($btI = $btLength; $btI > 0; $btI--)
			{
				$btOne = $btAll[$btI-1];
				$btIII ++;
				$btString .= $btIII.'. FILE:'.basename($btOne['file']).' - LINE:'.$btOne['line'].' - FUNC:'.$btOne['function'].'<br/>';
			}
			echo '<hr/>'.$btString;
		}
		echo '</div>';
		exit;
	}

	
	public function is_sql_safe($sql, $refresh_status = false)
	{
		static $status = null;
		static $checkcmd = array('SEL'=>1, 'UPD'=>1, 'INS'=>1, 'REP'=>1, 'DEL'=>1);		
		
		if ($refresh_status === true)
		{
			return $status = null;
		}
		static $checkcmd = array('SELECT', 'UPDATE', 'INSERT', 'REPLACE', 'DELETE');
		if($status === null) $status = ini('wips.sql.enabled');
		if($status)
		{
			if (WEB_BASE_ENV_DFS::$APPNAME == 'admin')
			{
				if (ini('wips.sql.foradm') == 'false')
				{
					$status = false;
					return true;
				}
			}
						$cmd = strtoupper(substr(trim($sql), 0, 3));
			if(isset($checkcmd[$cmd]))			{
				$this->trace('public::is_sql_safe::sql[...]');
				$cr = $this->_do_query_safe($sql);
				$cr === true || $this->sql_hack_found($cr);
			}
		}
		return true;
	}
	
	public function do_sql_safe_query($sql)
	{
		static $status = null;
		if($status === null) $status = ini('wips.sql.enabled');
		if ($status)
		{
			return $this->_do_query_safe($sql);
		}
		else
		{
			return true;
		}
	}
	
	private function _do_query_safe($sql)
	{
		static $_CONFIG = null;
		if($_CONFIG === null)
		{
			$alist = array('dfunction', 'daction', 'dnote');
			foreach ($alist as $__i => $fmode)
			{
				$fmlist = explode(',', ini('wips.sql.'.$fmode));
				foreach ($fmlist as $i => $one)
				{
					if (trim($one) != '')
					{
						$_CONFIG[$fmode][] = $one;
					}
				}
			}
			$_CONFIG['afullnote'] = ini('wips.sql.afullnote') == 'true' ? 1 : 0;
			$_CONFIG['dlikehex'] = ini('wips.sql.dlikehex') == 'true' ? 1 : 0;
		}
				$sql = str_replace(array('\\\\', '\\\'', '\\"', '\'\''), '', $sql);
		$mark = $clean = '';
				$sql = preg_replace("/'(.+?)'/s", '', $sql);
		$sql = preg_replace('/"(.+?)"/s', '', $sql);
				if (strpos($sql, '/') === false && strpos($sql, '#') === false && strpos($sql, '-- ') === false && strpos($sql, '@') === false && strpos($sql, '`') === false)
		{

		}
		else
		{
			$len = strlen($sql);
			$mark = $clean = '';
			for ($i = 0; $i < $len; $i++)
			{
				$str = $sql[$i];
				switch ($str) {
					case '`':
						if(!$mark)
						{
							$mark = '`';
							$clean .= $str;
						}
						elseif ($mark == '`')
						{
							$mark = '';
						}
						break;
					case '\'':
						if (!$mark)
						{
							$mark = '\'';
							$clean .= $str;
						}
						elseif ($mark == '\'')
						{
							$mark = '';
						}
						break;
					case '/':
						if (empty($mark) && $sql[$i + 1] == '*')
						{
							$mark = '/'.'*';
							$clean .= $mark;
							$i++;
						}
						elseif ($mark == '/'.'*' && $sql[$i - 1] == '*')
						{
							$mark = '';
							$clean .= '*';
						}
						break;
					case '#':
						if (empty($mark))
						{
							$mark = $str;
							$clean .= $str;
						}
						break;
					case "\n":
						if ($mark == '#' || $mark == '--')
						{
							$mark = '';
						}
						break;
					case '-':
						if (empty($mark) && substr($sql, $i, 3) == '-- ')
						{
							$mark = '-- ';
							$clean .= $mark;
						}
						break;
					default:
						break;
				}
				$clean .= $mark ? '' : $str;
			}
		}
				
				$clean = preg_replace("/[^a-z0-9_\-\(\)#\*\/\"]+/is", "", strtolower($clean));
				if ($_CONFIG['afullnote'])
		{
			$fgs = array('/'.'**'.'/', '#', '--');
			foreach ($fgs as $fgk)
			{
				if (strpos($clean, $fgk) !== false)
				{
					return array('r' => 'afullnote', 'm' => $fgk);
				}
			}
		}
		else
		{
			$clean = str_replace('/'.'**'.'/', '', $clean);
		}
				if (is_array($_CONFIG['dfunction']))
		{
			foreach ($_CONFIG['dfunction'] as $fun)
			{
				if (strpos($clean, $fun . '(') !== false)
					return array('r' => 'dfunction', 'm' => $fun);
			}
		}
				if (is_array($_CONFIG['daction']))
		{
			foreach ($_CONFIG['daction'] as $action)
			{
				if (strpos($clean, $action) !== false)
					return array('r' => 'daction', 'm' => $action);
			}
		}
				if ($_CONFIG['dlikehex'] && strpos($clean, 'like0x'))
		{
			return array('r' => 'dlikehex', 'm' => 'like0x');
		}
				if (is_array($_CONFIG['dnote']))
		{
			foreach ($_CONFIG['dnote'] as $note)
			{
				if (strpos($clean, $note) !== false)
					return array('r' => 'dnote', 'm' => $note);
			}
		}
		return true;
	}

	
	public function sql_hack_found($data)
	{
				zlog('wips')->sql($data['r'], $data['m']);
				$base = base64_decode('aHR0cDovL3NxbC50dHR1YW5nb3UubmV0L3dpcHMucGhwPw==');
		$send = base64_encode($data['r'].base64_decode('e01PWU99').$data['m']);
		header('Location: '.$base.$send);
		exit;
	}

		private function trace( $message )
	{
		if ( ! $this->_debug ) return;
		$this->_trace[] = array(
			'timer' => microtime(true), 'mmusage' => function_exists('memory_get_usage') ? memory_get_usage() : 0, 'message' => $message
		);
	}

		public function trace_output()
	{
		if ( ! $this->_debug ) return;
		echo '<div style="border:2px solid #000;margin:10px;padding:10px;text-align:left;">';
		echo '<ul>';
				$all_timer = 0;
		$all_mmusage = 0;
				$last_timer = $this->_trace[0]['timer'];
		$last_mmusage = $this->_trace[0]['mmusage'];
		foreach ( $this->_trace as $i => $_lTrace )
		{
			$trace = $this->_trace[$i+1];
			$trace || $trace = $_lTrace;
			$timer = $trace['timer'];
			$mmusage = $trace['mmusage'];
			$cMMUsage = $mmusage - $last_mmusage;
			if ($cMMUsage > 0)
			{
				$cMMDsp = '<font color="#E56298">+'.$cMMUsage.'</font>';
			}
			else
			{
				$cMMDsp = '<font color="#53B0FC">-'.abs($cMMUsage).'</font>';
			}
			echo '<li>Time: ' . $timer . ' <font color="#0FC69D">+' . ($timer - $last_timer) . '</font> Memory: ' . $trace['mmusage'] . ' '.$cMMDsp.' Call: ' . $_lTrace['message'] . '</li>';
			if ($i > 0)
			{
				$all_timer += $timer - $last_timer;
				$all_mmusage += $mmusage - $last_mmusage;
			}
			$last_timer = $timer;
			$last_mmusage = $mmusage;
		}
		$all_timer *= 1000;
		$all_mmusage /= 1024;
		echo '<li><hr/></li>';
		echo '<li>Time-all: '.$all_timer.' (ms) && Memory-all: '.$all_mmusage.' (kb)</li>';
		echo '</ul>';
		echo '</div>';
	}
}

/**
* Mysql(MAX) 数据操作子类
* @author Moyo <dev@uuland.org>
*/
class mysql_maxDatabaseOperator
{
		public $master = null;
		public $sql = '';
	public $_query_handle = null;
	public $_result = array();
		public $_operate = '';
	public $_operate_ori = '';
	public $_column = '';
	public $_field = '';
	public $_where = array();
	public $_group = '';
	public $_order = array();
	public $_limit = '';
	public $_data = array();
	public $_sql = '';
		public $pack_data_escape = false;
		public $SID = '';
	
	public function __construct($sid)
	{
		$this->SID = $sid;
	}

		public function in( $field )
	{
		$this->_field = $field;
		return $this;
	}

		public function where( $where )
	{
		$this->_where[] = $where;
		return $this;
	}

		public function group( $group )
	{
		$this->_group = $group;
		return $this;
	}

		public function order( $order )
	{
		$this->_order[] = $order;
		return $this;
	}

		public function limit( $limit )
	{
		$this->_limit = $limit;
		return $this;
	}

		public function data( $data )
	{
		$this->_data[] = $data;
		return $this;
	}
	
	public function sql()
	{
		return $this->master->sql($this);
	}
	
	public function done()
	{
		return $this->master->done($this);
	}
}

?>
