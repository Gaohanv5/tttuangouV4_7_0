<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name admin_left_menu.php
 * @date 2014-12-11 14:44:49
 */
 
 $menu_list = array (
  1 =>
  array (
	'title' => '常用操作',
	'link' => '',
	'sub_menu_list' =>
	array (
	),
  ),
  2 =>
  array (
	'title' => '全局设置',
	'link' => '',
	'sub_menu_list' =>
	array (
	  1 =>
	  array (
		'title' => '核心设置',
		'link' => 'admin.php?mod=setting&code=modify_normal',
		'shortcut' => false,
		'priv' => 'siteset'
	  ),
	  2 =>
	  array (
		'title' => '伪静态',
		'link' => 'admin.php?mod=setting&code=modify_rewrite',
		'shortcut' => false,
		'priv' => 'rewrite'
	  ),
	  3 =>
	  array (
		'title' => '内容过滤',
		'link' => 'admin.php?mod=setting&code=modify_filter',
		'shortcut' => false,
		'priv' => 'filter'
	  ),
	  4 =>
	  array (
		'title' => '友情链接',
		'link' => 'admin.php?mod=link',
		'shortcut' => false,
		'priv' => 'link'
	  ),
	  5 =>
	  array (
		'title' => 'IP访问控制',
		'link' => 'admin.php?mod=setting&code=modify_access',
		'shortcut' => false,
		'priv' => 'ipset'
	  ),
	  6 =>
	  array (
		'title' => '顶部导航设置',
		'link' => 'admin.php?mod=tttuangou&code=indexnav',
		'shortcut' => false,
		'priv' => 'navset'
	  ),
	  7 =>
	  array (
		'title' => '客服信息设置',
		'link' => 'admin.php?mod=widget&code=block&op=config&flag=cservice',
		'shortcut' => false,
		'priv' => 'cserset'
	  ),
	  8 =>
	  array (
		'title' => '支付设置',
		'link' => 'admin.php?mod=payment',
		'shortcut' => true,
		'priv' => 'payment'
	  ),
	  9 =>
	  array (
		'title' => '上传设置',
		'link' => 'admin.php?mod=upload&code=config',
		'shortcut' => false,
		'priv' => 'upload'
	  ),
	  10 =>
	  array (
		'title' => '图片水印',
		'link' => 'admin.php?mod=image&code=watermark',
		'shortcut' => false,
		'priv' => 'watermark'
	  ),
	  25 =>
	  array (
		'title' => '一键登录',
		'link' => 'admin.php?mod=ulogin',
		'shortcut' => false,
		'priv' => 'ulogin'
	  ),
	  26 =>
	  array (
		'title' => '积分设置',
		'link' => 'admin.php?mod=account&code=credit',
		'shortcut' => false,
		'priv' => 'creditset'
	  ),

	  1001 =>
	  array (
		'title' => TUANGOU_STR . '设置',
		'link' => 'hr',
		'shortcut' => false
	  ),
	  11 =>
	  array (
		'title' => '常用设置',
		'link' => 'admin.php?mod=tttuangou&code=varshow',
		'shortcut' => true,
		'priv' => 'shopset'
	  ),
	  12 =>
	  array (
		'title' => '侧边栏管理',
		'link' => 'admin.php?mod=widget',
		'shortcut' => false,
		'priv' => 'widget'
	  ),
	  13 =>
	  array (
		'title' => '广告管理',
		'link' => 'admin.php?mod=ad&code=vlist',
		'shortcut' => false,
		'priv' => 'adset'
	  ),
	  14 =>
	  array (
		'title' => '静态页面管理',
		'link' => 'admin.php?mod=html&code=front',
		'shortcut' => false,
		'priv' => 'htmlset'
	  ),
	  15 =>
	  array (
		'title' => TUANGOU_STR . '券设置',
		'link' => 'admin.php?mod=coupon&code=config',
		'shortcut' => false,
		'priv' => 'coupset'
	  ),
	  1002 =>
	  array (
		'title' => '模板风格设置',
		'link' => 'hr',
		'shortcut' => false
	  ),
	  20 =>
	  array (
		'title' => '模板设置',
		'link' => 'admin.php?mod=styles&code=temp',
		'shortcut' => true,
		'priv' => 'templates'
	  ),
	  21 =>
	  array (
		'title' => '皮肤设置',
		'link' => 'admin.php?mod=styles&code=vlist',
		'shortcut' => true,
		'priv' => 'styles'
	  ),
	  22 =>
	  array (
		'title' => '站点Logo',
		'link' => 'admin.php?mod=tttuangou&code=sitelogo',
		'shortcut' => false,
		'priv' => 'sitelogo'
	  ),
	  23 =>
	  array (
		'title' => '分享设置',
		'link' => 'admin.php?mod=tttuangou&code=shareconfig',
		'shortcut' => false,
		'priv' => 'share'
	  ),
	  24 =>
	  array (
		'title' => '多团设置',
		'link' => 'admin.php?mod=ui&code=igos&op=config',
		'shortcut' => false,
		'priv' => 'uiigos'
	  ),
	),
  ),
  3 =>
  array (
	'title' => TUANGOU_STR . '管理',
	'link' => '',
	'sub_menu_list' =>
	array (
	  0 =>
	  array (
		'title' => '产品管理',
		'link' => 'admin.php?mod=product',
		'shortcut' => true,
		'priv' => 'product'
	  ),
	  2 =>
	  array (
		'title' => '订单管理',
		'link' => 'admin.php?mod=order',
		'shortcut' => true,
		'priv' => 'ordermanage'
	  ),
	  3 =>
	  array (
		'title' => TUANGOU_STR . '券管理',
		'link' => 'admin.php?mod=coupon',
		'shortcut' => false,
		'priv' => 'coupon'
	  ),
	  4 =>
	  array (
		'title' => '发货管理',
		'link' => 'admin.php?mod=delivery&code=vlist&ordproc=WAIT_SELLER_SEND_GOODS',
		'shortcut' => true,
		'priv' => 'delivery'
	  ),
	  5 =>
	  array (
		'title' => '快递单打印',
		'link' => 'admin.php?mod=print&code=delivery&op=queue',
		'shortcut' => true,
		'priv' => 'print'
	  ),
	  7 =>
	  array (
		'title' => '城市区域',
		'link' => 'admin.php?mod=tttuangou&code=city',
		'shortcut' => false,
		'priv' => 'city'
	  ),
	  9 =>
	  array (
		'title' => '配送管理',
		'link' => 'admin.php?mod=express',
		'shortcut' => false,
		'priv' => 'express'
	  ),
	  10 =>
	  array (
		'title' => '分类管理',
		'link' => 'admin.php?mod=catalog',
		'shortcut' => false,
		'priv' => 'catalog'
	  ),
	  13 =>
	  array (
		'title' => '抽奖管理',
		'link' => 'admin.php?mod=prize&code=vlist',
		'shortcut' => false,
		'priv' => 'prize'
	  ),
	  14 =>
	  array (
		'title' => '标签管理',
		'link' => 'admin.php?mod=tag',
		'shortcut' => false,
		'priv' => 'tag'
	  ),
	  1001 =>
	  array (
		'title' => '数据清理',
		'link' => 'hr',
		'shortcut' => false
	  ),
	  101 =>
	  array (
		'title' => '数据初始化',
		'link' => 'admin.php?mod=tttuangou&code=clear',
		'shortcut' => false,
		'priv' => 'dataclear'
	  ),
	),
  ),
  4 =>
  array (
	'title' => '商家管理',
	'link' => '',
	'sub_menu_list' =>
	array (
	  8 =>
	  array (
		'title' => '商家管理',
		'link' => 'admin.php?mod=tttuangou&code=mainseller',
		'shortcut' => true,
		'priv' => 'seller'
	  ),
	  1 =>
	  array (
		'title' => '商家结算管理',
		'link' => 'admin.php?mod=fund&code=order',
		'shortcut' => false,
		'priv' => 'fundorder'
	  ),
	  	  101 =>
	  array (
		'title' => '商家结算设置',
		'link' => 'admin.php?mod=fund&code=config',
		'shortcut' => false,
		'priv' => 'fundset'
	  ),
	  103 =>
	  array (
		'title' => '商家分成设置',
		'link' => 'admin.php?mod=rebate_setting&code=show',
		'shortcut' => false,
		'priv' => 'rebate'
	  ),
	),
  ),
  5 =>
  array (
	'title' => '互动营销',
	'link' => '',
	'sub_menu_list' =>
	array (
	  1 =>
	  array (
		'title' => '短信平台设置',
		'link' => 'admin.php?mod=service&code=sms',
		'shortcut' => true,
		'priv' => 'servicesms'
	  ),
	  2 =>
	  array (
		'title' => '群发服务管理',
		'link' => 'admin.php?mod=service',
		'shortcut' => false,
		'priv' => 'service'
	  ),
	  3 =>
	  array (
		'title' => '订阅管理',
		'link' => 'admin.php?mod=subscribe',
		'shortcut' => false,
		'priv' => 'subscribe'
	  ),
	  4 =>
	  array (
		'title' => '订阅群发',
		'link' => 'admin.php?mod=subscribe&code=broadcast&class=mail',
		'shortcut' => false,
		'priv' => 'subscribemail'
	  ),
	  5 =>
	  array (
		'title' => '通知方式',
		'link' => 'admin.php?mod=notify',
		'shortcut' => false,
		'priv' => 'notify'
	  ),
	  6 =>
	  array (
		'title' => '通知事件管理',
		'link' => 'admin.php?mod=notify&code=event',
		'shortcut' => false,
		'priv' => 'notifyevent'
	  ),
	  7 =>
	  array (
		'title' => '问答管理',
		'link' => 'admin.php?mod=tttuangou&code=mainquestion',
		'shortcut' => false,
		'priv' => 'question'
	  ),
	  8 =>
	  array (
		'title' => '反馈信息',
		'link' => 'admin.php?mod=tttuangou&code=usermsg',
		'shortcut' => false,
		'priv' => 'usermsg'
	  ),
	  9 =>
	  array (
		'title' => '文章管理',
		'link' => 'admin.php?mod=article',
		'shortcut' => false,
		'priv' => 'article'
	  ),
	  10 =>
	  array (
		'title' => '财务报表',
		'link' => 'admin.php?mod=reports',
		'shortcut' => false,
		'priv' => 'reports'
	  ),
	  110 =>
	  array (
		'title' => '报表统计',
		'link' => 'admin.php?mod=salecount',
		'shortcut' => false,
		'priv' => 'salecount'
	  ),
	  11 =>
	  array (
		'title' => '评论管理',
		'link' => 'admin.php?mod=comment&code=vlist',
		'shortcut' => false,
		'priv' => 'comments'
	  ),
	  1001 =>
	  array (
		'title' => '推送管理',
		'link' => 'hr',
		'shortcut' => false
	  ),
	  101 =>
	  array (
		'title' => '推送队列',
		'link' => 'admin.php?mod=push&code=queue',
		'shortcut' => false,
		'priv' => 'push'
	  ),
	  2001 =>
	  array (
		'title' => '数据调用',
		'link' => 'hr',
		'shortcut' => false
	  ),
	  201 =>
	  array (
		'title' => '外部调用',
		'link' => 'admin.php?mod=tttuangou&code=dataapi',
		'shortcut' => false,
		'priv' => 'dataapi'
	  ),
	),
  ),
  6 =>
  array (
	'title' => '客户端管理',
	'link' => '',
	'sub_menu_list' =>
	array (
	  1 =>
	  array (
		'title' => '移动应用管理',
		'link' => 'admin.php?mod=api',
		'shortcut' => false,
		'priv' => 'apimanage'
	  ),
	  2 =>
	  array (
		'title' => '客户端版本管理',
		'link' => 'admin.php?mod=api&code=release',
		'shortcut' => false,
		'priv' => 'apimanage'
	  ),
	  3 =>
	  array (
		'title' => '移动支付管理',
		'link' => 'admin.php?mod=setting&code=mnote',
		'shortcut' => false,
		'priv' => 'apimanage'
	  ),
	  4 =>
	  array (
		'title' => '通知栏推送设置',
		'link' => 'admin.php?mod=setting&code=push',
		'shortcut' => false,
		'priv' => 'apimanage'
	  ),
	  5 =>
	  array (
		'title' => '安卓客户端介绍',
		'link' => ihelper('tg.app.android'),
		'shortcut' => false,
		'priv' => 'apimanage'
	  ),
	  6 =>
	  array (
		'title' => '客户端下载管理',
		'link' => 'admin.php?mod=app',
		'shortcut' => false,
		'priv' => 'appmanage'
	  ),
	),
  ),
  7 =>
  array (
	'title' => '系统工具',
	'link' => '',
	'sub_menu_list' =>
	array (
	  1 =>
	  array (
		'title' => '更新缓存',
		'link' => 'admin.php?mod=cache',
		'shortcut' => false,
		'priv' => 'cache'
	  ),
	  2 =>
	  array (
		'title' => '在线升级',
		'link' => 'admin.php?mod=upgrade',
		'shortcut' => true,
		'priv' => 'upgrade'
	  ),
	  3 =>
	  array (
		'title' => '错误调试',
		'link' => 'admin.php?mod=dev',
		'shortcut' => false,
		'priv' => 'dev'
	  ),
	  4 =>
	  array (
		'title' => '日志中心',
		'link' => 'admin.php?mod=zlog',
		'shortcut' => true,
		'priv' => 'zlog'
	  ),
	  5 =>
	  array (
		'title' => '入侵检测',
		'link' => 'admin.php?mod=wips',
		'shortcut' => false,
		'priv' => 'wips'
	  ),
	  6 =>
	  array (
		'title' => '文件校验',
		'link' => 'admin.php?mod=filecheck',
		'shortcut' => false,
		'priv' => 'filecheck'
	  ),
	  1001 =>
	  array (
		'title' => '数据库',
		'link' => 'hr',
		'shortcut' => false
	  ),
	  11 =>
	  array (
		'title' => '数据备份',
		'link' => 'admin.php?mod=db&code=export',
		'shortcut' => false,
		'priv' => 'dbexport'
	  ),
	  12 =>
	  array (
		'title' => '数据恢复',
		'link' => 'admin.php?mod=db&code=import',
		'shortcut' => false,
		'priv' => 'dbimport'
	  ),
	  13 =>
	  array (
		'title' => '数据表优化',
		'link' => 'admin.php?mod=db&code=optimize',
		'shortcut' => false,
		'priv' => 'dboptimize'
	  ),
	  14 =>
	  array (
		'title' => '数据库修复',
		'link' => 'admin.php?mod=db&code=repair',
		'shortcut' => false,
		'priv' => 'dbrepair'
	  ),
	  1002 =>
	  array (
		'title' => '站点信息',
		'link' => 'hr',
		'shortcut' => false
	  ),
	  21 =>
	  array (
		'title' => '蜘蛛爬行统计',
		'link' => 'admin.php?mod=robot',
		'shortcut' => false,
		'priv' => 'robot'
	  ),
	),
  ),
  8 =>
  array (
	'title' => '用户管理',
	'link' => '',
	'sub_menu_list' =>
	array (
	  1 =>
	  array (
		'title' => 'Ucenter整合',
		'link' => 'admin.php?mod=ucenter',
		'shortcut' => false,
		'priv' => 'ucenter'
	  ),
	  2 =>
	  array (
		'title' => '+添加新用户',
		'link' => 'admin.php?mod=member&code=add',
		'shortcut' => false,
		'priv' => 'memberadd'
	  ),
	  3 =>
	  array (
		'title' => '编辑用户',
		'link' => 'admin.php?mod=member&code=search',
		'shortcut' => false,
		'priv' => 'memberedite'
	  ),
	  
	  5 =>
	  array (
		'title' => '当前在线用户',
		'link' => 'admin.php?mod=sessions',
		'shortcut' => false,
		'priv' => 'sessions'
	  ),
	  6 =>
	  array (
		'title' => '用户提现管理',
		'link' => 'admin.php?mod=cash&code=order',
		'shortcut' => false,
		'priv' => 'cashorder'
	  ),
	 7 =>
	  array (
		'title' => '充值订单管理',
		'link' => 'admin.php?mod=recharge&code=order',
		'shortcut' => false,
		'priv' => 'rechargeorder'
	  ),
	  8 =>
	  array (
		'title' => '充值卡管理',
		'link' => 'admin.php?mod=recharge&code=card',
		'shortcut' => false,
		'priv' => 'rechargecard'
	  ),
	  9 =>
	  array (
		'title' => '退款申请管理',
		'link' => 'admin.php?mod=refund',
		'shortcut' => false,
		'priv' => 'refund'
	  ),
	  10 =>
	  array (
		'title' => '用户消费明细',
		'link' => 'admin.php?mod=member&code=moneylog',
		'shortcut' => false,
		'priv' => 'member_moneylog'
	  ),
	  200 =>
	  array (
		'title' => '用户设置',
		'link' => 'hr',
		'shortcut' => false
	  ),
	  201 =>
	  array (
		'title' => '用户注册设置',
		'link' => 'admin.php?mod=account&code=config',
		'shortcut' => false,
		'priv' => 'userreg'
	  ),
	  202 =>
	  array (
		'title' => '等级设置',
		'link' => 'admin.php?mod=account&code=credits',
		'shortcut' => false,
		'priv' => 'credits'
	  ),
	  203 =>
	  array (
		'title' => '充值返现设置',
		'link' => 'admin.php?mod=recharge&code=config',
		'shortcut' => false,
		'priv' => 'rechargeset'
	  ),
	 204 =>
	  array (
		'title' => '返利比例设置',
		'link' => 'admin.php?mod=recharge&code=scale',
		'shortcut' => false,
		'priv' => 'rechargescale'
	  ),

	),
  ),
  9 =>
  array (
	'title' => '使用帮助',
	'link' => '',
	'sub_menu_list' =>
	array (
	  1111 =>
	  array (
		'title' => '帮助手册',
		'link' => ihelper('tg.helper'),
		'shortcut' => false,
		'priv' => ''
	  ),
	  1112 =>
	  array (
		'title' => '短信购买',
		'link' => ihelper('tg.shop'),
		'shortcut' => false,
		'priv' => ''
	  ),
	  1113 =>
	  array (
		'title' => '支付宝申请',
		'link' => ihelper('tg.payment.alipay'),
		'shortcut' => false,
		'priv' => ''
	  ),
	  1114 =>
	  array (
		'title' => '网银直连接口',
		'link' => ihelper('tg.payment.wangyin'),
		'shortcut' => false,
		'priv' => ''
	  ),
	  1115 =>
	  array (
		'title' => '技术支持',
		'link' => 'hr',
		'shortcut' => false
	  ),
	  1116 =>
	  array (
		'title' => '支持论坛',
		'link' => ihelper('cenwor.forum'),
		'shortcut' => false,
		'priv' => ''
	  ),
	),
  ),
);
?>