<? include handler('template')->file('@admin/header'); ?>
 <script type="text/javascript">
var __Global_PID = "<?=$p['id']?>";
var __draft_ID = "<?=$did?>";
var __exists_Draft_ID = "<?=$draft['id']?>";
</script>
<?=ui('loader')->addon('picker.date')?>
<?=ui('loader')->js('@jquery.hook')?>
<?=ui('loader')->js('@jquery.idTabs')?>
<?=ui('loader')->css('#admin/css/product.mgr.idTabs')?>
<?=ui('loader')->js('@jquery.form')?>
<?=ui('loader')->js('@jquery.thickbox')?>
<?=ui('loader')->css('@jquery.thickbox')?>
<?=ui('loader')->js('#admin/js/product.mgr')?>
<?=ui('loader')->css('#admin/css/product.mgr')?>
<?=ui('loader')->js('#admin/js/product.mgr.autoSave')?>
<?=ui('loader')->js('#admin/js/wizard.processer')?>
<?=ui('loader')->js('#admin/js/attrs.mgr')?>
<?=logic('upload')->html('editor_create_product_mgr')?>
<style>.ifamenav{ display:none;}</style> <form id="productIfoForm" action="?mod=product&code=save" method="post"  enctype="multipart/form-data" onsubmit="return checkIfClick();">
<input type="hidden" name="FORMHASH" value='<?=FORMHASH?>'/> <input id="productID" name="id" type="hidden" value="<?=$p['id']?>" /> <div class="idTabs"> <div class="navsBar"> <font style="float: left;margin-right: 20px;margin-top: 7px;">
<? if($id) { ?>
编辑产品
<? } else { ?>添加产品
<? } ?>
[ <a href="?#wizard" onclick="wizProcessStart();return false;">使用向导</a> ]
</font> <ul class="navs"> <li><a id="nav2Base" href="#p_base">1.基本信息</a></li> <li><a id="nav2Type" href="#p_type">2.产品设置</a></li> <li><a id="nav2Intro" href="#p_intro">3.详情介绍</a></li> <li><a id="nav2Extend" href="#p_extend">4.其他设置</a></li> </ul> <font id="autoSaveStatus" style="float: right;margin-top: 7px;margin-right: 40px;_margin-right: 10px;"></font> </div> <div class="items"> <div id="p_base"> <table width="100%" class="tableborder"> <tr> <td width="100" class="td_title">产品标题：</td> <td> <input id="productName" name="name" type="text" size="80" value="<?=$p['name']?>" />
（吸引眼球第一要素）
</td> </tr> <tr> <td width="100" class="td_title">简短名称：</td> <td> <input style="width:275px;" id="productFlag" name="flag" type="text" value="<?=$p['flag']?>" maxlength="20" /> <font color="red">重要：一定要突出产品名称，价格等信息</font><br>（用于除首页外其他地方显示，比如<A HREF="admin.php?mod=service&code=sms" target=_blank>短信发送</A>通知中，团购卷验证页面）
</td> </tr> <tr> <td width="100" class="td_title">产品简介：</td> <td> <textarea name="intro" class="editor" style="width:500px;height:60px;"><?=$p['intro']?></textarea> <br/>建议字数在100以内
</td> </tr> <tr> <td width="150" class="td_title">产品多图片展示：<br/><font style="font-weight:bold;color:red;">图片尺寸：450*268</font></td> <td> <input type="hidden" name="imgs" id="imgs" value="" /> <ul class="img_scroll">
<? if(is_array($p['imgs'])) { foreach($p['imgs'] as $id) { ?>
<? if($id > 0) { ?>
<li id="img_li_for_<?=$id?>"> <a href="<? echo imager($id, IMG_Original); ?>" class="thickbox"><img class="pro_image_tiny" src="<? echo imager($id, IMG_Tiny); ?>" /></a> <input type="text" value="<? echo logic('upload')->Field($id, 'intro'); ?>" onfocus="introFocus(this)" onblur="introChange(<?=$id?>, this)" /> <a href="?#deleteImage" onclick="DeleteImage(<?=$id?>);return false;">[ 删除 ]</a> </li>
<? } ?>
<? } } ?>
<li id="img_li_TPL"> <a href="#http://[url]/" target="_blank"><img src="#http://[url]/" width="80" height="80" /></a> <input type="text" value="" onfocus="introFocus(this)" onblur="introChange([id], this)" /> <a href="?#deleteImage" onclick="DeleteImage([id]);return false;">[ 删除 ]</a> </li> </ul>
<?=logic('upload')->html()?>
*点击删除图片时会实时删除服务器上的图片，请慎重
<br><font color="red">*如果提示 Acces Deined 错误，请检查“全局设置-上传设置”里面对应的图片上传权限是否有打开</font> </td> </tr> </table> <table width="100%" class="tableborder" > 
<? if(MEMBER_ROLE_TYPE == 'admin') { ?>
 <tr> <td width="100" class="td_title">投放城市：</td> <td> <div id="fillIfoCity"> <select name="city" id="allCityList"> <option value="-1">正在加载</option> </select>
&nbsp;&nbsp;&nbsp;&nbsp;<a href="?#addCity" onclick="proIfoAddCity();return false;">[添加城市]</a> </div> <div id="OPBox_addCity" style="display: none;">
城市中文名称：<input id="opb_addcity_name" type="text" /><br/>
城市拼音名称：<input id="opb_addcity_flag" type="text" /> </div> </td> </tr> 
<? } else { ?> <input type="hidden" name="city" value="<?=$p['city']?>"> 
<? } ?>

