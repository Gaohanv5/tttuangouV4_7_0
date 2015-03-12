<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name soso.php
 * @date 2014-09-01 17:24:23
 */
 

$si = array
(
	'site_name' => $this->Config['site_name'],
	'site_title' => $this->Config['site_name'],
	'site_url' => $this->Config['site_url']
);
ENC_IS_GBK && $si = array_iconv('GBK', 'UTF-8', $si);
$rmb = '元';
ENC_IS_GBK && $rmb = iconv('GBK', 'UTF-8/'.'/IGNORE', $rmb);
$oa = array();
$oa['provider'] = $si['site_name'];
$oa['version'] = '1.0';
$oa['dataServiceId'] = '1_1';

$itemList = array();
foreach ($productList as $i => $team)
{
    ENC_IS_GBK && $team = array_iconv('GBK', 'UTF-8', $team);
    ENC_IS_GBK && $city = iconv('GBK', 'UTF-8/'.'/IGNORE', $cityList[$team['city']]);
    $group = $city;
    $item = array();
    $item['keyword'] = "{$si['site_name']} {$team['name']}";
    $item['url'] = "{$si['site_url']}/?view={$team['id']}";
    $item['creator'] = $_SERVER['HTTP_HOST'];
    $item['title'] = "{$si['site_name']} {$team['name']}";
    $item['publishdate'] = date('Y-m-d', $team['begintime']);
    $item['imageaddress1'] = imager($team['img']);
    $item['imagealt1'] = $team['name'];
    $item['imagelink1'] = "{$si['site_url']}/?view={$team['id']}";
    $item['content1'] = $team['name'];
    $item['linktext1'] = $team['name'];
    $item['linktarget1'] = "{$si['site_url']}/?view={$team['id']}";
    $item['content2'] = "{$team['price']}{$rmb}";
    $item['content3'] = "{$team['nowprice']}{$rmb}";
    $item['content4'] = "{$team['discount']}{$rmb}";
    $item['content5'] = $group;
    $item['content6'] = $city;
    $item['content7'] = $team['num'];
    $item['linktext2'] = $si['site_name'];
    $item['linktarget2'] = $si['site_url'];
    $item['content8'] = date('Y-m-d H:i:s', $team['begintime']);
    $item['content9'] = date('Y-m-d H:i:s', $team['overtime']);
    $item['valid'] = '1';
    $itemList[] = $item;
}

$oa['datalist']['item'] = $itemList;

header('Content-Type: application/xml; charset=GBK');
Output::XmlCustom($oa, 'sdd', 'GBK');
?>