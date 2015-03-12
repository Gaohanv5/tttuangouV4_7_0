<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name db.mod.php
 * @date 2014-12-11 14:44:49
 */
 



include_once(FUNCTION_PATH.'misc.func.php');
class ModuleObject extends MasterObject
{

	function ModuleObject($config)
	{
		$this->MasterObject($config);

		Load::moduleCode($this);$this->Execute();
	}

	function Execute()
	{

		switch($this->Code)
		{
			case 'export':
				$this->Export();
				break;
			case 'doexport':
				$this->DoExport();
				break;
			case 'import':
				$this->Import();
				break;
			case 'importzip':
				$this->DoImportZip();
				break;
			case 'doimport':
				$this->DoImport();
				break;
			case 'dodelete':
				$this->DoDelete();
				break;
			case 'optimize':
				$this->optimize();
				break;
			case 'dooptimize':
				$this->DoOptimize();
				break;
            case 'repair':
                $this->Repair();
                break;
			default:
				$this->Main();
				break;
		}
	}
	function Main()
	{
		exit('error');
	}

	function Import()
	{
		$this->CheckAdminPrivs('dbimport');
		$backupdir = $this->Get['backupdir'];
		if (!$backupdir) {
			$load = new Load();
			$load->lib('io');
			$IoHandler = new IoHandler();
			$_f_list = (array) $IoHandler->ReadDir(ROOT_PATH.'backup/db/'.$backupdir,1);
			$f_list = array();
			$key = 0;
			foreach ($_f_list as $_k=>$_f) {
				$ext = strtolower(trim(substr(strrchr($_f, '.'), 1, 10)));
				if(!in_array($ext,array('sql','zip',)) || 'tttuangou.sql'==basename($_f)) {
					unset($_f_list[$_k]);

					continue;
				}

				if(is_file($_f)) {
					$f_list[dirname($_f)] = 1;
				}
			}
			$_tmp_arr = (array_keys($f_list));
			$dateline_list = $dir_list = array();
			foreach ($_tmp_arr as $key=>$dir) {
				$timestamp = @filemtime($dir . './index.htm');
				$arr = array(
					'timestamp' => $timestamp,
					'dateline' => my_date_format($timestamp,'Y-m-d H:i:s'),
					'dir' => $dir,
					'backupdir' => ($backupdir = (substr($dir,9))) ? $backupdir : './',
				);
				$arr['backupdir_urlencode'] = urlencode($arr['backupdir']);
				if ($timestamp) {
					$dateline_list[$key] = (int) $timestamp;
				}

				$dir_list[$key] = $arr;
				@array_multisort($dateline_list,SORT_DESC,SORT_NUMERIC,$dir_list);
			}
		} else {
			$exportlog = array();
			if(is_dir(ROOT_PATH.'backup/'.$backupdir)) {
				$dateline_list = array();
				$key = 0;
				$dir = dir(ROOT_PATH.'backup/'.$backupdir);
				while($entry = $dir->read()) {
					$entry = ROOT_PATH.'backup/'.$backupdir.'/'.$entry;
					if(is_file($entry)) {
						if(preg_match("/\.sql$/i", $entry)) {
							$filesize = filesize($entry);
							$fp = fopen($entry, 'rb');
							$identify = explode(',', base64_decode(preg_replace("/^# Identify:\s*(\w+).*/s", "\\1", fgets($fp, 256))));
							fclose ($fp);
							$exportlog[$key] = array(
							'version' => $identify[1],
							'type' => $identify[2],
							'method' => $identify[3],
							'volume' => $identify[4],
							'filename' => $entry,
							'dateline' => filemtime($entry),
							'size' => $filesize
							);
						} elseif(preg_match("/\.zip$/i", $entry)) {
							$filesize = filesize($entry);
							$exportlog[$key] = array(
							'type' => 'zip',
							'filename' => $entry,
							'size' => filesize($entry),
							'dateline' => filemtime($entry)
							);
						}

						if($exportlog[$key]['dateline'])
						{
							$dateline_list[$key] = (int) $exportlog[$key]['dateline'];
						}
						$key++;
					}
				}
				$dir->close();
			} else {
				$this->Messager('database_export_dest_invalid');
			}
			@array_multisort($dateline_list,SORT_ASC,SORT_NUMERIC,$exportlog);

			$exportinfo = '';
			$exportinfo .= "<input type=hidden name=backupdir value='{$backupdir}' />";
			$type_list=array("all_tables"=>"全部数据","custom"=>"自定义备份",'zip'=>"压缩备份");
			$dateline_list = array();
			foreach($exportlog as $info) {
				$info['dateline'] = is_int($info['dateline']) ? my_date_format($info['dateline']) : "未知";
				$info['size'] = sizecount($info['size']);
				$info['volume'] = $info['method'] == 'multivol' ? $info['volume'] : '';
				$info['method'] = $info['type'] != 'zip' ? ($info['method'] == 'multivol' ? "多卷" : "Shell") : '';
				$exportinfo .= "<tr align=\"center\"><td class=\"altbg1\"><input class=\"checkbox\" type=\"checkbox\" name=\"delete[]\" value=\"".basename($info['filename'])."\"></td>\n".
				"<td class=\"altbg2\"><a href=\"$info[filename]\">".substr(strrchr($info['filename'], "/"), 1)."</a></td>\n".
				"<td class=\"altbg1\">$info[version]</td>\n".
				"<td class=\"altbg2\">$info[dateline]</td>\n".
				"<td class=\"altbg1\">".$type_list[$info['type']]."</td>\n".
				"<td class=\"altbg2\">$info[size]</td>\n".
				"<td class=\"altbg1\">$info[method]</td>\n".
				"<td class=\"altbg2\">$info[volume]</td>\n".
				($info['type'] == 'zip' ? "<td class=\"altbg1\"><a href=\"admin.php?mod=db&code=importzip&datafile_server=".urlencode($info[filename])."&importsubmit=yes\">[解压缩]</a></td>\n" :
				"<td class=\"altbg1\"><a href=\"admin.php?mod=db&code=doimport&from=server&datafile_server=".urlencode($info[filename])."&importsubmit=yes\"".
				($info['version'] != SYS_VERSION ? " onclick=\"return confirm('导入和当前 天天团购 版本不一致的数据极有可能产生无法解决的故障，您确定继续吗？');\"" : '').">[导入]</a></td>\n");
			}
		}

		include handler('template')->file('@admin/db_import');
	}

