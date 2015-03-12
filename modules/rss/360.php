<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name 360.php
 * @date 2014-09-01 17:24:23
 */
 

$data = array();
$data['website'] = $this->Config['site_name'];
$data['siteurl'] =  $this->Config['site_url'];
$data['items'] = array();$data['items'] = $productList;
$data['citys'] = $cityList;

$output = get_team($data);
header('Content-Type:text/xml;charset=utf-8');
header("Cache-Control: no-cache, must-revalidate"); if (ENC_IS_GBK) $output = iconv('GBK', 'UTF-8/'.'/IGNORE', $output);
echo $output;
unset($output); exit;

function get_team($team){
    if (!$team) return '';
	$xmlitems = '';
	foreach ($team['items'] as $k => $v)
	{
		$team['cityo']=  $team['citys'][$v['city']];
		$xmlitems .= get_items($team, $k);
	}    	$xmloutput = <<<XML
<?xml version="1.0" encoding="utf-8" ?>
<data>
    <site_name>{$team['website']}</site_name>
	<goodsdata>{$xmlitems}</goodsdata>
</data>
XML;
    unset($xmlitems);
	return $xmloutput;
}
function get_items($team, $key){
	if (!$team) return '';
	$rebate = $team['discount'];
	$imageurl = imager($team['items'][$key]['img']);
	$xmlitem = <<<XMLITEM

		<goods id="{$team['items'][$key]['id']}">
			<city_name>{$team['cityo']}</city_name>
			<site_url>{$team['siteurl']}</site_url>
			<title>{$team['items'][$key]['name']}</title>
			<goods_url>{$team['siteurl']}/?view={$team['items'][$key]['id']}</goods_url>
			<desc>{$team['items'][$key]['intro']}</desc>
			<class>精品购物</class>
			<img_url>{$imageurl}</img_url>
			<original_price>{$team['items'][$key]['price']}</original_price>
			<sale_price>{$team['items'][$key]['nowprice']}</sale_price>
			<sale_rate>{$rebate}</sale_rate>
			<sales_num>{$team['items'][$key]['num']}</sales_num>${ $bgtime = date('Y-m-d H:i:s', $team['items'][$key]['begintime'])}${ $edtime = date('Y-m-d H:i:s', $team['items'][$key]['overtime'])}
			<start_time>{$bgtime}</start_time>
			<close_time>{$edtime}</close_time>
			<merchant_name>{$team['items'][$key]['sellername']}</merchant_name>
			<merchant_tel>{$team['items'][$key]['sellerphone']}</merchant_tel>${ $sttime = date('Y-m-d H:i:s', $team['items'][$key]['begintime'])}${ $cltime = date('Y-m-d H:i:s', $team['items'][$key]['perioddate'])}
			<spend_start_time>{$sttime}</spend_start_time>
			<spend_close_time>{$cltime}</spend_close_time>
			<merchant_addr>{$team['items'][$key]['selleraddress']}</merchant_addr>
		</goods>

XMLITEM;
    return $xmlitem;
}
?>