<? if(logic('city')->place_enabled()) { ?>
<tr> <td width="100" class="td_title">区域归属：</td> <td>
<? logic('city')->place_inputer($p['id']) ?>
</td> </tr>
<? } ?>

<? if(MEMBER_ROLE_TYPE == 'admin') { ?>
 <tr> <td width="100" class="td_title">合作商家：</td> <td> <div id="fillIfoSeller"> <span id='allSellerList'>
请先选择城市
</span>
&nbsp;&nbsp;&nbsp;&nbsp;<a href="?#addSeller" onclick="proIfoAddSeller();return false;">[添加商家]</a> </div> <div id="OPBox_addSeller" style="display: none;">
商家用户：<input id="opb_addseller_username" type="text" />（<a href="?#helper" onclick="ifoShowHelper('addSeller');return false;">帮助</a>）<br/>
商家名称：<input id="opb_addseller_sellername" size="50" type="text" /><br/><font style="float:right;">（详细信息请到<a href="admin.php?mod=tttuangou&code=mainseller" target="_blank">商家管理页面</a>补充）</font> </div> <div id="helper_of_addSeller" style="display: none;">
1. 商家用户指的是商户登录本系统时使用的帐号，属于注册会员<br/>
2. 如果您输入一个不存在的用户，系统会自动注册，<b>默认密码123456</b>，添加完成后请尽快修改密码！<br/>
3. 如果您输入的是一个已经存在的用户名，则此用户必须身份为合作商家，并且没有绑定到其他商家
</div> </td> </tr> 
<? } else { ?> <input type="hidden" name="sellerid" value="<?=$p['sellerid']?>" id="sellerid"> 
<? } ?>
 <script type="text/javascript">var __Default_CityID = "<?=$p['city']?>";var __Default_SellerID = "<?=$p['sellerid']?>";</script> <tr> <td width="100" class="td_title">显示方式：</td> <td> <div id="fillIfoDisplay"> <select name="display"> <option value="<?=PRO_DSP_None?>"
