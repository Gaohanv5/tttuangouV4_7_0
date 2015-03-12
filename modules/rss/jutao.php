<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name jutao.php
 * @date 2014-09-01 17:24:23
 */
 




$si = array
(
	'site_name' => $this->Config['site_name'],
	'site_title' => $this->Config['site_name'],
	'site_url' => $this->Config['site_url']
);
$oa = array();
foreach($productList AS $one) {
	$city = $cityList[$one['city']];
	$group = $city;
	$item = array();
	$item['loc'] = "{$si['site_url']}/?view={$one['id']}";
	$item['data'] = array();
	$item['data']['display'] = array();
	$item['data']['componays']['componay']= array();

	$o = array();
	$o['website'] = $si['site_name'];
	$o['siteurl'] = $si['site_url'];
	$o['city'] = $city;
	$o['title'] = $one['name'];
	$o['image'] = imager($one['img']);
	$o['soldout'] = ($one['surplus'] <= 0) ? 'yes' : 'no';
	$o['buyer'] = $one['succ_buyers'];
	$o['start_date'] = date('Y-m-d H:i:s', $one['begintime']);
	$o['end_date'] = date('Y-m-d H:i:s', $one['overtime']);
	$o['expire_date'] = 0;
	$o['oriprice'] = $one['price'];
	$o['curprice'] = $one['nowprice'];
	$o['discount'] = $one['discount'];
	$o['tip'] = $one['intro'];
	$o['detail'] = $one['content'];
	$item['data']['display'] = $o;
	$pval = array();
	$pval['name'] = $one['sellername'];
	$pval['contact'] = $one['sellerphone'];
	$pval['address'] = $one['selleraddress'];
	$item['data']['componays']['componay']=$pval;
	$oa[] = $item;
}

header('Content-Type: application/xml; charset=UTF-8');
if (ENC_IS_GBK) $oa = array_iconv('GBK', 'UTF-8', $oa);
Output::XmlBaidu($oa);
?>