<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name isearcher_default.php
 * @date 2014-09-05 16:22:08
 */
 


$config['isearcher_default'] = array(
'idx' => array(
	'admin' => array(
		'product_list' => 'product_name,product_id,seller_name,city_name',
		'member_list' => 'username,phone,email',
		'order_list' => 'product_name,order_id,user_name',
		'coupon_list' => 'product_name,order_id,user_name,coupon_no',
		'delivery_list' => 'product_name,order_id,user_name',
        'recharge_card_list' => 'recharge_card_number',
        'recharge_order_list' => 'recharge_order_id,recharge_user_name',
		'cash_order_list' => 'cash_order_id,cash_user_name',
		'fund_order_list' => 'fund_order_id,fund_user_name,fund_seller_name'
	),
	'seller' => array(
		'product_list' => 'product_name',
		'product_order' => 'product_name,order_id,user_name',
		'product_ticket' => 'product_name,order_id,user_name,coupon_no',
		'product_delivery' => 'product_name,order_id,user_name'
	)
),
'frc' => array(
	'admin' => array(
		'order_list' => 'order_status,order_process',
		'coupon_list' => 'coupon_status',
        'recharge_card_list' => 'recharge_card_usetime',
        'product_list' => 'product_status,product_dsp_area',
        'recharge_order_list' => 'recharge_order_status',
		'cash_order_list' => 'cash_order_status',
		'fund_order_list' => 'fund_order_status',
		'member_list' => 'role_type',
		'delivery_list' => 'delivery_process'
	),
	'seller' => array(
		'product_list' => 'product_status,product_dsp_area',
		'product_order' => 'order_process',
		'product_ticket' => 'coupon_status',
		'product_delivery' => 'delivery_process'
	)
),
'tvs' => array(
	'admin' => array(
		'order_list' => 'order_main',
		'recharge_order_list' => 'recharge_order_main',
		'cash_order_list' => 'cash_order_main',
		'fund_order_list' => 'fund_order_main',
		'member_list' => 'member_main',
		'coupon_list' => 'coupon_main',
	),
	'seller' => array(
		'product_order' => 'seller_order',
		'product_ticket' => 'seller_ticket'
	)
),
'map' => array(
	'product_id' => array(
        'name' => '产品ID',
        'table' => 'product',
        'src' => 'id',
        'key' => 'pid',
        'idx' => 'id'
    ),
	'product_name' => array(
        'name' => '产品名称',
        'table' => 'product',
        'src' => array('flag','name','intro'),
        'key' => 'pid',
        'idx' => 'id'
    ),
	'seller_name' => array(
        'name' => '商家名称',
        'table' => 'seller',
        'src' => 'sellername',
        'key' => 'sid',
        'idx' => 'id'
    ),
	'city_name' => array(
        'name' => '城市名称',
        'table' => 'city',
        'src' => 'cityname',
        'key' => 'cid',
        'idx' => 'cityid'
    ),
	'order_id' => array(
		'name' => '订单编号',
		'table' => 'order',
		'src' => 'orderid',
		'key' => 'oid',
		'idx' => 'orderid'
	),
	'user_name' => array(
		'name' => '用户名',
		'table' => 'members',
		'src' => 'username',
		'key' => 'uid',
		'idx' => 'uid'
	),
	'username' => array(
		'name' => '用户名',
		'table' => 'members',
		'src' => 'username',
		'key' => 'uid',
		'idx' => 'uid'
	),
	'phone' => array(
		'name' => '手机号',
		'table' => 'members',
		'src' => 'phone',
		'key' => 'uid',
		'idx' => 'uid'
	),
	'email' => array(
		'name' => '邮箱',
		'table' => 'members',
		'src' => 'email',
		'key' => 'uid',
		'idx' => 'uid'
	),
	'coupon_no' => array(
		'name' => TUANGOU_STR . '券号码',
		'table' => 'ticket',
		'src' => 'number',
		'key' => 'coid',
		'idx' => 'ticketid'
 	),
    'recharge_card_number' => array(
        'name' => '充值卡号码',
        'table' => 'recharge_card',
        'src' => 'number',
        'key' => 'rcid',
        'idx' => 'id'
     ),
    'recharge_order_id' => array(
		'name' => '充值记录流水号',
		'table' => 'recharge_order',
		'src' => 'orderid',
		'key' => 'orderid',
		'idx' => 'orderid'
	),
	'recharge_user_name' => array(
		'name' => '用户名',
		'table' => 'members',
		'src' => 'username',
		'key' => 'userid',
		'idx' => 'uid'
	),
	'cash_order_id' => array(
		'name' => '提现记录流水号',
		'table' => 'cash_order',
		'src' => 'orderid',
		'key' => 'orderid',
		'idx' => 'orderid'
	),
	'cash_user_name' => array(
		'name' => '用户名',
		'table' => 'members',
		'src' => 'username',
		'key' => 'userid',
		'idx' => 'uid'
	),
	'fund_order_id' => array(
		'name' => '结算记录流水号',
		'table' => 'fund_order',
		'src' => 'orderid',
		'key' => 'orderid',
		'idx' => 'orderid'
	),
	'fund_user_name' => array(
		'name' => '用户名',
		'table' => 'members',
		'src' => 'username',
		'key' => 'userid',
		'idx' => 'uid'
	),
	'fund_seller_name' => array(
		'name' => '商家名称',
		'table' => 'seller',
		'src' => 'sellername',
		'key' => 'sellerid',
		'idx' => 'id'
	)
),
'filter' => array(
	'order_status' => array(
		'name' => '订单状态',
		'key' => 'ordsta',
		'list' => array(
			ORD_STA_Normal => '订单正常',
			ORD_STA_Cancel => '已经取消',
            ORD_STA_Failed => '订单失败',
            ORD_STA_Overdue => '已经过期',
            ORD_STA_Refund => '已经返款'
		)
	),
	'order_process' => array(
		'name' => '处理进程',
		'key' => 'ordproc',
		'list' => array(
            '__CREATE__' => '创建订单',
            '__PAY_YET__' => '已经付款',
            'WAIT_BUYER_PAY' => '等待付款',
            'WAIT_SELLER_SEND_GOODS' => '等待发货',
            'WAIT_BUYER_CONFIRM_GOODS' => '等待收货',
            'TRADE_FINISHED' => '交易完成'
		)
	),
	'coupon_status' => array(
		'name' => TUANGOU_STR . '券状态',
		'key' => 'coupsta',
		'list' => array(
			TICK_STA_Unused => '还未使用',
            TICK_STA_Used => '已经使用',
            TICK_STA_Overdue => '已经过期',
            TICK_STA_Invalid => '号码无效'
		)
	),
    'recharge_card_usetime' => array(
        'name' => '使用状态',
        'key' => 'used',
        'list' => array(
            0 => '还未使用',
            1 => '已经使用'
        )
    ),
    'product_status' => array(
        'name' => '产品状态',
        'key' => 'prosta',
        'list' => array(
            PRO_STA_Success => '进行中，已成团',
            PRO_STA_Normal => '进行中，未成团',
            PRO_STA_Finish => '已结束，' . TUANGOU_STR . '成功',
            PRO_STA_Failed => '已结束，' . TUANGOU_STR . '失败',
            PRO_STA_Refund => '已结束，已经返款'
        )
    ),
    'product_dsp_area' => array(
        'name' => '显示区域',
        'key' => 'prodsp',
        'list' => array(
            PRO_DSP_Global => '全部城市显示',
            PRO_DSP_City => '限定城市显示',
            PRO_DSP_None => '不在前台显示'
        )
    ),
    'recharge_order_status' => array(
        'name' => '支付状态',
        'key' => 'paystatus',
        'list' => array(
            0 => '还未支付',
            1 => '已经支付'
        )
    ),
	'cash_order_status' => array(
        'name' => '提现状态',
        'key' => 'paystatus',
        'list' => array(
            'no' => '等待处理',
            'yes' => '提现成功',
			'doing' => '正在处理',
			'error' => '提现失败'
        )
    ),
	'fund_order_status' => array(
        'name' => '结算状态',
        'key' => 'paystatus',
        'list' => array(
            'no' => '等待处理',
            'yes' => '结算成功',
			'doing' => '正在处理',
			'error' => '结算失败'
        )
    ),
	'role_type' => array(
        'name' => '身份类型',
        'key' => 'role_types',
        'list' => array(
            'normal' => '普通用户',
            'seller' => '合作商家',
			'admin' => '管理员'
        )
	),
	'delivery_process' => array(
		'name' => '交易状态',
		'key' => 'ordproc',
		'list' => array(
            'WAIT_SELLER_SEND_GOODS' => '等待发货',
            'WAIT_BUYER_CONFIRM_GOODS' => '等待收货',
            'TRADE_FINISHED' => '交易完成'
		)
	)
),
'timev' => array(
	'order_main' => array(
		array(
			'name' => '下单时间',
			'field' => 'buytime',
			'key' => 'ordbt'
		),
		array(
			'name' => '付款时间',
			'field' => 'paytime',
			'key' => 'ordpt'
		)
	),
	'recharge_order_main' => array(
		array(
			'name' => '支付时间',
			'field' => 'paytime',
			'key' => 'paytime'
		)
	),
	'fund_order_main' => array(
		array(
			'name' => '受理时间',
			'field' => 'paytime',
			'key' => 'paytime'
		)
	),
	'cash_order_main' => array(
		array(
			'name' => '受理时间',
			'field' => 'paytime',
			'key' => 'paytime'
		)
	),
	'member_main' => array(
		array(
			'name' => '注册时间',
			'field' => 'regdate',
			'key' => 'regdate'
		)
	),
	'seller_order' => array(
		array(
			'name' => '下单时间',
			'field' => 'buytime',
			'key' => 'ordbt'
		),
		array(
			'name' => '付款时间',
			'field' => 'paytime',
			'key' => 'ordpt'
		)
	),
	'seller_ticket' => array(
		array(
			'name' => '消费时间',
			'field' => 'usetime',
			'key' => 'usetime'
		)
	),
	'coupon_main' => array(
		array(
			'name' => '消费时间',
			'field' => 'usetime',
			'key' => 'usetime'
		)
	),
	'coupon_vlist' => array(
		array(
			'name' => '消费时间',
			'field' => 'usetime',
			'key' => 'usetime'
		)
	),
)
);
?>