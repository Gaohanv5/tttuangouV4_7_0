<? include handler('template')->file('@admin/header'); ?>
 <table cellspacing="1" cellpadding="4" width="100%" align="center" class="tableborder"> 
<? if(admin_priv('upgrade')) { ?>
 <tr class="header"> <td> <div class="NavaL ntj">
程序版本信息
<a href="admin.php?mod=upgrade&code=signup" style="margin:5px;padding:5px 7px;color:#000;background:#BCE3F0;text-decoration:none;font-weight:normal;font-size:10px;">更新授权</a> <font id="wips-status" style="margin:5px;padding:5px 7px;color:#000;background:#DBD29E;text-decoration:none;font-weight:normal;font-size:10px;">WIPS...</font> <font id="apis-status" style="text-decoration:none;font-weight:normal;font-size:10px;">APIS...</font> </div> </td> </tr> <tr> <td>
当前所用版本：V
<?=SYS_VERSION?><?=SYS_RELEASE?>&nbsp;(<?=ini("settings.charset")?>)&nbsp;&nbsp;
[ <span id="ups_alert">正在确定版本状态</span> ] - - <a href="http://tg.tttuangou.net/changelog.htm"><span class="fred">查看更新说明</span><a/> <br/><?=logic('acl')->LicenceDSP()?>
</td> </tr> 
<? } ?>
 <tr class="header"> <td><div class="NavaL nlj"><?=TUANGOU_STR?>常用操作</div></td> </tr> <tr> <td>
