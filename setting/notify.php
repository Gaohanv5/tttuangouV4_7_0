<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name notify.php
 * @date 2014-12-11 14:44:49
 */
 


$config["notify"] =  array (
  'api' => 
  array (
    'sms' => 
    array (
      'name' => '短信通知',
      'enabled' => true,
    ),
    'mail' => 
    array (
      'name' => '邮件通知',
      'enabled' => true,
    ),
  ),
  'listener' => true,
  'event' => 
  array (
    'admin_mod_notify_Event_test' => 
    array (
      'struct' => '*',
      'name' => '后台通知测试',
      'msg' => 
      array (
        'sms' => '短信通知测试',
        'mail' => '邮件通知测试',
        'qqrobot' => '机器人通知测试',
      ),
      'cfg' => 
      array (
        'sms' => 
        array (
          'cc2admin' => false,
          'al2user' => true,
        ),
        'mail' => 
        array (
          'cc2admin' => false,
          'al2user' => true,
        ),
        'qqrobot' => 
        array (
          'cc2admin' => false,
          'al2user' => true,
        ),
      ),
      'hook' => 
      array (
        'qqrobot' => 
        array (
          'enabled' => true,
        ),
        'sms' => 
        array (
          'enabled' => false,
        ),
        'mail' => 
        array (
          'enabled' => false,
        ),
      ),
      'intro' => '后台通知测试，仅供测试是否可以正常通知',
    ),
    'logic_coupon_Create' => 
    array (
      'struct' => 'uid,productid,orderid,number,password,mutis,status,product.id,product.sellerid,product.city,product.name,product.flag,product.price,product.nowprice,product.img,product.intro,product.content,product.cue,product.theysay,product.wesay,product.begintime,product.overtime,product.type,product.perioddate,product.weight,product.successnum,product.virtualnum,product.maxnum,product.oncemax,product.oncemin,product.multibuy,product.allinone,product.totalnum,product.display,product.addtime,product.status,product.order,product.sellername,product.sellerphone,product.selleraddress,product.sellerurl,product.discount,product.time_remain,product.succ_total,product.succ_real,product.succ_buyers,product.succ_remain,product.sells_real,product.sells_count,product.surplus,',
      'name' => '团购券生成',
      'msg' => 
      array (
        'mail' => '感谢您的购买
产品：{product.flag}
订单号：{orderid}
团购券编号：{number}
密码：{password}
过期时间：{product.perioddate}
商家地址：{product.selleraddress}
联系电话：{product.sellerphone}',
        'qqrobot' => '感谢您的购买
订单号：{orderid}
团购券编号：{number}
密码：{password}',
        'sms' => '感谢您购买“{product.flag}”！您的团购券编号：{number}，密码：{password}，过期时间：{product.perioddate}，请尽快使用，以免过期！商家地址：{product.selleraddress}，联系电话：{product.sellerphone}',
      ),
      'cfg' => 
      array (
        'mail' => 
        array (
          'cc2admin' => false,
          'al2user' => true,
        ),
        'qqrobot' => 
        array (
          'cc2admin' => false,
          'al2user' => true,
        ),
        'sms' => 
        array (
          'cc2admin' => false,
          'al2user' => true,
        ),
      ),
      'hook' => 
      array (
        'mail' => 
        array (
          'enabled' => false,
        ),
        'qqrobot' => 
        array (
          'enabled' => true,
        ),
        'sms' => 
        array (
          'enabled' => true,
        ),
      ),
      'intro' => '当一个团购券被创建的时候会触发此通知',
    ),
    'logic_order_MakeSuccessed' => 
    array (
      'struct' => 'orderid,productflag,productnum,productprice,buytime,paymoney,paytime,expressprice,extmsg,',
      'name' => '订单完成',
      'intro' => '当用户的一笔订单交易支付完成时会触发此通知',
      'hook' => 
      array (
        'mail' => 
        array (
          'enabled' => false,
        ),
      ),
      'msg' => 
      array (
        'mail' => '您购买的“{productflag}”的订单已经确认！（订单号：{orderid}），感谢您的购买！',
      ),
      'cfg' => 
      array (
        'mail' => 
        array (
          'cc2admin' => false,
          'al2user' => true,
        ),
      ),
    ),
    'logic_pay_SendGoods' => 
    array (
      'struct' => 'trade_no,name,invoice,',
      'name' => '商家发货通知',
      'intro' => '当商家或者管理员上传了订单时便会触发此通知',
      'hook' => 
      array (
        'sms' => 
        array (
          'enabled' => false,
        ),
        'mail' => 
        array (
          'enabled' => false,
        ),
      ),
      'msg' => 
      array (
        'sms' => '您好，您购买的商品已经发货，送货方式：{name}，快递单号：{invoice}',
        'mail' => '您好，您购买的商品已经发货，送货方式：{name}，快递单号：{invoice}',
      ),
      'cfg' => 
      array (
        'sms' => 
        array (
          'cc2admin' => false,
          'al2user' => true,
        ),
        'mail' => 
        array (
          'cc2admin' => false,
          'al2user' => true,
        ),
      ),
    ),
    'admin_mod_order_AfService' => 
    array (
      'struct' => 'orderid,remark,',
      'name' => '订单管理：售后服务',
      'hook' => 
      array (
        'qqrobot' => 
        array (
          'enabled' => false,
        ),
      ),
      'msg' => 
      array (
        'qqrobot' => '',
      ),
      'cfg' => 
      array (
        'qqrobot' => 
        array (
          'cc2admin' => false,
          'al2user' => false,
        ),
      ),
      'intro' => '后台对订单进行售后操作时触发',
    ),
    'logic_order_Confirm' => 
    array (
      'struct' => 'orderid,',
      'name' => '订单管理：确认订单',
      'hook' => 
      array (
        'qqrobot' => 
        array (
          'enabled' => false,
        ),
      ),
      'msg' => 
      array (
        'qqrobot' => '',
      ),
      'cfg' => 
      array (
        'qqrobot' => 
        array (
          'cc2admin' => false,
          'al2user' => false,
        ),
      ),
      'intro' => '后台对订单进行确认操作时触发',
    ),
    'logic_order_Refund' => 
    array (
      'struct' => 'orderid,productflag,refundmoney,',
      'name' => '订单管理：退款',
      'intro' => '后台对订单进行退款操作时触发',
    ),
    'logic_order_Cancel' => 
    array (
      'struct' => 'orderid,',
      'name' => '订单管理：取消订单',
      'intro' => '后台对订单进行取消操作时触发',
    ),
    'admin_mod_coupon_Alert' => 
    array (
      'struct' => 'ticketid,uid,productid,orderid,number,password,usetime,mutis,status,name,flag,intro,perioddate,',
      'name' => '团购券消费提醒',
      'hook' => 
      array (
        'qqrobot' => 
        array (
          'enabled' => true,
        ),
        'mail' => 
        array (
          'enabled' => false,
        ),
        'sms' => 
        array (
          'enabled' => true,
        ),
      ),
      'msg' => 
      array (
        'qqrobot' => '你好，你的团购券即将到期，所属产品：{flag}',
        'mail' => '您好，您的团购券即将到期，请尽快使用！',
        'sms' => '您好，您的团购券（{number}）即将到期，请尽快使用！',
      ),
      'cfg' => 
      array (
        'qqrobot' => 
        array (
          'cc2admin' => false,
          'al2user' => true,
        ),
        'mail' => 
        array (
          'cc2admin' => false,
          'al2user' => true,
        ),
        'sms' => 
        array (
          'cc2admin' => false,
          'al2user' => true,
        ),
      ),
      'intro' => '后台团购券管理页面对团购券进行消费提醒时触发此通知',
    ),
    'admin_mod_login_done' => 
    array (
      'struct' => 'uid,username,password,secques,gender,adminid,regip,regdate,lastip,lastvisit,lastactivity,lastpost,oltime,pageviews,credits,extcredits1,extcredits2,email,bday,sigstatus,tpp,ppp,styleid,dateformat,timeformat,pmsound,showemail,newsletter,invisible,timeoffset,newpm,accessmasks,face,tag_count,role_id,role_type,new_msg_count,tag,own_tags,login_count,truename,phone,last_year_rank,last_month_rank,last_week_rank,this_year_rank,this_month_rank,this_week_rank,last_year_credit,last_month_credit,last_week_credit,this_year_credit,this_month_credit,this_week_credit,view_times,use_tag_count,create_tag_count,image_count,noticenum,ucuid,invite_count,invitecode,province,city,topic_count,at_count,follow_count,fans_count,email2,qq,msn,aboutme,at_new,comment_new,fans_new,topic_favorite_count,tag_favorite_count,disallow_beiguanzhu,validate,favoritemy_new,money,checked,finder,findtime,totalpay,',
      'hook' => 
      array (
        'qqrobot' => 
        array (
          'enabled' => false,
        ),
        'sms' => 
        array (
          'enabled' => false,
        ),
      ),
      'name' => '后台管理登录',
      'intro' => '当后台管理帐号登录时会触发此通知，强烈建议您开启此事件的短信通知，发现非法登录时方便及时修改后台密码，保障网站运营安全',
      'msg' => 
      array (
        'sms' => '帐号：{username} 已经登录后台，请确认！',
      ),
      'cfg' => 
      array (
        'sms' => 
        array (
          'cc2admin' => true,
          'al2user' => false,
        ),
      ),
    ),
    'logic_account_register_done' => 
    array (
      'struct' => 'username,truename,password,phone,email,showemail,role_id,checked,finder,findtime,ucuid,regip,regdate,',
      'name' => '用户注册完成',
      'intro' => '新用户注册时会触发此通知',
      'hook' => 
      array (
        'sms' => 
        array (
          'enabled' => false,
        ),
      ),
      'msg' => 
      array (
        'sms' => '您好，感谢您的注册，天天团购欢迎您的到来！',
      ),
      'cfg' => 
      array (
        'sms' => 
        array (
          'cc2admin' => false,
          'al2user' => true,
        ),
      ),
    ),
    'logic_coupon_Used' => 
    array (
      'struct' => 'productflag,number,time,',
      'name' => '消费券被使用',
      'intro' => '当用户的消费券被商家标记为“已使用”时会触发此通知',
      'msg' => 
      array (
        'sms' => '您好，您的团购券{number}已经被使用！{time}',
      ),
      'cfg' => 
      array (
        'sms' => 
        array (
          'cc2admin' => false,
          'al2user' => true,
        ),
      ),
      'hook' => 
      array (
        'sms' => 
        array (
          'enabled' => true,
        ),
      ),
    ),
    'user_pay_confirm' => 
    array (
      'struct' => 'sign,trade_no,price,money,status,',
      'name' => '等待用户确认付款',
      'intro' => '仅当开启了担保交易接口，并且商品为团购券的时候才会触发此通知',
      'msg' => 
      array (
        'sms' => '您好，本站已开启担保交易接口，为了保障交易的安全，请您先到支付宝确认付款！随后我们会自动为您发送团购券！非常抱歉，感谢您的支持！',
      ),
      'cfg' => 
      array (
        'sms' => 
        array (
          'cc2admin' => false,
          'al2user' => true,
        ),
      ),
      'hook' => 
      array (
        'sms' => 
        array (
          'enabled' => false,
        ),
      ),
    ),
    'list_ask_new' => 
    array (
      'struct' => 'userid,username,content,time,',
      'name' => '问答模块有新问题',
      'intro' => '用户在问答模块提出问题的时候会触发此问题',
      'msg' => 
      array (
        'qqrobot' => '有新的问题：
{content}',
        'sms' => '你好，有人提出了新问题：{content}',
      ),
      'cfg' => 
      array (
        'qqrobot' => 
        array (
          'cc2admin' => true,
          'al2user' => false,
        ),
        'sms' => 
        array (
          'cc2admin' => true,
          'al2user' => false,
        ),
      ),
      'hook' => 
      array (
        'qqrobot' => 
        array (
          'enabled' => false,
        ),
        'sms' => 
        array (
          'enabled' => false,
        ),
      ),
    ),
    'list_ask_reply' => 
    array (
      'struct' => 'id,userid,username,content,reply,time,',
      'name' => '回复用户问题',
      'intro' => '管理员在后台回复用户问题时触发此通知',
      'hook' => 
      array (
        'qqrobot' => 
        array (
          'enabled' => false,
        ),
        'sms' => 
        array (
          'enabled' => false,
        ),
      ),
      'msg' => 
      array (
        'qqrobot' => '您好，管理员回答了您的问题！

{reply}',
        'sms' => '您好，管理员回复您的提问：{reply}',
      ),
      'cfg' => 
      array (
        'qqrobot' => 
        array (
          'cc2admin' => false,
          'al2user' => true,
        ),
        'sms' => 
        array (
          'cc2admin' => false,
          'al2user' => true,
        ),
      ),
    ),
    'user_product_notify' => 
    array (
      'struct' => 'name,flag,begin_date',
      'name' => '用户订阅开团提醒',
      'intro' => '用户如果订阅了某产品，那么该产品在开团前15分钟会提醒订阅的用户',
      'hook' => 
      array (
        'mail' => 
        array (
          'enabled' => true,
        ),
        'sms' => 
        array (
          'enabled' => true,
        ),
      ),
      'msg' => 
      array (
        'mail' => '您订阅的产品{flag}即将开团，开团时间{begin_date}。',
        'sms' => '您订阅的产品{flag}即将开团，开团时间{begin_date}。',
      ),
      'cfg' => 
      array (
        'mail' => 
        array (
          'cc2admin' => false,
          'al2user' => true,
        ),
        'sms' => 
        array (
          'cc2admin' => false,
          'al2user' => true,
        ),
      ),
    ),
  ),
  'adminid' => 1,
  'upcheck' => 
  array (
    'ets' => 
    array (
      'lang' => 
      array (
        'build' => '059b52fa530c2096efad7cd6d52805f4',
      ),
    ),
  ),
);
?>