<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name zip.moyo.php
 * @date 2014-09-01 17:24:22
 */
 




if (defined('zip.moyo.z2w'))
{
	return;
}

define('zip.moyo.z2w', true);

function zip2web($zip, $web)
{
	$z2w = loadInstance('zip.moyo.z2w', 'ExZIP2Web');
	$r = $z2w->Create_ZIP2Web($zip, $web);
	if (!zip2web_successd($web, $r))
	{
		$r = zip2web_Retry($zip, $web);
		if (!zip2web_successd($web, $r))
		{
			$r = zip2web_Retry2($zip, $web);
			if (!zip2web_successd($web, $r))
			{
				$r = array();
			}
		}
	}
		$cFile = $web.'setting/constants.php';
	if (!is_file($cFile))
	{
		return zip2web_error('无法释放临时文件！请检查目录“/data/”以及其子目录的权限是否为“可读写”');
	}
	$cContent = file_get_contents($cFile);
	if (!stristr($cContent, 'SYS_VERSION'))
	{
		return zip2web_error('升级文件解压失败！请手动进行升级 - <a href="'.ihelper('tg.upgrade.zip.error').'" target="_blank">查看帮助</a>');
	}
	return $r;
}

function zip2web_error($string)
{
	return array(
		'__extract_error__' => true,
		'__error_string__' => $string
	);
}

function zip2web_successd($dir, $rr)
{
	if (is_array($rr))
	{
		foreach ($rr as $rn => $B)
		{
			$file = $dir.$rn;
			if (is_file($file))
			{
				$fc = file_get_contents($file);
				$fcl = strlen($fc);
				$fc = str_replace(chr(0), '', $fc);
				if ($fcl && !$fc)
				{
					return false;
				}
			}
		}
		return true;
	}
	else
	{
		return false;
	}
}

function zip2web_Retry($zip, $web)
{
	if (!class_exists('PclZip'))
	{
		include ROOT_PATH.'include/function/zip.ex.pcl.php';
	}
	$zipC = new PclZip($zip);
	$exList = $zipC->extract($web);
	$wList = array();
	foreach ($exList as $i => $fileST)
	{
		$wList[$fileST['stored_filename']] = true;
	}
	return $wList;
}

function zip2web_Retry2($zip, $web)
{
	if (class_exists('ZipArchive', false))
	{
		$rr = false;
		$zh = new ZipArchive();
		$rs = $zh->open($zip);
		if ($rs)
		{
			$es = $zh->extractTo($web);
			if ($es)
			{
				for ($i = 0; $i < $zh->numFiles; $i ++)
				{
					$stat = $zh->statIndex($i);
					if ($stat)
					{
						$rr[$stat['name']] = true;
					}
				}
			}
		}
		$zh->close();
		return $rr;
	}
	else
	{
		return false;
	}
}