	function DoImport()
	{
		$this->CheckAdminPrivs('dbimport');
		extract($this->Post);
		extract($this->Get);
		$readerror = 0;
		$datafile = '';
		if($from == 'server') {
			$datafile = ROOT_PATH.'./'.$datafile_server;
		}
		$dbcharset = $this->DatabaseHandler->Charset;

		
		if(@$fp = fopen($datafile, 'rb')) {
			$sqldump = fgets($fp, 256);
			$identify = explode(',', base64_decode(preg_replace("/^# Identify:\s*(\w+).*/s", "\\1", $sqldump)));
			$dumpinfo = array('method' => $identify[3], 'volume' => intval($identify[4]));
			if($dumpinfo['method'] == 'multivol') {
				$sqldump .= fread($fp, filesize($datafile));
			}
			fclose($fp);
		} else {
			if($autoimport) {
				clearcache();
				$this->Messager('分卷数据成功导入数据库。',null);
			} else {
				$this->Messager('数据文件不存在: 可能服务器不允许上传文件或尺寸超过限制。',null);
			}
		}

		if($dumpinfo['method'] == 'multivol') {
			$sqlquery = splitsql($sqldump);
			unset($sqldump);
			$supetablepredot = strpos($supe['tablepre'], '.');
			$supe['dbname'] =  $supetablepredot !== FALSE ? substr($supe['tablepre'], 0, $supetablepredot) : '';

			foreach($sqlquery as $sql) {

				$sql = syntablestruct(trim($sql), $this->DatabaseHandler->GetVersion() > '4.1', $dbcharset);

				if(substr($sql, 0, 11) == 'INSERT INTO') {
					$sqldbname = substr($sql, 12, 20);
					$dotpos = strpos($sqldbname, '.');
					if($dotpos !== FALSE) {
						if(empty($supe['dbmode'])) {
							$sql = 'INSERT INTO `'.$supe['dbname'].'`.'.substr($sql, 13 + $dotpos);
						} else {
													}
					}
				}

				if($sql != '') {
					$this->DatabaseHandler->Query($sql, 'SKIP_ERROR');
					if(($sqlerror = $this->DatabaseHandler->GetLastErrorString()) && $this->DatabaseHandler->GetLastErrorNo() != 1062) {
						die('MySQL Query Error'.$sql);
					}
				}
			}

			if($delunzip) {
				@unlink($datafile_server);
			}

			$datafile_next = preg_replace("/-($dumpinfo[volume])(\..+)$/", "-".($dumpinfo['volume'] + 1)."\\2", $datafile_server);

			if($dumpinfo['volume'] == 1) {
				$to="admin.php?mod=db&code=doimport&from=server&datafile_server=".urlencode($datafile_next)."&autoimport=yes&importsubmit=yes".(!empty($delunzip) ? '&delunzip=yes' : '');
				$msg='            <form method="post" action="'.$to.'">
                    <br /><br /><br />分卷数据成功导入数据库，您需要自动导入本次其它的备份吗？<br /><br /><br /><br />
                    <input type="hidden" name="FORMHASH" value="'.FORMHASH.'"> &nbsp;
                    <input class="button" type="submit" name="confirmed" value=" 确 定 "> &nbsp;
                    <input class="button" type="button" value=" 取 消 " onClick="history.go(-1);">
                  </form><br />';
				$this->Messager($msg,null);
			} elseif($autoimport) {
				$this->Messager("数据文件 #{$dumpinfo['volume']} 成功导入，程序将自动继续。", "admin.php?mod=db&code=doimport&from=server&datafile_server=".urlencode($datafile_next)."&autoimport=yes&importsubmit=yes".(!empty($delunzip) ? '&delunzip=yes' : ''));
			} else {
				clearcache();
				$this->Messager('数据成功导入数据库。',null);
			}
		} elseif($dumpinfo['method'] == 'shell') {
			require './config.inc.php';
			list($dbhost, $dbport) = explode(':', $dbhost);

			$query = $this->DatabaseHandler->Query("SHOW VARIABLES LIKE 'basedir'");
			list(, $mysql_base) = $db->fetch_array($query, MYSQL_NUM);

			$mysqlbin = $mysql_base == '/' ? '' : addslashes($mysql_base).'bin/';
			shell_exec($mysqlbin.'mysql -h"'.$dbhost.($dbport ? (is_numeric($dbport) ? ' -P'.$dbport : ' -S"'.$dbport.'"') : '').
			'" -u"'.$dbuser.'" -p"'.$dbpw.'" "'.$dbname.'" < '.$datafile);

			clearcache();
			$this->Messager('数据成功导入数据库。',null);
		} else {
			$this->Messager('数据文件非 天天团购 格式，无法导入。');
		}

	}

