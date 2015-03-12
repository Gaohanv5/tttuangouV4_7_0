<? include handler('template')->file('@admin/header'); ?>
<?=ui('loader')->css('@jquery.thickbox')?>
<?=ui('loader')->js('@jquery.thickbox')?>
<?=ui('loader')->js('#admin/js/sdb.parser')?>
<div class="header"> <a href="?mod=product&code=vlist" style="float:left; display: block;">产品列表 </a><a href="?mod=product&code=add&~iiframe=yes" class="b_add" style="text-decoration:none;">添加产品 </a><a href="?mod=product&code=linklist&~iiframe=yes" class="b_add" style="text-decoration:none;">套餐管理 </a> </div>
<?=ui('isearcher')->load('admin.product_list')?>
<div class="export_link">
<? if($drfCount) { ?>
[[[ <a href="admin.php?mod=product&code=draft&op=list">草稿(<?=$drfCount?>)</a> ]]]&nbsp;&nbsp;&nbsp;&nbsp;
<? } ?>
<a href="?mod=ui&code=igos&op=config">多团展示方式设置</a> </div> <table id="productTable" cellspacing="1" cellpadding="4" width="100%" align="center" class="tableborder"> <thead> <tr class="tr_nav"> <td width="2%">ID</td> <td width="18%">产品名称（悬浮查看长标题）</td> <td width="12%">销售概览</td> <td width="10%">金额统计</td> <td width="13%">运行状态</td> <td width="10%">显示设定</td> <td width="8%">订阅通知</td> <td width="8%">是否套餐</td> <td width="8%" title="前台最多显示10个推荐团购，已结束的团购不在前台显示，轮播顺序按照产品设置的顺序显示">是否推荐</td> <td width="15%">管理操作</td> </tr> </thead> <tbody> 
<? if(is_array($list)) { foreach($list as $one) { ?>
 <tr> <td><?=$one['id']?></td> <td title="【<?=$one['id']?>】<?=$one['name']?>"> <a href="<?=$this->Config['site_url']?>/?view=<?=$one['id']?>" target="_blank"><?=$one['flag']?></a> </td> <td>
购买：<?=$one['succ_real']?> 人<br/>
售出：<?=$one['sells_real']?> 份<br/>
库存：
<? if($one['maxnum']>0) { ?>
<? echo $one['surplus']>0?($one['surplus'].' 份'):'已售罄'; ?>
<? } else { ?>无限制
<? } ?>
<br/> <b>虚拟</b>：<?=$one['virtualnum']?> 人
</td> <td>
总额：￥<?=$one['mny_all']?><br/>
实收：￥<?=$one['mny_paid']?><br/>
待付：￥<?=$one['mny_waited']?><br/>
退款：￥<?=$one['mny_refund']?>
</td> <td>
<? if($one['begintime'] > time()) { ?>
即将开团，开团时间<br /><? echo date('Y-m-d H:i:s', $one['begintime']); ?>
<? } else { ?><? echo logic('product')->STA_Name($one['status']); ?>
<? if($one['status'] == PRO_STA_Normal) { ?>
<br/><?=$one['succ_total']?> 人成团，差<?=$one['succ_remain']?>人<? } elseif($one['status'] == PRO_STA_Failed) { ?><? } elseif($one['status'] != PRO_STA_Refund) { ?><br/>
<? if($one['type']=='ticket') { ?>
<a href="?mod=coupon&code=vlist&search=pid:<?=$one['id']?>&ssrc=product_name&sstr=<?=$one['flag']?>">[ 查看<?=TUANGOU_STR?>券 ]</a><? } elseif($one['type']=='stuff') { ?><a href="?mod=delivery&code=vlist&alsend=no&search=pid:<?=$one['id']?>&ssrc=product_name&sstr=<?=$one['flag']?>">[ 发货管理 ]</a><? } elseif($one['type']=='prize') { ?><a href="?mod=prize&code=mgr&pid=<?=$one['id']?>">[ 抽奖管理 ]</a>
<? } ?>
<? } ?>
<? } ?>
</td> <td>
<? if($one['display']==PRO_DSP_None) { ?>
不在前台显示<? } elseif($one['display']==PRO_DSP_City) { ?>限定城市显示<br/>(<? echo logic('misc')->City('name', $one['city']); ?>)<? } elseif($one['display']==PRO_DSP_Global) { ?>全部城市显示
<? } ?>
<div style="color:#808080;" title="显示优先级">TOP +<?=$one['order']?></div> </td> <td> <a href="#" title="因通道调整，短信订阅群发功能暂不可用" style="cursor:help;"><img src="templates/default/images/sms_edit.png" /></a>&nbsp;
<a href="?mod=subscribe&code=generate&from=product&idx=<?=$one['id']?>&type=mail&keepThis=true&TB_iframe=true&height=300&width=600" class="thickbox" title="邮件订阅通知"><img src="templates/default/images/email.png" /></a> </td> <td>
<? if($one['linkid']) { ?>
<a href="?mod=product&code=editlink&id=<?=$one['linkid']?>">套餐</a>
<? } else { ?>否
<? } ?>
</td> <td>
<? if(true === admin_priv('producthot')) { ?>
<font class="dbf" src="id:<?=$one['id']?>@product/hotenabled"><?=$one['hotenabled']?></font>
<? } else { ?> <span title="您没有权限进行该操作，请联系管理员。"> -- </span> 
<? } ?>
</td> <td> <a href="?mod=order&code=vlist&search=pid:<?=$one['id']?>&ssrc=product_name&sstr=<?=$one['flag']?>">[ 相关订单 ]</a><br/> <a href="?mod=product&code=edit&id=<?=$one['id']?>&~iiframe=yes">编辑</a>&nbsp;&nbsp;|&nbsp;&nbsp;
<a href="?mod=product&code=del&id=<?=$one['id']?>" onclick="return confirm('删除产品会清空所有相关的订单，<?=TUANGOU_STR?>券，支付，发货等数据！\n此操作不可恢复！\n\n您确定要删除吗？');">删除</a><br/> <a href="?mod=zlog&code=search&type=product&index=<?=$one['id']?>">[ 修改记录 ]</a><br/> <a href="?mod=reports&code=view&service=product&hoster=<?=$one['id']?>">[ 销售报表 ]</a> </td> </tr> 
<? } } ?>
 </tbody> <tfoot> <tr> <td colspan="10"><?=page_moyo()?></td> </tr> </tfoot> </table>
<? include handler('template')->file('@admin/footer'); ?>