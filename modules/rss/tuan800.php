<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name tuan800.php
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
	$item['data']['shops']['shop']= array();

	$o = array();
	$o['website'] = $si['site_name'];
	$o['identifier'] = $one['id'];
	$o['siteurl'] = $si['site_url'];
	$o['city'] = $city;
	$o['tag'] = $group;
	$o['title'] = $one['name'];
	$o['image'] = imager($one['img']);
	$o['startTime'] = date('Y-m-d H:i:s', $one['begintime']);
	$o['endTime'] = date('Y-m-d H:i:s', $one['overtime']);
	$o['value'] = $one['price'];
	$o['price'] = $one['nowprice'];
	$o['rebate'] = $one['discount'];
	$o['bought'] = $one['succ_buyers'];
	$o['maxQuota'] = $one['maxnum'];
	$o['minQuota'] = $one['maxnum'];
	$o['post'] = ($one['type'] == 'stuff') ? 'yes' : 'no';
	$o['soldOut'] = (($one['num'] > $one['maxnum']) && ($one['maxnum'] > 0)) ? 'yes' : 'no';
	$o['tip'] = $one['intro'];

	$item['data']['display'] = $o;

	$pval = array();
	$pval['name'] = $one['sellername'];
	$pval['tel'] = $one['sellerphone'];
	$pval['address'] = $one['selleraddress'];
	$item['data']['shops']['shop']=$pval;
	$oa[] = $item;
}

header('Content-Type: application/xml; charset=UTF-8');
if (ENC_IS_GBK) $oa = array_iconv('GBK', 'UTF-8', $oa);
Output::XmlBaidu($oa);
?>