	function DoImportZip()
	{
		$this->CheckAdminPrivs('dbimport');
		extract($this->Post);
		extract($this->Get);
		require_once FUNCTION_PATH.'zip.func.php';
		$unzip = new SimpleUnzip();
		$unzip->ReadFile($datafile_server);

		if($unzip->Count() == 0 || $unzip->GetError(0) != 0 || !preg_match("/\.sql$/i", $importfile = $unzip->GetName(0))) {
			$this->Messager('数据文件不存在: 可能服务器不允许上传文件或尺寸超过限制。',null);
		}

		$identify = explode(',', base64_decode(preg_replace("/^# Identify:\s*(\w+).*/s", "\\1", substr($unzip->GetData(0), 0, 256))));
		$confirm = !empty($confirm) ? 1 : 0;
		if(!$confirm && $identify[1] !=SYS_VERSION) {
			$to="admin.php?mod=db&code=importzip&datafile_server=".urlencode($datafile_server)."&importsubmit=yes&confirm=yes";
			$msg=' <form method="post" action="'.$to.'">
                    <br /><br /><br />导入和当前程序版本不一致的数据极有可能产生无法解决的故障，您确定继续吗？<br /><br /><br /><br />
                    <input type="hidden" name="FORMHASH" value="'.FORMHASH.'"> &nbsp;
                    <input class="button" type="submit" name="confirmed" value=" 确 定 "> &nbsp;
                    <input class="button" type="button" value=" 取 消 " onClick="history.go(-1);">
                  </form><br />';
			$this->Messager($msg,null);
		}

		$sqlfilecount = 0;
		foreach($unzip->Entries as $entry) {
			if(preg_match("/\.sql$/i", $entry->Name)) {
				$fp = fopen('./backup/'.$backupdir.'/'.$entry->Name, 'w');
				fwrite($fp, $entry->Data);
				fclose($fp);
				$sqlfilecount++;
			}
		}

		if(!$sqlfilecount) {
			$this->Messager('database_import_file_illegal');
		}
		$type_list=array("all_tables"=>"全部数据","custom"=>"自定义备份",'zip'=>"压缩备份");
		$info = basename($datafile_server).'<br />'.'版本'.': '.$identify[1].'<br />'.'类型'.': '.$type_list[$identify[2]].'<br />'.'方式'.': '.($identify[3] == 'multivol' ? "多卷" : "SHELL").'<br />';

		if(isset($multivol)) {
			$multivol++;
			$datafile_server = preg_replace("/-(\d+)(\..+)$/", "-$multivol\\2", $datafile_server);
			if(is_file($datafile_server)) {
				$this->Messager("数据文件 #$multivol 成功解压缩，程序将自动继续。", 'admin.php?mod=db&code=importzip&multivol='.$multivol.'&datafile_vol1='.$datafile_vol1.'&datafile_server='.urlencode($datafile_server).'&importsubmit=yes&confirm=yes');
			} else {
				$to='admin.php?mod=db&code=doimport&from=server&datafile_server='.urlencode($datafile_vol1).'&importsubmit=yes&delunzip=yes';
				$msg=' <form method="post" action="'.$to.'">
		                    <br /><br /><br />所有分卷文件解压缩完毕，您需要自动导入备份吗？导入后解压缩的文件将会被删除。<br /><br /><br /><br />
		                    <input type="hidden" name="FORMHASH" value="'.FORMHASH.'"> &nbsp;
		                    <input class="button" type="submit" name="confirmed" value=" 确 定 "> &nbsp;
		                    <input class="button" type="button" value=" 取 消 " onClick="location.href=\'admin.php?mod=db&code=import\';">
		                  </form><br />';

				$this->Messager($msg,null);
			}
		}

		if($identify[3] == 'multivol' && $identify[4] == 1 && preg_match("/-1(\..+)$/", $datafile_server)) {
			$datafile_vol1 = $datafile_server;
			$datafile_server = preg_replace("/-1(\..+)$/", "-2\\1", $datafile_server);
			if(is_file($datafile_server)) {
				$to='admin.php?mod=db&code=importzip&multivol=1&datafile_vol1=./backup/'.$backupdir.'/'.$importfile.'&datafile_server='.urlencode($datafile_server).'&importsubmit=yes&confirm=yes';
				$msg=' <form method="post" action="'.$to.'">
		                    '.$info.'<br />备份文件解压缩完毕，您需要自动解压缩其它的分卷文件吗？<br /><br /><br /><br />
		                    <input type="hidden" name="FORMHASH" value="'.FORMHASH.'"> &nbsp;
		                    <input class="button" type="submit" name="confirmed" value=" 确 定 "> &nbsp;
		                    <input class="button" type="button" value=" 取 消 " onClick="history.go(-1);">
		                  </form><br />';
				$this->Messager($msg, null);
			}
		}
		$to='admin.php?mod=db&code=doimport&from=server&datafile_server=./backup/'.$backupdir.'/'.$importfile.'&importsubmit=yes&delunzip=yes';
		$msg=' <form method="post" action="'.$to.'">
                    <br /><br /><br />所有分卷文件解压缩完毕，您需要自动导入备份吗？导入后解压缩的文件将会被删除。<br /><br /><br /><br />
                    <input type="hidden" name="FORMHASH" value="'.FORMHASH.'"> &nbsp;
                    <input class="button" type="submit" name="confirmed" value=" 确 定 "> &nbsp;
                    <input class="button" type="button" value=" 取 消 " onClick="location.href=\"admin.php?mod=db&code=import\";">
                  </form><br />';

		$this->Messager($msg,null);
	}