1、初期设置：
<a href="admin.php?mod=tttuangou&code=sitelogo">更换Logo</a>&nbsp;&nbsp;|&nbsp;&nbsp;
<a href="admin.php?mod=service&code=sms">短信接口</a>&nbsp;&nbsp;|&nbsp;&nbsp;
<a href="admin.php?mod=service&code=mail">邮件设置</a>&nbsp;&nbsp;|&nbsp;&nbsp;
<a href="admin.php?mod=payment">支付接口</a>&nbsp;&nbsp;|&nbsp;&nbsp;
<a href="admin.php?mod=api">客户端</a> <br/>
2、开团管理：
<a href="admin.php?mod=catalog">添加分类</a>&nbsp;&nbsp;|&nbsp;&nbsp;
<a href="admin.php?mod=express">配送管理</a>&nbsp;&nbsp;|&nbsp;&nbsp;
<a href="?mod=product&code=add&~iiframe=yes">添加产品</a>&nbsp;&nbsp;|&nbsp;&nbsp;
<a href="admin.php?mod=order&code=vlist">订单管理</a>&nbsp;&nbsp;|&nbsp;&nbsp;
<a href="admin.php?mod=coupon&code=vlist"><?=TUANGOU_STR?>券管理</a>&nbsp;&nbsp;|&nbsp;&nbsp;
<a href="admin.php?mod=delivery&code=vlist">发货管理</a>&nbsp;&nbsp;|&nbsp;&nbsp;
</td> </tr> </table> 
<? if($statistic) { ?>
 
<? if(admin_priv('ordermanage')) { ?>
 <table cellspacing="1" cellpadding="4" width="100%" align="center" class="tableborder datacount"> <tr class="header"> <td colspan="6"> <div class="NavaL nkj">今日收益</div> </td> </tr> <tr> <td>今日订单：<b><a href="admin.php?mod=order&code=vlist&iscp_tv_area=order_main&iscp_tvfield_order_main=ordbt&iscp_tvbegin_order_main=<?=$dateYmd?>&iscp_tvfinish_order_main=<?=$dateYmd?>"><?=$statistic['today_orders']?></a></b></td> <td>今日付款订单：<b><a href="admin.php?mod=order&code=vlist&iscp_tv_area=order_main&iscp_tvfield_order_main=ordpt&ordproc=__PAY_YET__&iscp_tvbegin_order_main=<?=$dateYmd?>&iscp_tvfinish_order_main=<?=$dateYmd?>"><?=$statistic['today_pay_orders']?></a></b></td> <td>今日未付款订单：<b><a href="admin.php?mod=order&code=vlist&ordproc=WAIT_BUYER_PAY&iscp_tv_area=order_main&iscp_tvfield_order_main=ordbt&iscp_tvbegin_order_main=<?=$dateYmd?>&iscp_tvfinish_order_main=<?=$dateYmd?>"><?=$statistic['today_unpay_orders']?></a></b></td> <td>今日收益：<b><?=$statistic['today_income_orders']?></b> 
<? if(MEMBER_ID == 1) { ?>
 <a href="admin.php?mod=reports&code=view&service=payment"> (前期收益)</a> 
<? } ?>
 </td> </tr> </table> <p><b><font style="color:#ff0000;">说明：</font>今日付款订单</b>是指在今日完成的付款订单，该订单可能是来源于前几天下的订单，只要在今日付款，都会统计到今日付款订单里。</p> 
<? } ?>
 
<? if(MEMBER_ID == 1) { ?>
 <table cellspacing="1" cellpadding="4" width="100%" align="center" class="tableborder datacount"> <tr class="header"> <td colspan="6"> <div class="NavaL nkj">网站（全站）数据统计</div> </td> </tr> <tr> <td>用户数：<b><a href="<?=$statistic['system_members']['url']?>"><?=$statistic['system_members']['total']?></a></b></td> <td>商家数：<b><a href="<?=$statistic['tttuangou_seller']['url']?>"><?=$statistic['tttuangou_seller']['total']?></a></b></td> <td>城市数：<b><a href="<?=$statistic['tttuangou_city']['url']?>"><?=$statistic['tttuangou_city']['total']?></a></b></td> <td>订阅数：<b><a href="<?=$statistic['tttuangou_subscribe']['url']?>"><?=$statistic['tttuangou_subscribe']['total']?></a></b></td> <td>问答数：<b><a href="<?=$statistic['tttuangou_question']['url']?>"><?=$statistic['tttuangou_question']['total']?></a></b></td> <td>反馈信息：<b><a href="<?=$statistic['tttuangou_usermsg']['url']?>"><?=$statistic['tttuangou_usermsg']['total']?></a></b></td> </tr> <tr> <td>产品数：<b><a href="<?=$statistic['tttuangou_product']['url']?>"><?=$statistic['tttuangou_product']['total']?></a></b></td> <td>订单数：<b><a href="<?=$statistic['tttuangou_order']['url']?>"><?=$statistic['tttuangou_order']['total']?></a></b></td> <td><?=TUANGOU_STR?>券：<b><a href="<?=$statistic['tttuangou_ticket']['url']?>"><?=$statistic['tttuangou_ticket']['total']?></a></b></td> <td>等待发货：<b><a href="<?=$statistic['express_wait_count']['url']?>"><?=$statistic['express_wait_count']['total']?></a></b></td> <td>邮件队列：<b><a href="<?=$statistic['cron_length']['url']?>"><?=$statistic['cron_length']['total']?></a></b></td> <td>数据库：<b><a href="<?=$statistic['data_length']['url']?>"><?=$statistic['data_length']['total']?></a></b></td> </tr> </table> 
<? } ?>
 
<? } ?>
 <table cellspacing="1" cellpadding="4" width="100%" align="center" class="tableborder" id="recommend_tabler" style="display: none;"> <tr class="header"> <td colspan="12"> <div class="NavaL ndt">天天团购官方动态</div> </td> </tr> <tr> <td id="recommend">正在载入中...</td> </tr> </table> <table cellspacing="1" cellpadding="4" width="100%" align="center" class="tableborder"> <tr class="header"> <td colspan="2"> <div class="NavaL ntj">相关系统推荐</div> </td> </tr> <tr> <td><A HREF="http://biniu.com" title="比牛，让业务牛逼起来" target=_blank>比牛：微信移动营销平台</A></td> <td><A HREF="<?=ihelper('jsg.tg.admin')?>" target=_blank title="创新的开源微博系统，兼有sns、轻博和bbs特点">记事狗：开源微博系统</A></td> </tr> </table> 
<? if($check_upgrade) { ?>
 <script language="JavaScript" type="text/javascript" src="admin.php?mod=upgrade&code=check&js=1"></script> 
<? } ?>
 <script type="text/javascript">
$(document).ready(function()
{
$.get('admin.php?mod=index&code=recommend', function(data)
{
if (data != '')
{
$('#recommend_tabler').show();
$('#recommend').html(data);
}
});
$.get('admin.php?mod=index&code=upgrade_check', function(data){
if (data != 'noups')
{
$('#ups_alert').html(''+data+' &gt;&gt;&gt; <a href="admin.php?mod=upgrade"><font id="ups_alert_light" style="color:red;font-weight:bold;font-size:13px;">点此进行在线升级</font></a>');
}
else
{
$('#ups_alert').html('已是最新版本');
}
});
if (typeof(lrcmd) != 'undefined' && typeof(lrcmd) == 'string')
{
$.get('admin.php?mod=index&code=lrcmd_nt&lv='+lrcmd, function(data){
if (data != 'false')
{
$('#lic_recommend').html(data).slideDown();
}
});
}
$.get('admin.php?mod=wips&code=status&op=ajax&stamp=<? echo time(); ?>', function(html){
$('#wips-status').html(html);
});
$.get('admin.php?mod=upgrade_api&code=check&op=ajax&stamp=<? echo time(); ?>', function(html){
$('#apis-status').html(html);
});
});
</script>
<? include handler('template')->file('@admin/footer'); ?>