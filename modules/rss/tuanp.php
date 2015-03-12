<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name tuanp.php
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

	$o = array();
	$o['<[cd]>website'] = $si['site_name'];
	$o['siteurl'] = $si['site_url'];
	$o['city'] = $city;
    $o['area'] = $city;
    $o['category'] = 1;
	$o['<[cd]>title'] = $one['name'];
    $o['identifier'] = $one['id'];
	$o['<[cd]>image'] = imager($one['img']);
	$o['start_time'] = $one['begintime'];
	$o['end_time'] = $one['overtime'];
    $o['expire_time'] = $one['overtime'];
	$o['value'] = $one['price'];
	$o['price'] = $one['nowprice'];
    $o['rebate'] = $one['discount'];
    $o['bought'] = $one['succ_buyers'];
    $o['max_limit'] = $one['maxnum'] > 0 ? $one['maxnum'] : '';
    $o['min_limit'] = $one['succ_total'];
    $o['is_post'] = $one['type'] == 'stuff' ? 'Y' : 'N';
    $o['sold_out'] = $one['surplus'] > 0 ? 'N' : 'Y';
    $o['priority'] = $one['order'];
    $o['tag'] = '';
    $o['<[cd]>tip'] = $one['intro'];
    $o['<[cd]>desc'] = $one['intro'];
	$item['data']['display'] = $o;
    $item['data']['shops']['shop'] = array(
        'dpid' => '',
        '<[cd]>name' => $one['sellername'],
        'tel' => $one['sellerphone'],
        '<[cd]>addr' => $one['selleraddress'],
        'longitude' => (float)$one['sellermap'][0],
        'latitude' => (float)$one['sellermap'][1]
    );
	$oa[] = $item;
}

header('Content-Type: application/xml; charset=UTF-8');
if (ENC_IS_GBK) $oa = array_iconv('GBK', 'UTF-8', $oa);
Output::XmlBaidu($oa, 0, ' xmlns="http://checkapi.jieshi.com/"  xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"');
?>