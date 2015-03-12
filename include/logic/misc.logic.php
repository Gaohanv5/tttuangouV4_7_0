<?php

/**
 * 逻辑区：杂项
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package logic
 * @name misc.logic.php
 * @version 1.2
 */

class MiscLogic
{
	
	public function City($field = '', $cid = 0)
	{
				$cityAry = $this->CityList($cid);
				if ( $_GET['city'] != '' )
		{
			if(is_array($cityAry) && count($cityAry)) 
			{
				foreach ( $cityAry as $value )
				{
					if ( $value['shorthand'] == $_GET['city'] )
					{
						handler('cookie')->setVar('mycity', $value['cityid']);
						$cid = $value['cityid'];
						break;
					}
				}
			}
		}
				if ( $cid == '' )
		{
			if ( handler('cookie')->getVar('mycity') != '' )
			{
				$cid = handler('cookie')->getVar('mycity');
			}
			else
			{
				$cid = ini('product.default_city');
			}
		}
		if(is_array($cityAry) && count($cityAry)) 
		{
			foreach ($cityAry as $i => $city)
			{
				if ($city['cityid'] == $cid)
				{
					break;
				}
			}
		}
		$map = array(
			'id' => 'cityid',
			'name' => 'cityname',
			'flag' => 'shorthand'
		);
		if ($field == '')
		{
			return $city;
		}
		return $city[$map[$field]];
	}
	
	public function CityList($cid = 0, $showAll = false)
	{
				(int)$cid > 0 && $showAll = true;
		$ckey = 'misc.citylist.'.$cid;
		$list = cached($ckey);
		if ($list) return $list;
		$sql_limit_city = '1';
		if ($cid > 0)
		{
			$sql_limit_city = 'cityid = '.$cid;
		}
		$sql = 'SELECT * FROM '.table('city').' WHERE '.($showAll?'1':'display >= 1').' AND '.$sql_limit_city;
		return cached($ckey, dbc(DBCMax)->query($sql)->done());
	}
	
	public function AskList($limit = null)
	{
		$sql_limit = '';
		if (!is_null($limit))
		{
			$sql_limit = 'LIMIT '.$limit;
		}
		$ckey = 'misc.asklist'.$sql_limit;
		$list = cached($ckey);
		if ($list) return $list;
		$sql = 'SELECT * FROM '.table('question').' WHERE reply <> "" ORDER BY time DESC '.$sql_limit;
		$sql_limit != '' || $sql = page_moyo($sql);
		return dbc(DBCMax)->query($sql)->done();
	}
	
	public function RegionList($parent = 0)
	{
		$sql_limit = '1';
		if ($parent == 0)
		{
			$sql_limit = 'grade = 1';
		}
		else
		{
			$sql_limit = 'parent = '.$parent;
		}
		$sql = '
		SELECT
			*
		FROM
			'.table('regions').'
		WHERE
			'.$sql_limit.'
		';
		return dbc()->Query($sql)->GetAll();
	}
	
	public function rndString($length = 12, $mode = 6)
	{
		switch ($mode)
		{
			case '1':
				$str = '1234567890';
				break;
			case '2':
				$str = 'abcdefghijklmnopqrstuvwxyz';
				break;
			case '3':
				$str = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
				break;
			case '4':
				$str = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
				break;
			case '5':
				$str = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
				break;
			case '6':
				$str = 'abcdefghijklmnopqrstuvwxyz1234567890';
				break;
			default:
				$str = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890';
				break;
		}
		$randString = '';
		$len = strlen($str) - 1;
		for($i = 0; $i < $length; $i++)
		{
			$num = mt_rand(0, $len);
			$randString .= $str[$num];
		}
		return $randString;
	}
	
	public function siteInstalled()
	{
		return is_file(DATA_PATH.'install.lock');
	}
	
	public function alipay_check_xml_file()
	{
		$file = ROOT_PATH.'alipay.html';
		$isCheck = rand(9, 21);
		if (is_file($file) && $isCheck == 18)
		{
			return;
		}
		$version = SYS_VERSION;
		$xmlC = <<<XMLFILE
<?xml version="1.0" encoding="UTF-8" ?>
<alipay>
<merchant_pid>2088501217834340</merchant_pid>
<merchant_name>tttg</merchant_name>
<system_name>tt11</system_name>
<system_version>{$version}</system_version>
</alipay>
XMLFILE;
		handler('io')->WriteFile($file, $xmlC);
	}
}
?>