<? if($p['display']==PRO_DSP_None) { ?>
 selected="selected"
<? } ?>
>不在前台显示</option> <option value="<?=PRO_DSP_City?>"
<? if($p['display']==PRO_DSP_City) { ?>
 selected="selected"
<? } ?>
>在指定城市显示</option> <option value="<?=PRO_DSP_Global?>"
<? if($p['display']==PRO_DSP_Global) { ?>
 selected="selected"
<? } ?>
>在全部城市显示</option> </select> </div> </td> </tr> <tr> <td width="100" class="td_title">显示优先级：</td> <td> <input name="order" type="text" size="3" value="<? echo (int)$p['order']; ?>" />&nbsp;&nbsp;（数字越大，显示位置越靠前，用于同时<?=TUANGOU_STR?>多个产品的时候）
</td> </tr> </table> </div> <div id="p_intro"> <table width="100%" class="tableborder" > <tr> <td width="100" class="td_title">购买须知：<br/><font color="#999">*留空前台不显示</font></td> <td> <textarea name="cue" class="editor" style="width:600px;"><?=$p['cue']?></textarea> </td> </tr> <tr class="live_height"> <td width="100" class="td_title">本单详情：</td> <td> <textarea name="content" class="editor" style="width:600px;"><?=$p['content']?></textarea> </td> </tr> <tr> <td width="100" class="td_title">他们说：<br/>引用第三方看法<br/><font color="#999">*留空前台不显示</font></td> <td> <textarea name="theysay" class="editor" style="width:600px;"><?=$p['theysay']?></textarea> </td> </tr> <tr> <td width="100" class="td_title">我们说：<br/>官方自己的说法<br/><font color="#999">*留空前台不显示</font></td> <td> <textarea name="wesay" class="editor" style="width:600px;"><?=$p['wesay']?></textarea> </td> </tr> </table> <script>
$(document).ready(function(){
$(".live_height .ke-edit").css("height","300px");
});
</script> </div> <div id="p_type"> <table width="100%" class="tableborder"> <tr> <td width="160" class="td_title">原价：</td> <td> <input id="productPrice" name="price" type="text" size="6" value="<? echo (float)$p['price']; ?>" />&nbsp;&nbsp;元
</td> </tr> <tr> <td width="160" class="td_title"> <font id="price_dsp_presell" <? echo $p['presell'] ? '' : 'style="display:none;"'; ?>>预付价：</font> <font id="price_dsp_normal" <? echo $p['presell'] ? 'style="display:none;"' : ''; ?>><?=TUANGOU_STR?>价：</font> </td> <td> <input id="productNowPrice" name="nowprice" type="text" size="6" value="<? echo (float)$p['nowprice']; ?>" />&nbsp;&nbsp;元  <span style="color:red;">&nbsp;&nbsp;注意：修改此价格，会导致前台“商家管理”有效交易额统计出错</span>（如果启用下面的预付模式，此处将变更为预付价）
</td> </tr> <tr> <td width="160" class="td_title">商家结算价：</td> <td> <input id="FundPrice" name="fundprice" type="text" size="6" value="<? echo $p['fundprice']<0 ? '' : (float)$p['fundprice']; ?>" />&nbsp;&nbsp;元（如果设置了该价格，则按照该价格与商家进行结算；比如设置5.25元，则每单分给商家5.25元；）<br>
（注意：【20140515更新】如果填写0则代表不给商家分成；如果留空，则按照该商家与站长的分成比例与商家进行结算。(商家与站长的分成比例可通过“商家管理>>修改商家”进行设置)）
</td> </tr> <tr> <td width="160" class="td_title">赠送积分：</td> <td> <input id="Score" name="score" type="text" size="6" value="<?=$p['score']?>" />&nbsp;&nbsp;分（如果设置了该分数值，则买家在成功购买了该产品后，会获得相应积分）
</td> </tr> <tr> <td width="160" class="td_title">
启用预付模式？<br> <a href="/#presell/help" onclick="show_presell_help();return false;">查看使用帮助</a> </td> <td> <label><input id="presellCheked" name="presell_is" type="checkbox" value="yes" 
<? if($p['presell']) { ?>
checked="checked"
<? } ?>
 onclick="price_presell_dsp_update()" />启用预付</label>（启用后下面的设置才会生效）
