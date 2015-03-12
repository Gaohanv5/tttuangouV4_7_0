<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name mysql.db.php
 * @date 2014-12-11 14:44:49
 */
 



class MySqlHandler extends DatabaseHandler
{
	var $TableName; 	var $FieldList; 
	
	var $Charset='gbk';
	
	var $_cacheHandler=null;
	
	function MySqlHandler($server_host, $server_port = '3306')
	{
		$this->DatabaseHandler($server_host, $server_port);
	}
		
	function DoConnect($username, $password, $database, $persist = true,$setkey=1)
	{
		$host = $this->ServerHost . ':' . $this->ServerPort;

		if($persist)
		{
			@$db=mysql_pconnect($host, $username, $password,true);
		}
		else
		{
			@$db=mysql_connect($host, $username, $password,true);
		}
		$db==false?exit(mysql_errno().":".mysql_error()):$this->setConnectionId($db);

		if($this->GetVersion() > '4.1')
		{
			if($this->Charset)
			{
				@mysql_query("SET character_set_connection={$this->Charset},
							 character_set_results={$this->Charset},
							 character_set_client=binary",$db);
			}

			if($this->GetVersion() > '5.0.1')mysql_query("SET sql_mode=''",$db);
		}

		if(false == mysql_select_db($database, $this->GetConnectionId()))
		{
			$this->setConnectionId(0);
		}
	}
	
	function Charset($charset)
	{
		$this->Charset=str_replace("-", "", strtolower($charset));
	}
	

	function Query($sql,$type='SKIP_ERROR')
	{
		if(true===DEBUG)
		{
			$debug_list = debug_backtrace();
			foreach($debug_list as $key => $debug)
			{
				if($debug['file'] != __FILE__ and basename($debug['file']) != 'cache.db.php')
				{
					if($debug['class'] == __CLASS__ or $debug['class'] == 'cachehandler')
					{
						$file = $debug['file'];
						$line = $debug['line'];
					}
				}
			}
			$start = explode(" ", microtime());
			$start = $start[1] + $start[0];
		}
		$func=$type==='UNBUFFERED'?'mysql_unbuffered_query':'mysql_query';
				$cr = dbc(DBCMax)->is_sql_safe($sql);
				$result = $func($sql, $this->GetConnectionId());
		if($result==false)
		{
			if(in_array($this->GetLastErrorNo(), array(2006, 2013)) && substr($type, 0, 5) != 'RETRY') {
				$this->CloseConnection();

				require ROOT_PATH . './settings.php';
				$this->MySqlHandler($db_host,$db_port);
				$this->Charset($charset);
				$this->DoConnect($db_user,$db_pass,$db_name,$db_persist,0);

				$result = $this->Query($sql, 'RETRY'.$type);
			} elseif (in_array($this->GetLastErrorNo(), array(1040)) && substr($type,0,4) != "WAIT" && substr($type,0,5) < "WAIT3") {
				usleep(100000 * max(1,min(6,2 * ((int) substr($type,4,1) + 1))));

				$result = $this->Query($sql, 'WAIT'.++$WAITTIMES.$type);
			} elseif ($type != 'SKIP_ERROR' && substr($type, 5) != 'SKIP_ERROR') {
				die($this->GetLastError($sql, $file, $line));
			} else {
				return false;
			}
		}

		if(true===DEBUG)
		{

			$stop = explode(" ", microtime());
			$stop = round(($stop[1] + $stop[0]) - $start, 5);

									$explain="";
			if (substr(trim(strtoupper($sql)),0,6)=="SELECT")
			{
				$explain_id = mysql_query("EXPLAIN $sql", $this->GetConnectionId());
				while($array=mysql_fetch_array($explain_id)) {
					if(!empty($explain)) $explain .="<hr>";
					$explain .= "
					Sql_Table: $array[table]<br />
					Query_type: $array[type]<br />
					possible_keys: $array[possible_keys]<br />
					<font color=red>Query_key: $array[key]<br />
					Query_rows: $array[rows]<br />
					Query_time: $stop</font><br />
					key_len: $array[key_len]<br />
					ref: $array[ref]<br />
					extra: $array[Extra]<br />
				  ";
				}
			}

			$this->SetSqlStore(array('SQL' => $sql, 'TIME' => $stop, "FILE" => $file, "LINE" => $line, "FROM" => "Database", "explain" => $explain));
		}

		return new MySqlIterator($result);
	}

	
	function SetCacheHandler(&$rCacheHandler)
	{
		$this->_cacheHandler=$rCacheHandler;
	}
	
	function SetTable($tableName,$skip_error=false)
	{
		$this->TableName = $tableName;
		if(isset($this->Table[$tableName]))
		{
			$this->FieldList = $this->Table[$tableName];
		}
		else
		{
			if (($fieldList=cache("table/{$tableName}",-1))===false)
			{
				$sql = "SHOW \n\tCOLUMNS \nFROM \n\t`{$this->TableName}`";
				$query = $this->Query($sql,$skip_error?"SKIP_ERROR":"");
				if($query==false)return false;
				$fieldList = array();
				while($row = $query->GetRow())
				{
					if($row['Extra'] === "auto_increment")
					{
						$fieldList[$row['Key']] = $row['Field'];
					}
					else
					{
						$fieldList[] = $row['Field'];
					}
				}
				cache($fieldList);
			}
			$this->FieldList = $fieldList;
			$this->Table[$tableName] = $fieldList;
		}
		Return $this->FieldList;
	}
								function Select($id = '', $condition = NULL, $fields = "*")
	{
		if($condition === NULL)
		{
			if($ids = $this->BuildIn($id))
			{
				$where = "\r\nWHERE \n\t" . $ids;
			}
			else
			{
				Return false;
			}
		}
		else
		{
			if(trim($condition) != "")
			{
				$where = "\r\nWHERE \n\t" . $condition;
			}
		}

		$fieldNames = "\n\t*";
		$field_num=0;
		if($fields != "*")
		{
			$fieldNames = "\n\t".$this->FieldList['PRI'];
			if(is_string($fields) != false)
			{
				$field_list = explode(',', $fields);
			}elseif(is_array($fields) != false)
			{
				$field_list = array_filter($fields, 'strlen');
			}
			$valid_field_list=array();
			foreach($field_list as $key => $field)
			{
				if(in_array($field, $this->FieldList))
				{
					$fieldNames .= ",\n\t`" . $field . '`';
					$valid_field_list[]=$field;
				}
			}
			$field_num=count($valid_field_list);
			$fieldNames = ($field_num>=1)?ltrim($fieldNames, ",\n\t"):"\n\t*";
		}

		$sql = "SELECT {$fieldNames} \nFROM \n\t`{$this->TableName}` {$where}";

		$query = $this->query($sql);
		$data_list = array();
		if($field_num==1)$field_name=implode('',$valid_field_list);
		if($query->GetNumRows() > 1)
		{
			while($row = $query->GetRow())
			{
				$data_list[$row[$this->FieldList['PRI']]] =($field_num==1)?$row[$field_name]:$row;
			}
		}
		else
		{
			$row = $query->GetRow();
			$data_list =($field_num==1)?$row[$field_name]:$row;
		}
		Return $data_list;
	}
							function Replace($dataList)
	{
		if($dataList == "")Return false;
		foreach($this->FieldList as $key => $field)
		{
			if(isset($dataList[$field]))
			{
				$fieldNames .= ",\n\t`" . $field . '`';
				$fieldValues .= ",\n\t\"" . $dataList[$field] . "\"";
			}
		}
		$sql = sprintf("REPLACE INTO \n\t`%s`(%s) \nVALUES(%s)", $this->TableName, ltrim($fieldNames, ','), ltrim($fieldValues, ','));
		$this->query($sql);

		return $this->Insert_ID();
	}
							function Insert($dataList,$continue_primary_key=true)
	{
		if(($sql=$this->BuildInsert($this->TableName,$dataList,$continue_primary_key,true))=="")return false;
		$this->query($sql);
		return $this->Insert_ID();
	}
									function Update($dataList, $condition = NULL)
	{
		if(($sql=$this->BuildUpdate($this->TableName,$dataList,$condition,true))=="")return false;
		if ($this->query($sql))
		{
			return $this->AffectedRows();
		}
		else
		{
			return false;
		}
	}
							function Delete($id = "", $condition = NULL)
	{
		if($condition === NULL)
		{
			if($ids = $this->BuildIn($id))
			{
				$where = "WHERE " . $ids;
			}
			else
			{
				Return false;
			}
		}
		else
		{
			if(trim($condition) != "")
			{
				$where = "\r\nWHERE \n\t" . $condition;
			}
		}

		$sql = "DELETE FROM `{$this->TableName}` {$where}";
		if ($this->query($sql))
		{
			return $this->AffectedRows();
		}
		else
		{
			return false;
		}
	}

	function BuildField($mixed)
	{
		if($mixed==false or trim($mixed)=="*")Return "*";
		$type=gettype($mixed);
		if($type=="string" or $type=="integer" or $type=="double")
		{
			$mixed=trim($mixed,',');
			$mixed=strpos($mixed,',')!==false?"'".str_replace(',',"`,`",$mixed)."'":"`$mixed`";
		}
		elseif($type=="array")
		{
			$mixed="`".implode("`,`",$mixed)."`";
		}
		Return $mixed;
	}
	
	function BuildInsert($tableName,$dataList,$continue_primary_key=true,$filterValid=false)
	{
		if(is_array($dataList) == false)Return '';
		if($filterValid===true)
		{
			$this->SetTable($tableName);
			foreach($this->FieldList as $key => $field)
			{
				if(strcmp($key, "PRI") === 0 and $continue_primary_key===true)
				{
					continue;
				}
				if(isset($dataList[$field]))
				{
					$fieldNames .= ",\n\t`" . $field . '`';
					$fieldValues .= ",\n\t\"" . $dataList[$field] . "\"";
				}
			}
			if ($fieldNames=='' or $fieldValues=='')return '';
		}
		else
		{
			foreach($dataList as $field=>$value)
			{
				$fieldNames .= ",\n\t`" . $field . '`';
				$fieldValues .= ",\n\t\"" .$value. "\"";
			}
			$this->TableName=$tableName;
		}
		$sql = sprintf("INSERT INTO \n\t`%s`(%s) \nVALUES(%s)", $tableName, ltrim($fieldNames, ','), ltrim($fieldValues, ','));
		return $sql;
	}

	function BuildUpdate($tableName,$dataList,$condition=null,$filterValid=false)
	{
		if(is_array($dataList) == false)Return '';
		if($filterValid===true)
		{
			$this->SetTable($tableName);
			foreach($this->FieldList as $key => $field)
			{
				if(isset($dataList[$field]))
				{
					if($key === "PRI")
					{
						if($ids = $this->BuildIn($dataList[$field]))$where = "WHERE \n\t" . $ids;
					}
					else
					{
						$value=$dataList[$field];
						$fieldUpdate .=strpos($value, 'eval:') === 0?"\n\t`{$field}`=".substr($value, 5).",":"\n\t`{$field}`='{$value}',";
					}
				}
			}
		}
		else
		{
			$this->TableName=$tableName;
			foreach ($dataList as $field=>$value)
			{
				$fieldUpdate .= strpos($value, 'eval:') === 0?"\n\t`{$field}`=".substr($value, 5).",":"\n\t`{$field}`='{$value}',";
			}
		}
		if($fieldUpdate == '')Return '';
		if($condition !== NULL)
		{
			$where = (trim($condition) != "")?"WHERE \n\t" . $condition:"";
		}
		elseif($filterValid==true)
		{
			if($dataList[$this->FieldList['PRI']] == "")Return '';
		}
		$sql = sprintf("UPDATE \n\t`%s` \t\nSET %s \n%s", $this->TableName, rtrim($fieldUpdate, ','), $where);
		return $sql;
	}
	function BuildIn($mixed,$name=null)
	{
		if($name === NULL)$name = $this->FieldList['PRI'];
		$type=gettype($mixed);
		if($type=="string" or $type=="integer" or $type=="double")
		{
			$mixed=trim($mixed,',');
			$mixed=strpos($mixed,',')!==false
					?"'".str_replace(',',"','",$mixed)."'"
					:"'$mixed'";
		}
		elseif($type=="array")
		{
			$mixed=!empty($mixed)?"'".implode("','",array_unique($mixed))."'":'null';
		}

		Return $name!=null?"$name IN ($mixed)":$mixed;
	}

	function SaveLog($sql, $filea, $line, $query_dir = './errorlog/')
	{
		if(!is_dir($query_dir))tmkdir($query_dir);
		$file = $query_dir . date('Y-m') . '.php';
		if(!is_file($file))
		{
			$create = fopen($file, 'w');
			if($create)
			{
				fwrite($create, "<?php\r\n\$query=array();\r\n?>");
			}
			fclose($create);
		}
		include($file);
		$query = (array)$query;
		$query_exists = array_filter($query, create_function('$var', 'return ($var["sql"]=="' . $sql . '");'));

		if($query_exists == false)
		{
			$query_string = array('time' => time(),
			'sql' => $sql,
			'file' => $filea,
			'line' => $line,
			);
			array_unshift($query, $query_string);
			$query = var_export($query, true);
			$query_str = "<?php\r\n\$query=$query;\r\n?>";
			$fp = fopen($file, 'w');
			if($fp)
			{
				fwrite($fp, $query_str);
			}
			fclose($fp);
		}
	}

	
	function GetVersion()
	{
		return mysql_get_server_info($this->GetConnectionId());
	}

	
	function GetLastError($sql, $file, $line)
	{
		$error = mysql_error($this->GetConnectionId());

		if(function_exists('error'))
		{
			error(MY_QUERY_ERROR, $error . '|^|' . $sql, $file, $line);

			return true;
		}

		return $error . $sql;
	}
	function GetLastErrorString()
	{
		return mysql_error($this->GetConnectionId());
	}
	function GetLastErrorNo()
	{
		return mysql_errno($this->GetConnectionId());
	}

	
	function Insert_ID()
	{
		return mysql_insert_id($this->GetConnectionId());
	}

	function LastInsertId()
	{
		$this->Insert_ID();
	}

	
	function AffectedRows()
	{
		Return mysql_affected_rows($this->GetConnectionId());
	}

	
	function CloseConnection()
	{
		return mysql_close($this->GetConnectionId());
	}
}



class MySqlIterator
{
	
