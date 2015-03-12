<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name index.php
 * @date 2014-09-01 17:24:23
 */
 

$si = array
(
	'name' => $this->Config['site_name'],
	'title' => $this->Config['site_name'],
	'url' => $this->Config['site_url']
);
$oa = array();
foreach($productList AS $one)
{
	$city = $cityList[$one['city']];
	$group = $city;
	$o = array();
	$o['pid'] = $one['id'];
	$o['url'] = "{$si['url']}/?view={$one['id']}";
	$o['image_large'] = imager($one['img']);
	$o['image_small'] = imager($one['img'], IMG_Small);
	$o['title'] = $one['name'];
	$o['intro'] = $one['intro'];
	$o['now_price'] = $one['nowprice'];
	$o['price'] = $one['price'];
	$o['discount'] = $one['discount'];
	$o['begin_date'] = date('Y-m-d H:i:s', $one['begintime']);
	$o['finish_date'] = date('Y-m-d H:i:s', $one['overtime']);
	$o['status'] = $one['status'];

	$co = array();
	$co['maximum'] = abs(intval($one['oncemax']));
	$co['deadline'] = date('Y-m-d H:i:s', $one['perioddate']);
	$o['conditions'] = $co;

	$o['city'] = $city;
	$oa[$one['id']] = $o;
}
$o = array( 'site' => $si, 'products' => $oa );
if (ENC_IS_GBK) $o = array_iconv('GBK', 'UTF-8', $o);
if ('json'===strtolower(strval($_GET['s']))) Output::Json($o);
header('Content-Type: application/xml; charset=UTF-8');
Output::Xml($o);

?>