<br> <label>预付价的显示名称：<input id="presellText" name="presell_text" type="text" size="6" value="<? echo $p['presell']['text']; ?>" />（比如：预付、定金、在线支付）</label><br> <label><?=TUANGOU_STR?>价：<input id="presellPrice" name="presell_price" type="text" size="6" value="<? echo $p['presell']['price_full']; ?>" />&nbsp;&nbsp;元（启用预付模式后，需在此填写一个<?=TUANGOU_STR?>价）</label> </td> </tr> <script type="text/javascript">
function price_presell_dsp_update() {
if ($('#presellCheked').attr('checked')) {
$('#price_dsp_presell').show();
$('#price_dsp_normal').hide();
} else {
$('#price_dsp_presell').hide();
$('#price_dsp_normal').show();
}
}
function show_presell_help()
{
art.dialog({
title: '帮助手册',
icon: 'question',
lock: true,
content: '启用预付模式后，用户只需要在网上支付预付价的金额，收到<?=TUANGOU_STR?>券到店验证后再<b>在店内</b>支付（<?=TUANGOU_STR?>价-预付价）的金额，一般用在大额商品预订上（如汽车预订等）<br/><br/>填写说明：<br/>1、勾选“启用预付”后，上方的“<?=TUANGOU_STR?>价”会变成“预付价”，这是用户需要在网上支付的金额<br/>2、“预付价的显示名称”和“<?=TUANGOU_STR?>价格”仅供在前台显示之用（不参与订单结算）<br/>3、示例：预付价：10元，<?=TUANGOU_STR?>价格：100元；前台会显示：商品原价xx元，<?=TUANGOU_STR?>价100元，预付价10元',
yesText: '知道了',
yesFn: true
});
}
</script> <tr> <td width="160" class="td_title"><?=TUANGOU_STR?>开始时间：</td> <td> <input type="text" onfocus="WdatePicker({isShowClear:false,readOnly:true,dateFmt:'yyyy年MM月dd日 HH时mm分ss秒',startDate:'<? echo date('Y-m-d H:i:s', $p['begintime'] ? $p['begintime'] : time());; ?>',vel:'begintime'})" size="35" class="Wdate" value="<? echo date('Y年m月d日 H时i分s秒', $p['begintime'] ? $p['begintime'] : time());; ?>" /> <input name="begintime" type="hidden" id="begintime" value="<? echo date('Y-m-d H:i:s', $p['begintime'] ? $p['begintime'] : time());; ?>" />
到了<?=TUANGOU_STR?>开始时间，产品才会在首页显示，同一时间支持多个<?=TUANGOU_STR?>产品；
</td> </tr> <tr> <td width="160" class="td_title"><?=TUANGOU_STR?>结束时间：</td> <td> <input type="text" onfocus="WdatePicker({isShowClear:false,readOnly:true,dateFmt:'yyyy年MM月dd日 HH时mm分ss秒',startDate:'<? echo date('Y-m-d H:i:s', $p['overtime'] ? $p['overtime'] : time()+86400);; ?>',vel:'overtime'})" size="35" class="Wdate" value="<? echo date('Y年m月d日 H时i分s秒', $p['overtime'] ? $p['overtime'] : time()+86400);; ?>" /> <input name="overtime" type="hidden" id="overtime" value="<? echo date('Y-m-d H:i:s', $p['overtime'] ? $p['overtime'] : time()+86400);; ?>" />
超过<?=TUANGOU_STR?>结束时间，会结束<?=TUANGOU_STR?>，并显示在往期<?=TUANGOU_STR?>中
</td> </tr> <tr> <td width="160" class="td_title">限时购模式：</td> <td> <input type="checkbox" name="is_countdown" value="1" 
<? if($p['is_countdown']) { ?>
checked="checked"
<? } ?>
/>
用户下单后必须在规定时间内完成付款
</td> </tr> </table> <table width="100%" class="tableborder"> <tr> <td width="160" class="td_title">产品分类：</td> <td>
<? if(logic('catalog')->Enabled()) { ?>
<?=ui('loader')->js('#html/catalog/catalog.mgr.ajax')?>
<? ui('catalog')->inputer($p['category']) ?>
<? } else { ?>如需开启，请<A HREF="admin.php?mod=catalog" target=_blank>点此设置</A>。注意：刷新本页面前，请先提交保存。
<? } ?>
</td> </tr> <tr> <td width="160" class="td_title"><?=TUANGOU_STR?>类型：</td> <td> <select name="type" onchange="product_type_show(this)" id="product_type_sel">
<? if($p['type']!='prize') { ?>
<option value="ticket"
<? if($p['type']=='ticket') { ?>
 selected="selected"
<? } ?>
><?=TUANGOU_STR?>券</option> <option value="stuff"
<? if($p['type']=='stuff') { ?>
 selected="selected"
<? } ?>
>实物</option>
<? } ?>
<? if($p['type']=='prize' || $p['type']=='') { ?>
<option value="prize">抽奖</option>
<? } ?>
</select>（如是<?=TUANGOU_STR?>券，那么将通过<A HREF="admin.php?mod=service&code=sms" target=_blank>短信</A>发送；如果是实物，则通过<A HREF="admin.php?mod=express" target=_blank>快递配送</A>）<b>(注：抽奖项目保存后此项不可再修改)</b> </td> </tr> <script type="text/javascript">
function product_type_show(sel)
{
var type = sel.options[sel.options.selectedIndex].value;
if (type == 'ticket')
{
$('.displayer_of_ticket').show();
$('.displayer_of_stuff').hide();
}
else if (type == 'stuff')
{
$('.displayer_of_ticket').hide();
$('.displayer_of_stuff').show();
}
else if (type == 'prize')
{
$('.displayer_of_ticket').hide();
$('.displayer_of_stuff').hide();
}
}
$(document).ready(function(){
product_type_show(document.getElementById('product_type_sel'));
});
</script> <tr class="displayer_of_ticket" 
<? if($p['type']!='ticket') { ?>
style="display:none;"
<? } ?>
> <td width="160" class="td_title"><?=TUANGOU_STR?>券有效期至：</td> <td> <input type="text" onfocus="WdatePicker({isShowClear:false,readOnly:true,dateFmt:'yyyy年MM月dd日 HH时mm分ss秒',startDate:'<? echo date('Y-m-d H:i:s', $p['perioddate'] ? $p['perioddate'] : time()+604800);; ?>',vel:'perioddate'})" size="35" class="Wdate" value="<? echo date('Y年m月d日 H时i分s秒', $p['perioddate'] ? $p['perioddate'] : time()+604800);; ?>" /> <input name="perioddate" type="hidden" id="perioddate" value="<? echo date('Y-m-d H:i:s', $p['perioddate'] ? $p['perioddate'] : time()+604800);; ?>" /> </td> </tr> <tr class="displayer_of_ticket" 
<? if($p['type']!='ticket') { ?>
style="display:none;"
<? } ?>
> <td width="160" class="td_title">多券合一：</td> <td> <label><input name="allinone" type="radio" value="true"
<? if($p['allinone']=='true') { ?>
 checked="checked"
<? } ?>
/> 是</label> <label><input name="allinone" type="radio" value="false"
<? if($p['allinone']=='false' || !$p['allinone']) { ?>
 checked="checked"
<? } ?>
/> 否</label>
&nbsp;&nbsp;（如选是，那么即使用户团购了多份产品，也只发放一张<?=TUANGOU_STR?>券）<BR>
提醒：如果每份产品需要分不同时间消费，请务必选择“否”。
</td> </tr> <tr class="displayer_of_stuff" 
<? if($p['type']!='stuff') { ?>
style="display:none;"
<? } ?>
> <td width="160" class="td_title">每份重量：</td> <td> <input name="weight" type="text" value="<?=$p['weight']?>" /> <select name="weightunit"> <option value="g"
<? if($p['weightunit']=='g') { ?>
 selected="selected"
<? } ?>
>克</option> <option value="kg"
<? if($p['weightunit']=='kg') { ?>
 selected="selected"
<? } ?>
>千克</option> </select>（系统会根据<a href="admin.php?mod=express" target=_blank>配送管理</a>中的设置，自动按重量、按城市计算用户的运费）
</td> </tr> <tr class="displayer_of_stuff" 
<? if($p['type']!='stuff') { ?>
style="display:none;"
<? } ?>
> <td width="160" class="td_title">指定配送方式：</td> <td> <input name="expresslist" type="text" value="<? echo meta('expresslist_of_'.$p['id']); ?>" />
如需指定，请填写配送方式的<font color="red">编号</font>（<a href="admin.php?mod=express" target="_blank">点此查看</a>）；<BR>
如填写多个编号，<font color="red">请务必用英文逗号间隔（例如：1,2）</font>；如果留空，则用户可选择全部配送方式。
</td> </tr> </table> <table width="100%" class="tableborder"> <tr> <td width="160" class="td_title">成功<?=TUANGOU_STR?>人数要求：</td> <td> <input name="successnum" type="text" id="successnum" value="<? echo (int)$p['successnum']; ?>" />
&nbsp;&nbsp;最少需要多少人购买才算<?=TUANGOU_STR?>成功。注意：如不是实物，那只有达到<?=TUANGOU_STR?>人数要求才会生成<?=TUANGOU_STR?>券并发送短信。
</td> </tr> <tr> <td width="160" class="td_title">虚拟已购买人数：</td> <td> <input name="virtualnum" type="text" id="virtualnum" value="<? echo (int)$p['virtualnum']; ?>" 
<? if($p['type']=='prize') { ?>
readonly="readonly"
<? } ?>
/>
&nbsp;&nbsp;前台购买人数会显示[ 虚拟购买人数+真实购买人数 ] <b>(注：抽奖项目保存后此项不可再修改)</b> </td> </tr> <tr> <td width="160" class="td_title">产品总数量：</td> <td> <input name="maxnum" type="text" value="<? echo (int)$p['maxnum']; ?>" /> <span style="color:red;">&nbsp;&nbsp;*0表示不限制，否则产品会出现“已卖光”状态</span> </td> </tr> <tr> <td width="160" class="td_title">一次最多购买数量：</td> <td> <input name="oncemax" type="text" id="oncemax" value="<? echo (int)$p['oncemax']; ?>" /> <span style="color:red;">&nbsp;&nbsp;*0表示不限制</span> </td> </tr> <tr> <td width="160" class="td_title">一次最少购买数量：</td> <td> <input name="oncemin" type="text" id="oncemin" value="<? echo (int)$p['oncemin']; ?>" /> <span style="color:red;">&nbsp;&nbsp;*购买数量低于此设定的不允许参团</span> </td> </tr> </table> </div> <div id="p_limit"> </div> <div id="p_extend"> <table width="100%" class="tableborder"> <tr> <td width="160" class="td_title">是否允许多次购买：</td> <td> <label><input name="multibuy" type="radio" value="true"
<? if($p['multibuy']=='true') { ?>
 checked="checked"
<? } ?>
/> 是</label>
&nbsp;&nbsp;&nbsp;
<label><input name="multibuy" type="radio" value="false"
<? if($p['multibuy']=='false' || !$p['multibuy']) { ?>
 checked="checked"
<? } ?>
/> 否</label> </td> </tr> <tr> <td width="160" class="td_title">是否隐藏商家信息：</td> <td> <label><input name="hideseller" type="radio" value="true"
<? if(meta('p_hs_'.$p['id'])) { ?>
 checked="checked"
<? } ?>
/> 是</label>
&nbsp;&nbsp;&nbsp;
<label><input name="hideseller" type="radio" value="false"
<? if(!meta('p_hs_'.$p['id'])) { ?>
 checked="checked"
<? } ?>
/> 否</label>
（如选择是，那产品详情页面将不显示商家名称、地图、电话等）
</td> </tr> <tr> <td width="160" class="td_title">是否参与邀请返利：</td> <td> <label><input name="irebates" type="radio" value="true"
<? if(meta('p_ir_'.$p['id'])) { ?>
 checked="checked"
<? } ?>
/> 是</label>
&nbsp;&nbsp;&nbsp;
<label><input name="irebates" type="radio" value="false"
<? if(!meta('p_ir_'.$p['id'])) { ?>
 checked="checked"
<? } ?>
/> 否</label> </td> </tr> <tr> <td width="160" class="td_title">产品标签设置：<br><a href="?#product_tag" onclick="product_tag_mgr('<?=$p['id']?>');return false;">添加/管理标签</a></td> <td> <div id="product_tag_view"></div> </td> </tr> <tr id="attrs-append-pox"> <td width="130" class="td_title cat-name"> <a href="?cat-create" onclick="attrs_append_cat('@auto', '<?=$p['id']?>');return false;">添加属性分类</a> </td> <td> <font style="color:#999;">属性分类指的是规格的分类归属，比如黄色，蓝色都属于颜色，这里可以添加一个属性分类“颜色”，然后再创建属性标签“蓝色”</font> <div id="attrs-form-area"></div> </td> </tr> <script type="text/template" id="attr-tpl-cat-fw"> <tr id="attrs-area-!!catId!!" class="attrs-item"> <td class="td_title cat-name"> <font id="attrs-cat-name-!!catId!!">!!catName!!</font> <br/> <a href="#cat-modify" onclick="attrs_modify_cat('!!ops!!', '!!catId!!');return false;">编辑</a> | <a href="#cat-delete" onclick="attrs_remove_cat('!!ops!!', '!!catId!!');return false;">删除</a> </td> <td> <div id="attrs-ops-pox-!!catId!!"></div> <input type="button" value="添加" onclick="attrs_append_item('!!ops!!', '!!catId!!')" style="border:1px solid red;cursor:pointer;" /> </td> </tr> </script> <script type="text/template" id="attr-tpl-item-value"> <input id="attrs-item-!!attrId!!" class="atttr-value" type="button" value="!!attrName!!" onclick="attrs_modify_item('!!ops!!', '!!attrId!!')" title="管理此属性标签" style="cursor:pointer;" /> </script> <script type="text/template" id="attr-tpl-dialog-cat">
属性分类名称：<input id="attrs-input-cat-name" type="text" value="!!catName!!" /><br/>
是否必须选择：<select id="attrs-input-cat-required"><option value="true" !!selected-true!!>是</option><option value="false" !!selected-false!!>否</option></select> <a href="#attr-cat-help" onclick="attrs_cat_help();return false;">帮助</a> </script> <script type="text/template" id="attr-tpl-dialog-item">
属性标签名称：<input id="attrs-input-item-name" type="text" value="!!attrName!!" /> <a href="#attr-remove" onclick="attrs_remove_item('!!ops!!', '!!attrId!!');return false;">删除</a> <br/>
价格变动数值：<input id="attrs-input-item-pricemoves" type="text" value="!!priceMoves!!" /> <a href="#attr-help" onclick="attrs_help();return false;">帮助</a> <br/>
是否绑定数量：<select id="attrs-input-item-binding"><option value="true" !!selected-true!!>是</option><option value="false" !!selected-false!!>否</option></select> </script> <script type="text/template" id="attr-tpl-help">
价格变动数值是指用户选择了此属性标签后的订单总价变动值，支持负数<br/> <br/>例如：<br/>
1、标签填写“加10元送充电器”，数值填写“10”，订单总价50，选择标签后，订单总价为60元<br/>
2、标签填写“下单立减20元”，数值填写“-20”，订单总价100，选择标签后，订单总价为80元<br/>
3、如果绑定了数量后，价格变动数值会自动关联计算用户的购买数量
<br/>注意：<br/>
同一个分类下的多个标签用户只能选择其中一个
</script> <script type="text/template" id="attr-cat-tpl-help">
如果设定了“必须选择”后，用户必须选择此分类中的某一个标签才能继续完成下单操作
</script> <script type="text/javascript">
$(document).ready(function() {
attrs_load_data('<?=$p['id']?>');
load_product_tag('<?=$p['id']?>');
});
</script> </table> <table width="100%" class="tableborder"> <tr> <td width="160" class="td_title">支付设置：</td> <td> <label><input name="specialPayment" type="radio" value="false"
<? if(!meta('paymentlist_of_'.$p['id'])) { ?>
 checked="checked"
<? } ?>
 onclick="dsp_payment_list(false)" /> 使用统一的支付方式</label>