	var $_resource_id;

	
	var $_current_row;

	
	var $_total_rows;

	
	function MySqlIterator($resource_id)
	{
		$this->_resource_id = $resource_id;
		$this->_total_rows = 0;
		$this->_current_row = 0;
	}

	
	function GetNumRows()
	{
		$this->_total_rows = mysql_num_rows($this->GetResourceId());

		return $this->_total_rows;
	}

	function GetNumFields()
	{
		return mysql_num_fields($this->GetResourceId());
	}

	
	function GetResourceId()
	{
		return $this->_resource_id;
	}

	
	function GetCurrentRow()
	{
		return $this->_current_row;
	}

	
	function isSuccess()
	{
		return $this->GetResourceId() ? true : false;
	}

	
	function FreeResult()
	{
		mysql_free_result($this->GetResourceId());

		return;
	}

	
	function GetRow($result_type = 'assoc')
	{
		$this->_current_row++;

		switch($result_type)
		{
			case 'row':
				return mysql_fetch_row($this->GetResourceId());
				break;

			case 'assoc':
				return mysql_fetch_assoc($this->GetResourceId());
				break;

			case 'both':
				return mysql_fetch_array($this->GetResourceId());
				break;
			case 'object':
				return mysql_fetch_object($this->GetResourceId());
				break;
		}
	}
	function result($row)
	{
		return mysql_result($this->GetResourceId(),$row);
	}

	
	function GetAll($result_type = 'assoc')
	{
		$list = array();
		while($row = $this->GetRow($result_type))
		{
			$list[] = $row;
		}
		Return $list;
	}
}

?>