	function DoDelete()
	{
		$this->CheckAdminPrivs('dbexport');
		$backupdir = $this->Post['backupdir'];
		$delete=$this->Post['delete'];
		if(is_array($delete)) {
			$dir = ROOT_PATH.'backup/'.$backupdir.'/';
			foreach($delete as $filename) {
				@unlink($dir.str_replace(array('/', '\\'), '', $filename));
			}

			
			if ($backupdir && false!==strpos($dir,'/db/')) {
				$load = new Load();
				$load->lib('io');
				$IoHandler = new IoHandler();
				$f_list = $IoHandler->ReadDir($dir,1);
				if(count($f_list) < 3) {

					$d = true;
					foreach ($f_list as $f) {
						if ((filesize($f) > 0) || ((basename($f) != 'index.htm') && (basename($f) != 'index.html'))) {
							$d = false;
							break;
						}
					}

					if ($d) {
						$IoHandler->RemoveDir($dir);
					}
				}
			}

			$this->Messager('指定备份文件成功删除',null);
		} else {
			$this->Messager('您没有选择要删除的备份文件，请返回');
		}
	}

	function optimize()
	{
		$this->CheckAdminPrivs('dboptimize');
		$tabletype = $this->DatabaseHandler->GetVersion() > '4.1' ? 'Engine' : 'Type';
		$optimizetable = '';
		$totalsize = 0;
		$tablearray = array( 0 =>TABLE_PREFIX) ;
		$table_string="";
		foreach($tablearray as $tp) {
			$query = $this->DatabaseHandler->Query("SHOW TABLE STATUS LIKE '$tp%'", 'SKIP_ERROR');
			while($table = $query->GetRow()) {
				if($table['Data_free'] && $table[$tabletype] == 'MyISAM') {
					$checked = $table[$tabletype] == 'MyISAM' ? 'checked' : 'disabled';
					$table_string.= "<tr onmouseover=\"this.className='tr_hover'\" onmouseout=\"this.className='tr_normal'\"><td align=\"center\"><input class=\"checkbox\" type=\"checkbox\" name=\"optimizetables[]\" value=\"$table[Name]\" $checked></td>\n".
					"<td align=\"center\">$table[Name]</td>\n".
					"<td align=\"center\">".$table[$tabletype]."</td>\n".
					"<td align=\"center\">$table[Rows]</td>\n".
					"<td align=\"center\">$table[Data_length]</td>\n".
					"<td align=\"center\">$table[Index_length]</td>\n".
					"<td align=\"center\">$table[Data_free]</td></tr>\n";
					$totalsize += $table['Data_length'] + $table['Index_length'];
				}
			}
		}
		if(empty($totalsize)) {
			$table_string.= "<tr><td colspan=\"7\" align=\"right\">数据表没有碎片，不需要再优化。</td></tr></table></div>";
		} else {
			$table_string.="<tr><td colspan=\"7\" align=\"right\">尺寸 ".sizecount($totalsize)."</td></tr></table></div><br /><center><input class=\"button\" type=\"submit\" name=\"optimizesubmit\" value=\"提交\"></center>";
		}

		include handler('template')->file('@admin/db_optimize');
	}
	function DoOptimize()
	{
		$this->CheckAdminPrivs('dboptimize');
		extract($this->Post);
		$optimizetable = '';
		$totalsize = 0;
		$tablearray = array( 0 =>TABLE_PREFIX) ;
		$table_string="";
		foreach($tablearray as $tp) {
			$query = $this->DatabaseHandler->Query("SHOW TABLE STATUS LIKE '$tp%'", 'SKIP_ERROR');
			while($table = $query->GetRow()) {
				if(is_array($optimizetables) && in_array($table['Name'], $optimizetables)) {
					$this->DatabaseHandler->Query("OPTIMIZE TABLE $table[Name]");
					$this->DatabaseHandler->Query("REPAIR TABLE $table[Name]");
				}

				$table_string.= "<tr onmouseover=\"this.className='tr_hover'\" onmouseout=\"this.className='tr_normal'\">\n".
				"<td align=\"center\">是</td>\n".
				"<td align=\"center\">$table[Name]</td>\n".
				"<td align=\"center\">".($this->DatabaseHandler->GetVersion() > '4.1' ?  $table['Engine'] : $table['Type'])."</td>\n".
				"<td align=\"center\">$table[Rows]</td>\n".
				"<td align=\"center\">$table[Data_length]</td>\n".
				"<td align=\"center\">$table[Index_length]</td>\n".
				"<td align=\"center\">0</td>\n".
				"</tr>\n";
				$totalsize += $table['Data_length'] + $table['Index_length'];
			}
		}
		$table_string.= "<tr><td colspan=\"7\" align=\"right\">尺寸  ".sizecount($totalsize)."</td></tr></table>";

		include handler('template')->file('@admin/db_optimize');
	}

