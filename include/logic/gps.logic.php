<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name gps.logic.php
 * @date 2014-10-30 10:42:13
 */
 




class GpsLogic
{
	
	public function product_linker($seller_id, $map)
	{
		list($longitude, $latitude, $level) = explode(',', $map);
		if ($longitude && $latitude)
		{
			dbc(DBCMax)->update('product')->where(array('sellerid' => $seller_id))->data(array('longitude' => (float)$longitude, 'latitude' => (float)$latitude))->done();
		}
	}
	
	public function seller_linker($seller_id, $product_id)
	{
		$seller = dbc(DBCMax)->select('seller')->where(array('id' => $seller_id))->limit(1)->done();
		if ($seller)
		{
			list($longitude, $latitude, $level) = explode(',', $seller['sellermap']);
			if ($longitude && $latitude)
			{
				dbc(DBCMax)->update('product')->where(array('id' => $product_id))->data(array('longitude' => (float)$longitude, 'latitude' => (float)$latitude))->done();
			}
		}
	}
	
	public function init()
	{
		$sellers = dbc(DBCMax)->select('seller')->done();
		foreach ($sellers as $seller)
		{
			list($longitude, $latitude, $level) = explode(',', $seller['sellermap']);
			if ($longitude && $latitude)
			{
				dbc(DBCMax)->update('product')->where(array('sellerid' => $seller['id']))->data(array('longitude' => (float)$longitude, 'latitude' => (float)$latitude))->done();
				dbc(DBCMax)->update('seller')->where(array('id' => $seller['id']))->data(array('longitude' => (float)$longitude, 'latitude' => (float)$latitude))->done();
			}
		}
	}
}

?>