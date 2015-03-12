<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name widget.php
 * @date 2014-12-11 14:44:49
 */
 


$config["widget"] =  array (
  'index_home' => 
  array (
    'blocks' => 
    array (
      'broadcast' => 
      array (
        'enabled' => true,
      ),
      'follow_us' => 
      array (
        'enabled' => true,
      ),
      'cservice' => 
      array (
        'enabled' => true,
      ),
      'asker' => 
      array (
        'enabled' => true,
      ),
      'admin_widget_guide' => 
      array (
        'enabled' => true,
      ),
      'article_list' => 
      array (
        'enabled' => true,
      ),
    ),
    'name' => '首页产品列表',
  ),
  'index_detail' => 
  array (
    'name' => '首页产品详情',
    'blocks' => 
    array (
      'cservice' => 
      array (
        'enabled' => true,
      ),
      'product_list' => 
      array (
        'enabled' => true,
      ),
      'asker' => 
      array (
        'enabled' => true,
      ),
      'admin_widget_guide' => 
      array (
        'enabled' => true,
      ),
    ),
  ),
  'countdown' => 
  array (
    'blocks' => 
    array (
      'broadcast' => 
      array (
        'enabled' => true,
      ),
      'follow_us' => 
      array (
        'enabled' => true,
      ),
      'cservice' => 
      array (
        'enabled' => true,
      ),
    ),
    'name' => '限时抢购',
  ),
  'list_deals' => 
  array (
    'blocks' => 
    array (
      'product_list' => 
      array (
        'enabled' => true,
      ),
      'admin_widget_guide' => 
      array (
        'enabled' => true,
      ),
    ),
    'name' => '往期团购',
  ),
  'list_ask' => 
  array (
    'blocks' => 
    array (
      'cservice' => 
      array (
        'enabled' => true,
      ),
      'admin_widget_guide' => 
      array (
        'enabled' => true,
      ),
    ),
    'name' => '问答模块',
  ),
  'me_coupon' => 
  array (
    'name' => '我的团购券',
    'blocks' => 
    array (
      'my_account' => 
      array (
        'enabled' => true,
      ),
      'user_nav' => 
      array (
        'enabled' => true,
      ),
      'user_cash_nav' => 
      array (
        'enabled' => true,
      ),
      'faq_my_coupon' => 
      array (
        'enabled' => true,
      ),
      'admin_widget_guide' => 
      array (
        'enabled' => true,
      ),
    ),
  ),
  'me_order' => 
  array (
    'name' => '我的订单',
    'blocks' => 
    array (
      'my_account' => 
      array (
        'enabled' => true,
      ),
      'user_nav' => 
      array (
        'enabled' => true,
      ),
      'user_cash_nav' => 
      array (
        'enabled' => true,
      ),
      'faq_my_order' => 
      array (
        'enabled' => true,
      ),
      'admin_widget_guide' => 
      array (
        'enabled' => true,
      ),
    ),
  ),
  'me_bill' => 
  array (
    'name' => '我的收支明细',
    'blocks' => 
    array (
      'my_account' => 
      array (
        'enabled' => true,
      ),
      'user_nav' => 
      array (
        'enabled' => true,
      ),
      'user_cash_nav' => 
      array (
        'enabled' => true,
      ),
      'faq_my_bill' => 
      array (
        'enabled' => true,
      ),
      'admin_widget_guide' => 
      array (
        'enabled' => true,
      ),
    ),
  ),
  'me_setting' => 
  array (
    'name' => '账户设置',
    'blocks' => 
    array (
      'my_account' => 
      array (
        'enabled' => true,
      ),
      'user_nav' => 
      array (
        'enabled' => true,
      ),
      'user_cash_nav' => 
      array (
        'enabled' => true,
      ),
      'admin_widget_guide' => 
      array (
        'enabled' => true,
      ),
    ),
  ),
  'me_phone' => 
  array (
    'name' => '手机设置',
    'blocks' => 
    array (
      'my_account' => 
      array (
        'enabled' => true,
      ),
      'user_nav' => 
      array (
        'enabled' => true,
      ),
      'user_cash_nav' => 
      array (
        'enabled' => true,
      ),
      'admin_widget_guide' => 
      array (
        'enabled' => true,
      ),
    ),
  ),
  'me_address' => 
  array (
    'name' => '我的收货地址',
    'blocks' => 
    array (
      'my_account' => 
      array (
        'enabled' => true,
      ),
      'user_nav' => 
      array (
        'enabled' => true,
      ),
      'user_cash_nav' => 
      array (
        'enabled' => true,
      ),
      'admin_widget_guide' => 
      array (
        'enabled' => true,
      ),
    ),
  ),
  '~@blocks' => 
  array (
    'invite' => 
    array (
      'name' => '邀请返利',
    ),
    'cservice' => 
    array (
      'name' => '客服信息',
    ),
    'product_list' => 
    array (
      'name' => '其他团购列表',
    ),
    'broadcast' => 
    array (
      'name' => '首页公告',
    ),
    'faq_invite' => 
    array (
      'name' => '邀请返利FAQ',
    ),
    'faq_my_bill' => 
    array (
      'name' => '收支明细FAQ',
    ),
    'faq_my_order' => 
    array (
      'name' => '我的订单FAQ',
    ),
    'faq_my_coupon' => 
    array (
      'name' => '我的团购券FAQ',
    ),
    'my_account' => 
    array (
      'name' => '账户统计',
    ),
    'asker' => 
    array (
      'name' => '问答模块',
    ),
    'faq_ticket' => 
    array (
      'name' => '团购券FAQ',
    ),
    'follow_us' => 
    array (
      'name' => '微博链接',
    ),
    'admin_widget_guide' => 
    array (
      'name' => '管理员提示',
    ),
    'article_list' => 
    array (
      'name' => '新闻资讯',
    ),
    'order_buys' => 
    array (
      'name' => '最近购买',
    ),
    'member_growth' => 
    array (
      'name' => '我的成长值',
    ),
    'user_cash_nav' => 
    array (
      'name' => '我的帐户',
    ),
    'user_nav' => 
    array (
      'name' => '我的订单',
    ),
  ),
  'openapi_main' => 
  array (
    'blocks' => 
    array (
      'cservice' => 
      array (
        'enabled' => true,
      ),
      'admin_widget_guide' => 
      array (
        'enabled' => true,
      ),
    ),
    'name' => '开放API',
  ),
  'buy_checkout' => 
  array (
    'blocks' => 
    array (
      'cservice' => 
      array (
        'enabled' => true,
      ),
      'my_account' => 
      array (
        'enabled' => true,
      ),
      'admin_widget_guide' => 
      array (
        'enabled' => true,
      ),
    ),
    'name' => '提交订单',
  ),
  'buy_order' => 
  array (
    'blocks' => 
    array (
      'cservice' => 
      array (
        'enabled' => true,
      ),
      'asker' => 
      array (
        'enabled' => true,
      ),
      'admin_widget_guide' => 
      array (
        'enabled' => true,
      ),
    ),
    'name' => '确认订单',
  ),
  'buy_pay' => 
  array (
    'blocks' => 
    array (
      'cservice' => 
      array (
        'enabled' => true,
      ),
      'my_account' => 
      array (
        'enabled' => true,
      ),
      'admin_widget_guide' => 
      array (
        'enabled' => true,
      ),
    ),
    'name' => '支付订单',
  ),
  'account_register' => 
  array (
    'blocks' => 
    array (
      'cservice' => 
      array (
        'enabled' => true,
      ),
      'asker' => 
      array (
        'enabled' => true,
      ),
      'admin_widget_guide' => 
      array (
        'enabled' => true,
      ),
    ),
    'name' => '注册帐号',
  ),
  'account_login' => 
  array (
    'blocks' => 
    array (
      'cservice' => 
      array (
        'enabled' => true,
      ),
      'admin_widget_guide' => 
      array (
        'enabled' => true,
      ),
    ),
    'name' => '用户登录',
  ),
  'subscribe_sms' => 
  array (
    'blocks' => 
    array (
      'product_list' => 
      array (
        'enabled' => true,
      ),
      'admin_widget_guide' => 
      array (
        'enabled' => true,
      ),
    ),
    'name' => '短信订阅',
  ),
  'list_invite' => 
  array (
    'blocks' => 
    array (
      'faq_invite' => 
      array (
        'enabled' => true,
      ),
      'admin_widget_guide' => 
      array (
        'enabled' => true,
      ),
    ),
    'name' => '邀请返利',
  ),
  'subscribe_mail' => 
  array (
    'blocks' => 
    array (
      'product_list' => 
      array (
        'enabled' => true,
      ),
      'admin_widget_guide' => 
      array (
        'enabled' => true,
      ),
    ),
    'name' => '邮件订阅',
  ),
  'me_printticket' => 
  array (
    'blocks' => 
    array (
      'faq_my_coupon' => 
      array (
        'enabled' => true,
      ),
      'admin_widget_guide' => 
      array (
        'enabled' => true,
      ),
    ),
    'name' => '打印团购券',
  ),
  'list_business' => 
  array (
    'blocks' => 
    array (
      'cservice' => 
      array (
        'enabled' => true,
      ),
      'asker' => 
      array (
        'enabled' => true,
      ),
      'admin_widget_guide' => 
      array (
        'enabled' => true,
      ),
    ),
    'name' => '商务合作',
  ),
  'list_feedback' => 
  array (
    'blocks' => 
    array (
      'cservice' => 
      array (
        'enabled' => true,
      ),
      'follow_us' => 
      array (
        'enabled' => true,
      ),
    ),
    'name' => '意见反馈',
  ),
  'list_ckticket' => 
  array (
    'blocks' => 
    array (
      'faq_my_coupon' => 
      array (
        'enabled' => true,
      ),
      'cservice' => 
      array (
        'enabled' => true,
      ),
      'admin_widget_guide' => 
      array (
        'enabled' => true,
      ),
    ),
    'name' => '团购券验证页面',
  ),
  'subscribe_validate' => 
  array (
    'blocks' => 
    array (
      'cservice' => 
      array (
        'enabled' => true,
      ),
      'admin_widget_guide' => 
      array (
        'enabled' => true,
      ),
    ),
    'name' => '订阅验证页面',
  ),
  'subscribe_undo' => 
  array (
    'blocks' => 
    array (
      'cservice' => 
      array (
        'enabled' => true,
      ),
      'admin_widget_guide' => 
      array (
        'enabled' => true,
      ),
    ),
    'name' => '取消订阅页面',
  ),
  'html_help' => 
  array (
    'name' => '团购指南',
    'blocks' => 
    array (
    ),
  ),
  'html_faq' => 
  array (
    'name' => '常见问题',
    'blocks' => 
    array (
    ),
  ),
  'html_contact' => 
  array (
    'name' => '联系我们',
    'blocks' => 
    array (
    ),
  ),
  'html_about' => 
  array (
    'name' => '关于我们',
    'blocks' => 
    array (
    ),
  ),
  'html_privacy' => 
  array (
    'name' => '隐私保护',
    'blocks' => 
    array (
    ),
  ),
  'html_join' => 
  array (
    'name' => '加入我们',
    'blocks' => 
    array (
    ),
  ),
  'html_terms' => 
  array (
    'name' => '用户协议',
    'blocks' => 
    array (
    ),
  ),
  'recharge_main' => 
  array (
    'name' => '帐户充值',
    'blocks' => 
    array (
      'my_account' => 
      array (
        'enabled' => true,
      ),
      'user_nav' => 
      array (
        'enabled' => true,
      ),
      'user_cash_nav' => 
      array (
        'enabled' => true,
      ),
      'faq_my_order' => 
      array (
        'enabled' => true,
      ),
    ),
  ),
  'recharge_order' => 
  array (
    'name' => '充值记录',
    'blocks' => 
    array (
      'my_account' => 
      array (
        'enabled' => true,
      ),
      'user_nav' => 
      array (
        'enabled' => true,
      ),
      'user_cash_nav' => 
      array (
        'enabled' => true,
      ),
      'faq_my_order' => 
      array (
        'enabled' => true,
      ),
    ),
  ),
  'recharge_pay' => 
  array (
    'name' => '支付页面',
    'blocks' => 
    array (
      'my_account' => 
      array (
        'enabled' => true,
      ),
      'faq_my_order' => 
      array (
        'enabled' => true,
      ),
    ),
  ),
  'cash_order' => 
  array (
    'name' => '提现记录',
    'blocks' => 
    array (
      'my_account' => 
      array (
        'enabled' => true,
      ),
      'user_nav' => 
      array (
        'enabled' => true,
      ),
      'user_cash_nav' => 
      array (
        'enabled' => true,
      ),
      'faq_my_order' => 
      array (
        'enabled' => true,
      ),
    ),
  ),
  'cash_main' => 
  array (
    'name' => '帐户提现',
    'blocks' => 
    array (
      'my_account' => 
      array (
        'enabled' => true,
      ),
      'user_nav' => 
      array (
        'enabled' => true,
      ),
      'user_cash_nav' => 
      array (
        'enabled' => true,
      ),
      'faq_my_order' => 
      array (
        'enabled' => true,
      ),
    ),
  ),
  'me_credit' => 
  array (
    'name' => '我的积分',
    'blocks' => 
    array (
      'my_account' => 
      array (
        'enabled' => true,
      ),
      'user_nav' => 
      array (
        'enabled' => true,
      ),
      'user_cash_nav' => 
      array (
        'enabled' => true,
      ),
      'member_growth' => 
      array (
        'enabled' => true,
      ),
      'admin_widget_guide' => 
      array (
        'enabled' => true,
      ),
    ),
  ),
  'me_rebate' => 
  array (
    'blocks' => 
    array (
      'my_account' => 
      array (
        'enabled' => true,
      ),
      'user_nav' => 
      array (
        'enabled' => true,
      ),
      'user_cash_nav' => 
      array (
        'enabled' => true,
      ),
      'invite' => 
      array (
        'enabled' => true,
      ),
      'faq_invite' => 
      array (
        'enabled' => true,
      ),
    ),
    'name' => '我的返利',
  ),
  'me_favorite' => 
  array (
    'name' => '我的收藏',
    'blocks' => 
    array (
      'my_account' => 
      array (
        'enabled' => true,
      ),
      'user_nav' => 
      array (
        'enabled' => true,
      ),
      'user_cash_nav' => 
      array (
        'enabled' => true,
      ),
      'admin_widget_guide' => 
      array (
        'enabled' => true,
      ),
    ),
  ),
  'comment_main' => 
  array (
    'name' => '产品评价',
    'blocks' => 
    array (
      'my_account' => 
      array (
        'enabled' => true,
      ),
      'user_nav' => 
      array (
        'enabled' => true,
      ),
      'user_cash_nav' => 
      array (
        'enabled' => true,
      ),
      'admin_widget_guide' => 
      array (
        'enabled' => true,
      ),
    ),
  ),
);
?>