class ExZIP2Web
{
	var $total_files = 0;
	var $total_folders = 0;
	function Create_ZIP2Web ($zip, $web)
	{
		$r = $this->Extract($zip, $web);
		if (is_numeric($r) && $r < 0)
		{return false;}
		if (is_array($r) && count($r) == 0)
		{return false;}
		return $r;
	}
	function Extract ($zn, $to, $index = Array(-1))
	{
		$ok = 0;
		$zip = @fopen($zn, 'rb');
		if (! $zip) return (- 1);
		$cdir = $this->ReadCentralDir($zip, $zn);
		$pos_entry = $cdir['offset'];
		if (! is_array($index))
		{
			$index = array(
				$index
			);
		}
		for ($i = 0; $index[$i]; $i ++)
		{
			if (intval($index[$i]) != $index[$i] || $index[$i] > $cdir['entries']) return (- 1);
		}
		for ($i = 0; $i < $cdir['entries']; $i ++)
		{
			@fseek($zip, $pos_entry);
			$header = $this->ReadCentralFileHeaders($zip);
			$header['index'] = $i;
			$pos_entry = ftell($zip);
			@rewind($zip);
			fseek($zip, $header['offset']);
			if (in_array("-1", $index) || in_array($i, $index)) $stat[$header['filename']] = $this->ExtractFile(
			$header, $to, $zip);
		}
		fclose($zip);
		return $stat;
	}
	function ReadFileHeader ($zip)
	{
		$binary_data = fread($zip, 30);
		$data = unpack(
		'vchk/vid/vversion/vflag/vcompression/vmtime/vmdate/Vcrc/Vcompressed_size/Vsize/vfilename_len/vextra_len',
		$binary_data);
		$header['filename'] = fread($zip, $data['filename_len']);
		if ($data['extra_len'] != 0)
		{
			$header['extra'] = fread($zip, $data['extra_len']);
		}
		else
		{
			$header['extra'] = '';
		}
		$header['compression'] = $data['compression'];
		$header['size'] = $data['size'];
		$header['compressed_size'] = $data['compressed_size'];
		$header['crc'] = $data['crc'];
		$header['flag'] = $data['flag'];
		$header['mdate'] = $data['mdate'];
		$header['mtime'] = $data['mtime'];
		if ($header['mdate'] && $header['mtime'])
		{
			$hour = ($header['mtime'] & 0xF800) >> 11;
			$minute = ($header['mtime'] & 0x07E0) >> 5;
			$seconde = ($header['mtime'] & 0x001F) * 2;
			$year = (($header['mdate'] & 0xFE00) >> 9) + 1980;
			$month = ($header['mdate'] & 0x01E0) >> 5;
			$day = $header['mdate'] & 0x001F;
			$header['mtime'] = mktime($hour, $minute, $seconde, $month, $day,
			$year);
		}
		else
		{
			$header['mtime'] = time();
		}
		$header['stored_filename'] = $header['filename'];
		$header['status'] = "ok";
		return $header;
	}
	function ReadCentralFileHeaders ($zip)
	{
		$binary_data = fread($zip, 46);
		$header = unpack(
		'vchkid/vid/vversion/vversion_extracted/vflag/vcompression/vmtime/vmdate/Vcrc/Vcompressed_size/Vsize/vfilename_len/vextra_len/vcomment_len/vdisk/vinternal/Vexternal/Voffset',
		$binary_data);
		if ($header['filename_len'] != 0)
			$header['filename'] = fread($zip, $header['filename_len']);
		else $header['filename'] = '';
		if ($header['extra_len'] != 0)
			$header['extra'] = fread($zip, $header['extra_len']);
		else $header['extra'] = '';
		if ($header['comment_len'] != 0)
			$header['comment'] = fread($zip, $header['comment_len']);
		else $header['comment'] = '';
		if ($header['mdate'] && $header['mtime'])
		{
			$hour = ($header['mtime'] & 0xF800) >> 11;
			$minute = ($header['mtime'] & 0x07E0) >> 5;
			$seconde = ($header['mtime'] & 0x001F) * 2;
			$year = (($header['mdate'] & 0xFE00) >> 9) + 1980;
			$month = ($header['mdate'] & 0x01E0) >> 5;
			$day = $header['mdate'] & 0x001F;
			$header['mtime'] = mktime($hour, $minute, $seconde, $month, $day,
			$year);
		}
		else
		{
			$header['mtime'] = time();
		}
		$header['stored_filename'] = $header['filename'];
		$header['status'] = 'ok';
		if (substr($header['filename'], - 1) == '/') $header['external'] = 0x41FF0010;
		return $header;
	}
	function ReadCentralDir ($zip, $zip_name)
	{
		$size = filesize($zip_name);
		if ($size < 277)
			$maximum_size = $size;
		else $maximum_size = 277;
		@fseek($zip, $size - $maximum_size);
		$pos = ftell($zip);
		$bytes = 0x00000000;
		while ($pos < $size)
		{
			$byte = @fread($zip, 1);
			$bytes = ($bytes << 8) | ord($byte);
			if ($bytes == 0x504b0506 or $bytes == 0x2e706870504b0506)
			{
				$pos ++;
				break;
			}
			$pos ++;
		}
		$fdata = fread($zip, 18);
		$data = @unpack(
		'vdisk/vdisk_start/vdisk_entries/ventries/Vsize/Voffset/vcomment_size',
		$fdata);
		if ($data['comment_size'] != 0)
			$centd['comment'] = fread($zip, $data['comment_size']);
		else $centd['comment'] = '';
		$centd['entries'] = $data['entries'];
		$centd['disk_entries'] = $data['disk_entries'];
		$centd['offset'] = $data['offset'];
		$centd['disk_start'] = $data['disk_start'];
		$centd['size'] = $data['size'];
		$centd['disk'] = $data['disk'];
		return $centd;
	}
	function ExtractFile ($header, $to, $zip)
	{
		$header = $this->readfileheader($zip);
		if (substr($to, - 1) != "/") $to .= "/";
		if ($to == './') $to = '';
		$pth = explode("/", $to . $header['filename']);
		$pth[0] || $pth[0] = '/';
		$mydir = '';
		for ($i = 0; $i < count($pth) - 1; $i ++)
		{
			if (! $pth[$i]) continue;
			$mydir .= $pth[$i] . "/";
			if ((! is_dir($mydir) && @mkdir($mydir, 0777)) ||
			 (($mydir == $to . $header['filename'] ||
			 ($mydir == $to && $this->total_folders == 0)) && is_dir($mydir)))
			{
				@chmod($mydir, 0777);
				$this->total_folders ++;
			}
		}
		if (strrchr($header['filename'], '/') == '/') return;
		if (! ($header['external'] == 0x41FF0010) && ! ($header['external'] == 16))
		{
			if ($header['compression'] == 0)
			{
				$fp = @fopen($to . $header['filename'], 'wb');
				if (! $fp) return (- 1);
				$size = $header['compressed_size'];
				while ($size != 0)
				{
					$read_size = ($size < 2048 ? $size : 2048);
					$buffer = fread($zip, $read_size);
					$binary_data = pack('a' . $read_size, $buffer);
					@fwrite($fp, $binary_data, $read_size);
					$size -= $read_size;
				}
				fclose($fp);
				touch($to . $header['filename'], $header['mtime']);
			}
			else
			{
				$fp = @fopen($to . $header['filename'] . '.gz', 'wb');
				if (! $fp) return (- 1);
				$binary_data = pack('va1a1Va1a1', 0x8b1f,
				Chr($header['compression']), Chr(0x00), time(), Chr(0x00),
				Chr(3));
				fwrite($fp, $binary_data, 10);
				$size = $header['compressed_size'];
				while ($size != 0)
				{
					$read_size = ($size < 1024 ? $size : 1024);
					$buffer = fread($zip, $read_size);
					$binary_data = pack('a' . $read_size, $buffer);
					@fwrite($fp, $binary_data, $read_size);
					$size -= $read_size;
				}
				$binary_data = pack('VV', $header['crc'], $header['size']);
				fwrite($fp, $binary_data, 8);
				fclose($fp);
				$gzp = @gzopen($to . $header['filename'] . '.gz', 'rb') or
				 die("Cette archive est compress");
				if (! $gzp) return (- 2);
				$fp = @fopen($to . $header['filename'], 'wb');
				if (! $fp) return (- 1);
				$size = $header['size'];
				while ($size != 0)
				{
					$read_size = ($size < 2048 ? $size : 2048);
					$buffer = gzread($gzp, $read_size);
					$binary_data = pack('a' . $read_size, $buffer);
					@fwrite($fp, $binary_data, $read_size);
					$size -= $read_size;
				}
				fclose($fp);
				gzclose($gzp);
				touch($to . $header['filename'], $header['mtime']);
				@unlink($to . $header['filename'] . '.gz');
			}
		}
		$this->total_files ++;
		return true;
	}
}

?>