&nbsp;&nbsp;&nbsp;
<label><input name="specialPayment" type="radio" value="true"
<? if(meta('paymentlist_of_'.$p['id'])) { ?>
 checked="checked"
<? } ?>
 onclick="dsp_payment_list(true)" /> 使用特定的支付方式</label> </td> </tr> <tr id="dsp_payment_list"
<? if(!meta('paymentlist_of_'.$p['id'])) { ?>
style="display:none;"
<? } ?>
> <td class="td_title">可选支付方式：</td> <td>
<? $listString = meta('paymentlist_of_'.$p['id']) ?>
<? if(is_array(logic('pay')->GetList())) { foreach(logic('pay')->GetList() as $i => $pay) { ?>
<? if($pay['code'] == 'recharge') { ?>
<? continue ?>
<? } ?>
<label><input name="specialPaymentSel[]" type="checkbox" value="<?=$pay['code']?>" 
<? if(stristr($listString, $pay['code'].',')) { ?>
checked="checked"
<? } ?>
 />&nbsp;&nbsp;<?=$pay['name']?></label><br/>
<? } } ?>
<hr style="border:1px dashed #ccc;margin: 5px auto;"/>
注意：此处已选的支付方式在某些情况下前台未必会显示（<a href="?#helper" onclick="ifoShowHelper('paymentSel');return false;">详细说明</a>）
<div id="helper_of_paymentSel" style="display:none;">
1、余额少于需支付费用时不会显示“余额付款”<br/>
2、<?=TUANGOU_STR?>类型非实物时不会显示“货到付款”<br/>
3、产品价格为0时，前台只会显示“余额付款”，如果此处没勾选，用户将无法继续下单
</div> </td> </tr> </table> <table width="100%" class="tableborder"> <tr> <td width="160" class="td_title">通知方式：</td> <td>
<? $ntc = meta('p_nt_'.$p['id']) ?>
<select name="notifyType"> <option value="-1" 
<? if(!$ntc) { ?>
selected="selected"
<? } ?>
>默认方式</option>
<? $nlist = ini('notify.api') ?>
<? if(is_array($nlist)) { foreach($nlist as $k => $d) { ?>
<? if($d['enabled']) { ?>
<option value="<?=$k?>" 
<? if($ntc == $k) { ?>
selected="selected"
<? } ?>
><?=$d['name']?></option>
<? } ?>
<? } } ?>
</select>
（<?=TUANGOU_STR?>券生成时，并且需要在“通知事件管理”处启用方可生效）
</td> </tr> </table> 
<? if($p['linkid']) { ?>
 <table width="100%" class="tableborder"> <tr> <td width="160" class="td_title">包含套餐：</td> <td> 
<? if(is_array($p['product_link']['products'])) { foreach($p['product_link']['products'] as $pls) { ?>
<? if($p['id'] == $pls['pid']) { ?>
<span style="margin:0px 10px;padding:5px 10px;border:1px solid #888;background-color:#ddd;"><?=$pls['name']?></span>
<? } else { ?><span style="margin:0px 10px;padding:5px 10px;border:1px solid #888;"><a href="?mod=product&code=edit&id=<?=$pls['pid']?>" title="<?=$pls['product_name']?>"><?=$pls['name']?></a></span>
<? } ?>
<? } } ?>
 </td> </tr> </table> 
<? } ?>
 </div> </div> </div> <div class="submitArea"> <p style="text-align:center;margin-bottom:5px;">完成4步设置后，再点击保存即可</p> <input id="submitButton" type="submit" class="button back2" value="保存" /> </div> </form> <div id="OPBox_productTag" style="display:none;">
<? logic('tag')->html($p['id']); ?>
</div>
<? include handler('template')->file('@admin/footer'); ?>