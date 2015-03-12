<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name admin_privs.php
 * @date 2014-12-11 14:44:49
 */
 

$privs_list = array (
  1 => array ('title' => '全局设置','sub_priv_list' =>
	array (
	  1 => array ('title' => '核心设置','priv' => 'siteset'),
	  2 => array ('title' => '伪静态','priv' => 'rewrite'),
	  3 => array ('title' => '内容过滤','priv' => 'filter'),
	  4 => array ('title' => '友情链接','priv' => 'link'),
	  5 => array ('title' => 'IP访问控制','priv' => 'ipset'),
	  6 => array ('title' => '顶部导航设置','priv' => 'navset'),
	  7 => array ('title' => '客服信息设置','priv' => 'cserset'),
	  8 => array ('title' => '支付设置','priv' => 'payment'),
	  9 => array ('title' => '上传设置','priv' => 'upload'),
	  10 => array ('title' => '图片水印','priv' => 'watermark'),
	  11 => array ('title' => '一键登录','priv' => 'ulogin'),
	  12 => array ('title' => '积分设置','priv' => 'creditset')
	 )
	),
  2 => array ('title' => TUANGOU_STR . '设置','sub_priv_list' =>
  	array (
	  1 => array ('title' => '常用设置','priv' => 'shopset'),
	  2 => array ('title' => '侧边栏管理','priv' => 'widget'),
	  3 => array ('title' => '广告管理','priv' => 'adset'),
	  4 => array ('title' => '静态页面管理','priv' => 'htmlset'),
	  5 => array ('title' => TUANGOU_STR . '券设置','priv' => 'coupset')
	 )
	),
  3 => array ('title' => '模板风格设置','sub_priv_list' =>
  	array (
	  1 => array ('title' => '模板设置','priv' => 'templateset'),
	  2 => array ('title' => '皮肤设置','priv' => 'styles'),
	  3 => array ('title' => '站点Logo','priv' => 'sitelogo'),
	  4 => array ('title' => '分享设置','priv' => 'share'),
	  5 => array ('title' => '多团设置','priv' => 'uiigos'),
	  6 => array ('title' => '后台快捷菜单','priv' => 'shortcut')
	)
  ),
  4 => array ('title' => TUANGOU_STR . '管理','sub_priv_list' =>
	array (
	  1 => array ('title' => '产品管理','priv' => 'product'),
	  2 => array ('title' => '订单管理','priv' => 'ordermanage'),
	  3 => array ('title' => '订单删除','priv' => 'orderdelete'),
	  4 => array ('title' => TUANGOU_STR . '券管理','priv' => 'coupon'),
	  5 => array ('title' => '发货管理','priv' => 'delivery'),
	  6 => array ('title' => '快递单打印','priv' => 'print'),
	  7 => array ('title' => '城市区域','priv' => 'city'),
	  8 => array ('title' => '配送管理','priv' => 'express'),
	  9 => array ('title' => '分类管理','priv' => 'catalog'),
	  10 => array ('title' => '抽奖管理','priv' => 'prize'),
	  11 => array ('title' => '数据初始化','priv' => 'dataclear'),
	  12 => array ('title' => '产品推荐权限','priv' => 'producthot'),
	)
  ),
  5 => array ('title' => '商家管理','sub_priv_list' =>
	array (
	  1 => array ('title' => '商家管理','priv' => 'seller'),
	  2 => array ('title' => '商家结算管理','priv' => 'fundorder'),
	  3 => array ('title' => '商家结算设置','priv' => 'fundset'),
	  4 => array ('title' => '商家分成设置','priv' => 'rebate')
	)
  ),
  6 =>
  array ('title' => '互动营销','sub_priv_list' =>
	array (
	  1 => array ('title' => '短信平台设置','priv' => 'servicesms'),
	  2 => array ('title' => '群发服务管理','priv' => 'service'),
	  3 => array ('title' => '订阅管理','priv' => 'subscribe'),
	  4 => array ('title' => '订阅群发','priv' => 'subscribemail'),
	  5 => array ('title' => '通知方式','priv' => 'notify'),
	  6 => array ('title' => '通知事件管理','priv' => 'notifyevent'),
	  7 => array ('title' => '问答管理','priv' => 'question'),
	  8 => array ('title' => '反馈信息','priv' => 'usermsg'),
	  9 => array ('title' => '文章管理','priv' => 'article'),
	  10 => array ('title' => '财务报表','priv' => 'reports'),
	  11 => array ('title' => '报表统计','priv' => 'salecount'),
	  12 => array ('title' => '评论管理','priv' => 'comments'),
	  13 => array ('title' => '推送管理','priv' => 'push'),
	  14 => array ('title' => '数据调用','priv' => 'dataapi')
	)
  ),
  7 =>
  array ('title' => '客户端管理','sub_priv_list' =>
	array (
	  1 => array ('title' => '移动应用管理','priv' => 'apimanage'),
	  2 => array ('title' => '客户端下载管理','priv' => 'appmanage')
	)
  ),
  8 => array ('title' => '系统工具','sub_priv_list' =>
	array (
	  1 => array ('title' => '更新缓存','priv' => 'cache'),
	  2 => array ('title' => '在线升级','priv' => 'upgrade'),
	  3 => array ('title' => '错误调试','priv' => 'dev'),
	  4 => array ('title' => '日志中心','priv' => 'zlog'),
	  5 => array ('title' => '入侵检测','priv' => 'wips'),
	  6 => array ('title' => '文件校验','priv' => 'filecheck'),
	  7 => array ('title' => '数据备份','priv' => 'dbexport'),
	  8 => array ('title' => '数据恢复','priv' => 'dbimport'),
	  9 => array ('title' => '数据表优化','priv' => 'dboptimize'),
	  10 => array ('title' => '数据库修复','priv' => 'dbrepair'),
	  11 => array ('title' => '蜘蛛爬行统计','priv' => 'robot')
	)
  ),
  9 =>
  array ('title' => '用户管理','sub_priv_list' =>
	array (
	  1 => array ('title' => 'Ucenter整合','priv' => 'ucenter'),
	  2 => array ('title' => '添加新用户','priv' => 'memberadd'),
	  3 => array ('title' => '编辑用户','priv' => 'memberedite'),
	  
	  5 => array ('title' => '查看在线用户','priv' => 'sessions'),
	  6 => array ('title' => '用户提现管理','priv' => 'cashorder'),
	  7 => array ('title' => '充值订单管理','priv' => 'rechargeorder'),
	  8 => array ('title' => '充值卡管理','priv' => 'rechargecard'),
	  9 => array ('title' => '退款申请管理','priv' => 'refund'),
	  10 => array ('title' => '用户注册设置','priv' => 'userreg'),
	  11 => array ('title' => '权限设置','priv' => 'privs'),
	  12 => array ('title' => '等级设置','priv' => 'credits'),
	  13 => array ('title' => '充值返现设置','priv' => 'rechargeset'),
	  14 => array ('title' => '返利比例设置','priv' => 'rechargescale'),
	  15 => array ('title' => '快捷充值/扣费','priv' => 'quickrecharge'),
	  16 => array ('title' => '用户消费明细','priv' => 'member_moneylog'),
	)
  )
);
?>