	function Export()
	{
		$this->CheckAdminPrivs('dbexport');
		$filename=my_date_format(time(),'YmdHi').'_'.random(8);
		$shelldisabled = function_exists('shell_exec') ? '' : 'disabled';
		$table_list = $this->_fetch_table_list(TABLE_PREFIX);
		$table_list_group=array_chunk($table_list,4);
		
		include handler('template')->file('@admin/db_export');
	}
	function DoExport()
	{
		$this->CheckAdminPrivs('dbexport');
		global $sizelimit, $startrow, $extendins, $sqlcompat, $sqlcharset, $dumpcharset, $usehex, $complete, $excepttables;
		extract($this->Post);extract($this->Get);

		$excepttables=array(TABLE_PREFIX."sessions",);

		$time=$timestamp=time();
		$tablepre=TABLE_PREFIX;

		$this->DatabaseHandler->Query('SET SQL_QUOTE_SHOW_CREATE=1', 'SKIP_ERROR');
		if(!$filename || preg_match("/(\.)(exe|jsp|asp|aspx|cgi|fcgi|pl)(\.|$)/i", $filename))
		{
			$this->Messager("备份文件名无效");
		}

				if($type == 'all_tables') {
			$tables = $this->_array_keys2($this->_fetch_table_list($tablepre), 'Name');
		}
		elseif($type == 'custom')
		{
			$tables = array();
			if(empty($setup))
			{
				$tables=cache("tables",-1,true);
			}
			else
			{
				cache('tables',-1);
				cache($customtables);
				$tables = & $customtables;
			}
			if( !is_array($tables) || empty($tables))
			{
				$this->Messager("没有要导出的数据表");
			}
		}


		$volume = intval($volume) + 1;
		$idstring = '# Identify: '.base64_encode("$timestamp,".SYS_VERSION.",$type,$method,$volume")."\n";


		$dumpcharset = $sqlcharset ? $sqlcharset : str_replace('-', '', $this->Config['charset']);
		$setnames = ($sqlcharset && $this->DatabaseHandler->GetVersion() > '4.1' && (!$sqlcompat || $sqlcompat == 'MYSQL41')) ? "SET NAMES '$dumpcharset';\n\n" : '';
		if($this->DatabaseHandler->GetVersion() > '4.1') {
			if($sqlcharset) {
				$this->DatabaseHandler->Query("SET NAMES '".$sqlcharset."';\n\n");
			}
			if($sqlcompat == 'MYSQL40') {
				$this->DatabaseHandler->Query("SET SQL_MODE='MYSQL40'");
			} elseif($sqlcompat == 'MYSQL41') {
				$this->DatabaseHandler->Query("SET SQL_MODE=''");
			}
		}

		
		$backupdir = 'db/' . ($f = str_replace(array('/', '\\', '.'), '', $filename));
		$backupfilename = './backup/'.$backupdir.'/'.$f;
		if (!is_dir(($d = dirname($backupfilename)))) {
			$load = new Load();
			$load->lib('io');
			$IoHandler = new IoHandler();
			$IoHandler->MakeDir($d);
		}
		

		if($usezip) {
			require_once FUNCTION_PATH.'zip.func.php';
		}

		if($method == 'multivol') {
			$sqldump = '';
			$tableid = intval($tableid);
			$startfrom = intval($startfrom);
			
			$complete = TRUE;

			for(; $complete && $tableid < count($tables) && strlen($sqldump) + 500 < $sizelimit * 1000; $tableid++) {
				$sqldump .= $this->_sql_dump_table($tables[$tableid], $startfrom, strlen($sqldump));
				if($complete) {
					$startfrom = 0;
				}
			}
			
			$dumpfile = $backupfilename."-%s".'.sql';
			!$complete && $tableid--;
			if(trim($sqldump)) {
				$sqldump = "$idstring".
				"# <?exit();?>\n".
				"# TTTuangou Multi-Volume Data Dump Vol.$volume\n".
				"# Version: TTTuangou ".SYS_VERSION."\n".
				"# Time: $time\n".
				"# Type: $type\n".
				"# Table Prefix: $tablepre\n".
				"#\n".
				"# TTTuangou Home: http:\/\/www.tttuangou.net\n".
				"# Please visit our website for newest infomation about TTTuangou\n".
				"# --------------------------------------------------------\n\n\n".
				"$setnames".
				$sqldump;
				$dumpfilename = sprintf($dumpfile, $volume);
				@$fp = fopen($dumpfilename, 'wb');
				@flock($fp, 2);
				if(@!fwrite($fp, $sqldump)) {
					@fclose($fp);
					$this->Messager("备份文件名有问题");
				} else {
					fclose($fp);
					if($usezip == 2) {
						$fp = fopen($dumpfilename, "r");
						$content = @fread($fp, filesize($dumpfilename));
						fclose($fp);
						$zip = new zipfile();
						$zip->addFile($content, basename($dumpfilename));
						$fp = fopen(sprintf($backupfilename."-%s".'.zip', $volume), 'w');
						if(@fwrite($fp, $zip->file()) !== FALSE) {
							@unlink($dumpfilename);
						}
						fclose($fp);
					}
					unset($sqldump, $zip, $content);
					$this->Messager("分卷备份: 数据文件 #{$volume} 成功创建，程序将自动继续。
", "admin.php?mod=db&code=doexport&type=".rawurlencode($type)."&saveto=server&filename=".rawurlencode($filename)."&method=multivol&sizelimit=".rawurlencode($sizelimit)."&volume=".rawurlencode($volume)."&tableid=".rawurlencode($tableid)."&startfrom=".rawurlencode($startrow)."&extendins=".rawurlencode($extendins)."&sqlcharset=".rawurlencode($sqlcharset)."&sqlcompat=".rawurlencode($sqlcompat)."&exportsubmit=yes&usehex=$usehex&usezip=$usezip");

				}
			} else {
				$volume--;
				$filelist = '<ul>';

				if($usezip == 1) {
					$zip = new zipfile();
					$zipfilename = $backupfilename.'.zip';
					$unlinks = '';
					for($i = 1; $i <= $volume; $i++) {
						$filename = sprintf($dumpfile, $i);
						$fp = fopen($filename, "r");
						$content = @fread($fp, filesize($filename));
						fclose($fp);
						$zip->addFile($content, basename($filename));
						$unlinks .= "@unlink('$filename');";
						$filelist .= "<li><a href=\"$filename\">$filename\n";
					}
					$fp = fopen($zipfilename, 'w');
					if(@fwrite($fp, $zip->file()) !== FALSE) {
						eval($unlinks);
					} else {
						$this->Messager('database_export_multivol_succeed');
					}
					unset($sqldump, $zip, $content);
					fclose($fp);
					@touch('./backup/'.$backupdir.'/index.htm');
					$filename = $zipfilename;
					$this->Messager("数据成功备份并压缩至服务器 <a href=\"$filename\">$filename</a> 中。",null);
				} else {
					@touch('./backup/'.$backupdir.'/index.htm');
					for($i = 1; $i <= $volume; $i++) {
						$filename = sprintf($usezip == 2 ? $backupfilename."-%s".'.zip' : $dumpfile, $i);
						$filelist .= "<li><a href=\"$filename\">$filename\n";
					}
					$this->Messager("恭喜您，全部 $volume 个备份文件成功创建，备份完成。
".$filelist ,null);
				}
			}

		} else {

			$tablesstr = '';
			foreach($tables as $table) {
				$tablesstr .= '"'.$table.'" ';
			}

			require './config.inc.php';
			list($dbhost, $dbport) = explode(':', $dbhost);

			$query = $this->DatabaseHandler->Query("SHOW VARIABLES LIKE 'basedir'");
			list(, $mysql_base) = $db->fetch_array($query, MYSQL_NUM);

			$dumpfile = addslashes(dirname(dirname(__FILE__))).'/'.$backupfilename.'.sql';
			@unlink($dumpfile);

			$mysqlbin = $mysql_base == '/' ? '' : addslashes($mysql_base).'bin/';
			@shell_exec($mysqlbin.'mysqldump --force --quick '.($this->DatabaseHandler->GetVersion() > '4.1' ? '--skip-opt --create-options' : '-all').' --add-drop-table'.($extendins == 1 ? ' --extended-insert' : '').''.($this->DatabaseHandler->GetVersion() > '4.1' && $sqlcompat == 'MYSQL40' ? ' --compatible=mysql40' : '').' --host="'.$dbhost.($dbport ? (is_numeric($dbport) ? ' --port='.$dbport : ' --socket="'.$dbport.'"') : '').'" --user="'.$dbuser.'" --password="'.$dbpw.'" "'.$dbname.'" '.$tablesstr.' > '.$dumpfile);

			if(@is_file($dumpfile)) {

				if($usezip) {
					require_once FUNCTION_PATH.'zip.func.php';
					$zip = new zipfile();
					$zipfilename = $backupfilename.'.zip';
					$fp = fopen($dumpfile, "r");
					$content = @fread($fp, filesize($dumpfile));
					fclose($fp);
					$zip->addFile($idstring."# <?exit();?>\n ".$setnames."\n #".$content, basename($dumpfile));
					$fp = fopen($zipfilename, 'w');
					@fwrite($fp, $zip->file());
					fclose($fp);
					@unlink($dumpfile);
					@touch('./backup/'.$backupdir.'/index.htm');
					$filename = $backupfilename.'.zip';
					unset($sqldump, $zip, $content);
					$this->Messager('database_export_zip_succeed');
				} else {
					if(@is_writeable($dumpfile)) {
						$fp = fopen($dumpfile, 'rb+');
						@fwrite($fp, $idstring."# <?exit();?>\n ".$setnames."\n #");
						fclose($fp);
					}
					@touch('./backup/'.$backupdir.'/index.htm');
					$filename = $backupfilename.'.sql';
					$this->Messager('database_export_succeed');
				}

			} else {

				$this->Messager('database_shell_fail');

			}

		}
	}


	function _fetch_table_list($tablepre = '')
	{
		$arr = explode('.', $tablepre);
		$dbname = $arr[1] ? $arr[0] : '';
		$sqladd = $dbname ? " FROM $dbname LIKE '$arr[1]%'" : "LIKE '$tablepre%'";
		!$tablepre && $tablepre = '*';
		$tables = $table = array();
		$query = $this->DatabaseHandler->query("SHOW TABLE STATUS $sqladd");
		while($table = $query->GetRow()) {
			$table['Name'] = ($dbname ? "$dbname." : '').$table['Name'];
			$tables[] = $table;
		}
		return $tables;
	}
	function _array_keys2($array, $key2) {
		$return = array();
		foreach($array as $val) {
			$return[] = $val[$key2];
		}
		return $return;
	}
	function _sql_dump_table($table, $startfrom = 0, $currsize = 0) {
		global $sizelimit, $startrow, $extendins, $sqlcompat, $sqlcharset, $dumpcharset, $usehex, $complete, $excepttables;

		$offset = 300;
		$tabledump = '';
		$tablefields = array();

		$query = $this->DatabaseHandler->Query("SHOW FULL COLUMNS FROM $table", 'SKIP_ERROR');
		if(strexists($table, 'adminsessions')) {
			return ;
		} elseif(!$query && $this->DatabaseHandler->GetLastErrorNo() == 1146) {
			return;
		} elseif(!$query) {
			$usehex = FALSE;
		} else {
			while($fieldrow = $query->GetRow()) {
				$tablefields[] = $fieldrow;
			}
		}
		if(!$startfrom) {

			$createtable = $this->DatabaseHandler->Query("SHOW CREATE TABLE $table", 'SKIP_ERROR');

			if(!$this->DatabaseHandler->GetLastErrorString()) {
				$tabledump = "DROP TABLE IF EXISTS $table;\n";
			} else {
				return '';
			}

			$create = $createtable->GetRow('row');

			if(strpos($table, '.') !== FALSE) {
				$tablename = substr($table, strpos($table, '.') + 1);
				$create[1] = str_replace("CREATE TABLE $tablename", 'CREATE TABLE '.$table, $create[1]);
			}
			$tabledump .= $create[1];

			if($sqlcompat == 'MYSQL41' && $this->DatabaseHandler->GetVersion() < '4.1') {
				$tabledump = preg_replace("/TYPE\=(.+)/", "ENGINE=\\1 DEFAULT CHARSET=".$dumpcharset, $tabledump);
			}
			if($this->DatabaseHandler->GetVersion() > '4.1' && $sqlcharset) {
				$tabledump = preg_replace("/(DEFAULT)*\s*CHARSET=.+/", "DEFAULT CHARSET=".$sqlcharset, $tabledump);
			}

			$query = $this->DatabaseHandler->Query("SHOW TABLE STATUS LIKE '$table'");
			$tablestatus = $query->GetRow();
			$tabledump .= ($tablestatus['Auto_increment'] ? " AUTO_INCREMENT=$tablestatus[Auto_increment]" : '').";\n\n";
			if($sqlcompat == 'MYSQL40' && $this->DatabaseHandler->GetVersion() >= '4.1' && $this->DatabaseHandler->GetVersion() < '5.1') {
				if($tablestatus['Auto_increment'] <> '') {
					$temppos = strpos($tabledump, ',');
					$tabledump = substr($tabledump, 0, $temppos).' auto_increment'.substr($tabledump, $temppos);
				}
				if($tablestatus['Engine'] == 'MEMORY') {
					$tabledump = str_replace('TYPE=MEMORY', 'TYPE=HEAP', $tabledump);
				}
			}
		}

		if(!in_array($table, $excepttables)) {
			$tabledumped = 0;
			$numrows = $offset;
			$firstfield = $tablefields[0];
			if($extendins == '0') {
				while($currsize + strlen($tabledump) + 500 < $sizelimit * 1000 && $numrows == $offset) {
					if($firstfield['Extra'] == 'auto_increment') {
						$selectsql = "SELECT * FROM $table WHERE $firstfield[Field] > $startfrom LIMIT $offset";
					} else {
						$selectsql = "SELECT * FROM $table LIMIT $startfrom, $offset";
					}
					$tabledumped = 1;
					$rows = $this->DatabaseHandler->Query($selectsql);
					$numfields = $rows->GetNumFields();

					$numrows = $rows->GetNumRows();

					while($row = $rows->GetRow('row')) {
						$comma = $t = '';
						for($i = 0; $i < $numfields; $i++) {
							$t .= $comma.($usehex && !empty($row[$i]) && (strexists($tablefields[$i]['Type'], 'char') || strexists($tablefields[$i]['Type'], 'text')) ? '0x'.bin2hex($row[$i]) : '\''.mysql_real_escape_string($row[$i]).'\'');
							$comma = ',';
						}
						if(strlen($t) + $currsize + strlen($tabledump) + 500 < $sizelimit * 1000) {
							if($firstfield['Extra'] == 'auto_increment') {
								$startfrom = $row[0];
							} else {
								$startfrom++;
							}
							$tabledump .= "INSERT INTO $table VALUES ($t);\n";
						} else {
							$complete = FALSE;
							break 2;
						}
					}
									}
			} else {
				while($currsize + strlen($tabledump) + 500 < $sizelimit * 1000 && $numrows == $offset) {
					if($firstfield['Extra'] == 'auto_increment') {
						$selectsql = "SELECT * FROM $table WHERE $firstfield[Field] > $startfrom LIMIT $offset";
					} else {
						$selectsql = "SELECT * FROM $table LIMIT $startfrom, $offset";
					}
					$tabledumped = 1;
					$rows = $this->DatabaseHandler->Query($selectsql);
					$numfields = $rows->GetNumFields();

					if($numrows = $rows->GetNumRows()) {
						$t1 = $comma1 = '';
						while($row = $rows->GetRow('row')) {
							$t2 = $comma2 = '';
							for($i = 0; $i < $numfields; $i++) {
								$t2 .= $comma2.($usehex && !empty($row[$i]) && (strexists($tablefields[$i]['Type'], 'char') || strexists($tablefields[$i]['Type'], 'text'))? '0x'.bin2hex($row[$i]) : '\''.mysql_real_escape_string($row[$i]).'\'');
								$comma2 = ',';
							}
							if(strlen($t1) + $currsize + strlen($tabledump) + 500 < $sizelimit * 1000) {
								if($firstfield['Extra'] == 'auto_increment') {
									$startfrom = $row[0];
								} else {
									$startfrom++;
								}
								$t1 .= "$comma1 ($t2)";
								$comma1 = ',';
							} else {
								$tabledump .= "INSERT INTO $table VALUES $t1;\n";
								$complete = FALSE;
								break 2;
							}
						}
						$tabledump .= "INSERT INTO $table VALUES $t1;\n";
					}
				}
			}

			$startrow = $startfrom;
			$tabledump .= "\n";
		}

		return $tabledump;
	}
    
    function Repair()
    {
        $this->CheckAdminPrivs('dbrepair');
		$op = get('op', 'txt');
        if (!$op)
        {
            include handler('template')->file('@admin/db_repair_guide');
            exit;
        }
        $func = 'Repair_'.$op;
        if (method_exists($this, $func))
        {
            $this->$func();
        }
        else
        {
            exit('NO function');
        }
    }
    function Repair_analyze()
    {
        $this->CheckAdminPrivs('dbrepair');
		list($cmpResult) = logic('db')->structAnalyze();
        include handler('template')->file('@admin/db_repair_detail');
    }
    function Repair_done()
    {
        $this->CheckAdminPrivs('dbrepair');
		$repair_result = logic('db')->structRepair();
        if ($repair_result)
        {
        	$this->Messager('数据结构修复完成！', '?mod=db&code=repair');
        }
        else
        {
        	$this->Messager('数据结构正常，无需修复！', '?mod=db&code=repair');
        }
    }
}

?>