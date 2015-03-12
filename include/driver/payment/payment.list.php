<?php

/**
 * 支付方式列表
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package payment
 * @name payment.list.php
 * @version 1.0
 */

return array(
	'alipay' => array(
		'name' => '支付宝',
		'detail' => '【推荐】费率高（1.2%），但最多人使用的支付方式'
	),
    'alipaymobile' => array(
        'name' => '支付宝移动支付',
        'detail' => '【推荐】手机客户端快捷支付，仅限团购无线客户端使用'
    ),
	'self' => array(
		'name' => '余额支付',
		'detail' => '【推荐】使用本站账户余额进行支付'
	),
	'cod' => array(
		'name' => '货到付款',
		'detail' => '【不推荐】送货上门，当面付款'
	),
	'tenpay' => array(
		'name' => '财付通',
		'detail' => '【不推荐】费率高（1%），需购买年费套餐'
	),
	'bank' => array(
		'name' => '转账汇款',
		'detail' => '【不推荐】通过ATM机或银行转帐（周期长）'
	),
	'recharge' => array(
		'name' => '充值卡',
		'detail' => '【不推荐】本站自有充值卡充值（用于无网银区域）'
	),
	'chinabank' => array(
		'name' => '网银在线',
		'detail' => '【不推荐】费率高（0.9%），需购买年费套餐，提现周期长'
	),
	'bankdirect' => array(
		'name' => '网银直连即时到账',
		'detail' => '【推荐】费率超低（0.55%），个人企业团购都可用，提现周期短T+1'
	),
	'kuaibillmobile' => array(
		'name' => '快钱移动支付',
		'detail' => '【推荐】手机客户端快捷支付，仅限团购无线客户端使用'
	),
	
	'yeepay' => array(
		'name' => '易宝一键支付',
		'detail' => '【费率低】有卡就能付，无需开通网银'
	),
	'lianlianpay' => array(
		'name' => '银行卡快捷支付',
		'detail' => '【推荐】无需开通网银也可一键支付，开通门槛低、费率低，支持154家银行，一次开通同时支持web、客户端、wap'
	)
);

?>