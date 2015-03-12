<? include handler('template')->file('header'); ?>
<?=ui('loader')->js('@time.lesser')?>
<style>
.site-mast__user-nav,.site-mast__branding,.site-ms,.site-mast__branding, .site-mast__site-nav,.wrap {width: 980px;}
.deal_component{ width:938px;}
.deal-tile__c{ width:468px;}
.site-ms__left{ width:760px;} 
.content-navbar{ width:758px;}
.search-box { margin-left:130px;width: 321px;}
.search-box .search-box__input{ width:158px;}
.deal-tile__cover .smallslider li a img{width:450px;}
</style>
<div class="bread-nav">
<? ui('catalog')->tree($product['category']) ?>
</div>    
<div class="deal_component">
<h3><?=$product['name']?><p><?=$product['intro']?></p></h3>                  
<div class="deal-tile__cover" style="float:left;">
<div class="deal-tile__cover_img">
<? ui('iimager')->multis($product['id'], $product['imgs']) ?>
</div>
<div class="deal-tile__cover_tag">
<? if($product['linkid']) { ?>
<i><span title="多个套餐">多种套餐</span></i>
<? } ?>
<? logic('product_tag')->html($product['id']) ?>
</div>
</div>            
<div class="deal-tile__c">                
<div class="price">
<? if($product['presell']) { ?>
<b> <?=$product['presell']['text']?></b>
<? } ?>
<em>&yen;<?=$product['nowprice']?> </em>
<sub>&yen;<?=$product['price']?></sub>
<? if($product['presell']) { ?>
<i><?=TUANGOU_STR?>价：&yen;<?=$product['presell']['price_full']?>【<?=$product['presell']['text']?>：&yen;<?=$product['nowprice']?>】</i>
<? } else { ?><span><?=$product['discount']?>折</span>
<? } ?>
</div>   
<? $product['num'] = $product['maxnum'] == 0 ? 999 : (logic('product')->Surplus($product['maxnum'], logic('product')->SellsCount($product['id']))); ?>
<div class="roduct-stars B">
<? if($product['type'] == 'prize') { ?>
<div class="product-stars">
<em><? echo logic('prize')->allCount($product['id']); ?></em>人已参加抽奖
<? if($product['time_remain'] < 0) { ?>
<? if(logic('prize')->PrizeWIN($product['id'])) { ?>
已开奖，<a href="?mod=prize&code=view&pid=<?=$product['id']?>">查看中奖号码</a>
<? } else { ?>未开奖
<? } ?>
<? } ?>
</div><? } elseif(!$product['begin_date'] && !$product['limit_time']) { ?><div class="product-stars">
<? if($product['is_countdown'] == 1) { ?>
<? if($product['maxnum'] > 0) { ?>
<? if($product['num'] <1) { ?>
已售罄，
<? } else { ?>剩余<em><?=$product['num']?></em>，
<? } ?>
<? } ?>
<? } else { ?><em><?=$product['succ_buyers']?></em>人购买，
<? } ?>
售出<em><?=$product['sells_count']?></em>份
</div>
<? } ?>
  
<? if(false != ($summary=logic('comment')->front_get_summary($product['id']))) { ?>
<div id="name-comment-i" class="product-summary"> 
<em><?=$summary['count']?></em>条评价   
<ul class="comment-info__rating">
<li style="z-index:0; display: list-item; width:<? echo $summary['average'] * 20; ?>%;"></li>
</ul>                         
<em><?=$summary['average']?></em>分
</div> 
</div>
<? } ?>
<div class="detail-B_box" style="position:relative;">   
<p id="name-address-i" class="B B12" style="cursor:default;">商家：<?=$product['sellername']?> | 查看地址电话</p>             
<p class="B B12" >
<? if($product['begin_date']) { ?>
开始时间：<em><?=$product['begin_date']?></em><? } elseif($product['limit_time']) { ?>开始计时：<div class="deal_djs" id="remainTime_<?=$product['id']?>"></div>
<script language="javascript">
addTimeLesser(<?=$product['id']?>, <?=$product['limit_time']?>);
</script><? } elseif($product['time_remain'] > 86400) { ?>有效期：<span><? echo date('Y-m-d H:i',$product['overtime']); ?></span><? } elseif($product['time_remain'] > 0) { ?>剩余时间：
<div class="deal_djs" id="remainTime_<?=$product['id']?>"></div>
<script language="javascript">
addTimeLesser(<?=$product['id']?>, <?=$product['time_remain']?>);
</script>
<? } else { ?>
<? } ?>
<style>.deal_djs{ position:absolute; top:35px; left:60px; font-size:12px;}</style>
</p>
<p class="B12">
<? if($product['time_remain'] < 0) { ?>
<? } else { ?>
<? if($product['surplus']<=0) { ?>
下次请赶早
<? } else { ?>
<? if(!$product['begin_date'] && !$product['limit_time'] && ($product['is_countdown'] == 1 && $product['num'] > 0 || $product['is_countdown'] == 0)) { ?>
<? if($product['succ_remain']<=0) { ?>
<?=TUANGOU_STR?>成功，请尽快购买！
<? } else { ?>达成<?=TUANGOU_STR?>还需<?=$product['succ_remain']?>人
<? if(meta('p_ir_'.$product['id'])) { ?>
<a href="
<? echo $this->Config['site_url'];  ?>
/?u=<?=MEMBER_ID?>" onclick="copyText(this.href);">，邀请朋友一起买，返利<?=ini("product.default_payfinder")?>元</a>
<? } ?>
<? } ?>
<? } ?>
<? } ?>
<? } ?>
</p>
</div>
<? if($product['linkid']) { ?>
<div class="detail-B_box"> 
<div class="other_l"><em>套餐类型：</em>
<div class="other_l_s">
<? if(is_array($product['product_link']['products'])) { foreach($product['product_link']['products'] as $plk) { ?>
<? if($product['id'] == $plk['pid']) { ?>
<span class="u_seled"><?=$plk['name']?></span>
<? } else { ?><span class="u_sel"><a href="?view=<?=$plk['pid']?>"><?=$plk['name']?></a></span>
<? } ?>
<? } } ?>
</div>
</div>
</div>
<? } ?>
<? if($product['time_remain'] < 0) { ?>
<div class="deal_o"><a class="u_btn" href="javascript:void(0)">已结束</a></div>
<? } else { ?>
<? if($product['surplus']>0) { ?>
<div class="deal_b">
<? if($product['type'] == 'prize') { ?>
<a class="b_btn" href="?mod=buy&code=checkout&id=<?=$product['id']?>">立即抽奖</a>
<? } else { ?>
<? if($product['maxnum']>0 && $product['surplus']<=0) { ?>
<a class="b_btn" href="javascript:;">已经售罄</a><? } elseif($product['begin_date'] || $product['limit_time']) { ?>
<? if(logic('product_notify')->enabled()) { ?>
                    
<a class="remind_btn" href="?mod=about_to_begin&code=notify&id=<?=$product['id']?>" title="开团提醒"></a>                     
<? } ?>
<a class="b_btn" href="javascript:;">即将开始</a><? } elseif($product['is_countdown'] == 1 && $product['num'] > 0 || $product['is_countdown'] == 0) { ?>
<? if(1 == ini('product.default_cart')) { ?>
<a class="cat_btn" href="?mod=mycart&code=addcart&id=<?=$product['id']?>" title="加入购物车"></a>
<? } ?>
<a class="b_btn" href="?mod=buy&code=checkout&id=<?=$product['id']?>" title="立即抢购">立即抢购</a>
<? } ?>
<? } ?>
</div>
<? } else { ?><div class="deal_o"><a class="u_btn" href="javascript:void(0)">已售完</a></div>
<? } ?>
 
<? } ?>
 
<div class="datail-shop">
<div class="shop-share">
<? app('bshare')->load('product_detail', array('product'=>$product)) ?>
</div>
<script>
$(".share-tip").click(function() {
$(".share-list").toggle();
})
</script>
<? if($product['surplus']>0) { ?>
<? if($favorited && MEMBER_ID > 0) { ?>
<a class="shop-fav" href="javascript:void(0);" title="已收藏">已收藏</a><? } elseif(MEMBER_ID > 0) { ?><a id="favorite" class="shop-fav" href="javascript:void(0);" tourl="?mod=me&code=favorite&op=add&id=<?=$product['id']?>" onclick="addfavorite()"  title="收藏">收藏</a>
<? } ?>
<? } ?>
</div>  
</div>
</div>
<div class="site-ms__left">
<? if(false != ($data = logic('product')->GetOwnerList($product['sellerid'],5))) { ?>
<div class="t_area_out">
<div class="t_area_in">
<h5 class="detail-centit">
商家的其他<?=TUANGOU_STR?>
<div class="detail-centit_sub">
<span class="sub_sale">已售</span>
<span class="sub_value_cn">原价</span>
<span class="sub_price"><?=TUANGOU_STR?>价</span>
</div>
</h5>                
<? if(is_array($data)) { foreach($data as $product2) { ?>
<? if($product2['id']!=$product['id']) { ?>
<p class="otherBiz">
<a target="_blank" href="?view=<?=$product2['id']?>">
<span class="biz_title"><?=$product2['name']?></span>
<span class="price">&yen;<?=$product2['nowprice']?></span>
<span class="value_cn">&yen;<?=$product2['price']?></span>
<span id="tuanState" class="sale"><?=$product2['sells_count']?></span>
</a>
</p>
<? } ?>
<? } } ?>
</div>
</div>
<? } ?>
<div class="t_area_out">
<div class="content-navbar" id="content-navbar-id">
<ul>
<? if(!meta('p_hs_'.$product['id'])) { ?>
<li class="name-cur"><span id="name-address">商家介绍</span></li>
<script type="text/javascript">
$("#name-address").click(function() {
$(this).parent("li").nextAll().removeClass("name-cur");
$(this).parent("li").addClass("name-cur");
document.getElementById("name-address-block").scrollIntoView();
})
$("#name-address-i").click(function() {
document.getElementById("name-address-block").scrollIntoView();
})
</script>
<? } ?>
<li 
<? if(meta('p_hs_'.$product['id'])) { ?>
class="name-cur"
<? } ?>
><span id="name-product-detail" ><?=TUANGOU_STR?>详情</span></li>
<li><span id="name-comment">用户评价</span></li>
</ul>
<script type="text/javascript">
if(!($.browser.msie && $.browser.version<7)){
document.write('<script type="text/javascript" src="templates/default/./js/side_follow.js"><'+'/script>');
}
$("#name-product-detail").click(function(){
$(this).parent("li").nextAll().removeClass("name-cur");
$(this).parent("li").prevAll().removeClass("name-cur");
$(this).parent("li").addClass("name-cur");
document.getElementById("name-product-detail-block").scrollIntoView();
})
$("#name-comment").click(function() {
$(this).parent("li").prevAll().removeClass("name-cur");
$(this).parent("li").addClass("name-cur");
document.getElementById("name-comment-block").scrollIntoView();
})
$("#name-comment-i").click(function() {
document.getElementById("name-comment-block").scrollIntoView();
})
</script>
<div class="content-navbar_buy">
<? if($product['time_remain'] < 0) { ?>
<div class="u_btn s_btn">已结束</div>
<? } else { ?>
<? if($product['surplus']>0) { ?>
<div class="cj_or_bp">
<? if($product['type'] == 'prize') { ?>
<a class="b_btn s_btn" href="?mod=buy&code=checkout&id=<?=$product['id']?>">立即抽奖</a>
<? } else { ?>
<? if($product['maxnum']>0 && $product['surplus']<=0) { ?>
<a class="u_btn s_btn" href="javascript:;">已经售罄</a><? } elseif($product['begin_date'] || $product['limit_time']) { ?>
<? if(logic('product_notify')->enabled()) { ?>
                    
<a class="remind_btn" href="?mod=about_to_begin&code=notify&id=<?=$product['id']?>" title="开团提醒"></a>                     
<? } ?>
<a class="b_btn s_btn" href="javascript:;">即将开始</a>
<? } else { ?><a class="b_btn s_btn" href="?mod=buy&code=checkout&id=<?=$product['id']?>">立即抢购</a>
<? } ?>
<? } ?>
</div>
<? } else { ?>
<? } ?>
 
<? } ?>
</div>
<div style="clear:both;"></div>
</div>
<script type="text/javascript">
if(!($.browser.msie && $.browser.version<7)){
$("#content-navbar-id").fixbox({distanceToBottom:200,threshold:8});
}
</script>
<div class="mainbox">
<div class="main">
<?=ui('loader')->js('@product.detail')?>
<? if(!meta('p_hs_'.$product['id'])) { ?>
<a class="content-title" id="name-address-block">
<span>商家地址</span>
</a>
<div class="position-wrapper">
<div class="address-list">
<? $sellermap = $product['sellermap'] ?>
<? if($sellermap['0']!='') { ?>
<script type="text/javascript" src="http://api.go2map.com/maps/js/api_v2.0.js"></script>
<script type="text/javascript"> 
var map, marker;
function map_initialize()
{
var location = new sogou.maps.Point('<?=$sellermap['0']?>', '<?=$sellermap['1']?>');
var mapOptions = {
zoom: parseInt('<?=$sellermap['2']?>'),
center: location,
mapTypeId: sogou.maps.MapTypeId.ROADMAP,
mapControl: false
};
map = new sogou.maps.Map(document.getElementById("map_canvas"), mapOptions);
marker = new sogou.maps.Marker({
map: map,
position: location,
title: "<?=$product['sellername']?>"
});
}
</script>
<div class="left-content">
<div id="map_canvas">
<div style="padding:1em; color:gray;">正在载入...</div>
</div>
<a id="img1" class="img2"><div class="map_big">查看完整地图</div></a>
</div>
<? } ?>
<div class="biz-wrapper" style="float:left;">
<h1><a href="?mod=seller&code=view&id=<?=$product['sellerid']?>"><?=$product['sellername']?></a></h1>
<ul style="margin-top:15px;font-size:12px;">
<li class="com_adr">
<? if($product['selleraddress']) { ?>
<div><strong>地址：</strong><?=$product['selleraddress']?></div>
<? } ?>

<? if($product['sellerphone']) { ?>
<div><strong>电话：</strong><?=$product['sellerphone']?></div>
<? } ?>

<? if($product['trade_time']) { ?>
<div><strong>营业时间：</strong><?=$product['trade_time']?></div>
<? } ?>
<a href="<?=$product['sellerurl']?>" target="_blank"><?=$product['sellerurl']?></a>
</li>
</ul>
</div>
<? if($sellermap['0']!='') { ?>
<script type="text/javascript">
$(document).ready(function() {
$("#img1").click(function() {
window.open('http://map.sogou.com/#c=<?=$sellermap['0']?>,<?=$sellermap['1']?>,<?=$sellermap['2']?>');
});
<? if($sellermap['0']!='') { ?>
map_initialize();
<? } ?>
});
</script>
<? } ?>
</div>
<div style="clear:both;"></div>
</div>
<? } ?>
<a class="content-title" id="name-product-detail-block">
<span><?=TUANGOU_STR?>详情</span>
</a>
<div id="product_detail_area">
<? if($product['cue']) { ?>
<h4>【购买须知】</h4>
<div class="product_detail_cnt"><?=$product['cue']?></div>
<? } ?>
<h4>【本单详情】</h4>
<Script type="text/javascript">
$("#t_detail_txt img").each(function(){
if($(this).width() > $(this).parent().width()) {
$(this).width("100%");
}});
</Script>
<div id="product_detail_cnt" class="product_detail_cnt"><?=$product['content']?></div>
<? if($product['theysay']) { ?>
<h4>【他们说】</h4>
<div class="product_detail_cnt"><?=$product['theysay']?></div>
<? } ?>

<? if($product['wesay']) { ?>
<h4>【我们说】</h4>
<div class="product_detail_cnt"><?=$product['wesay']?></div>
<? } ?>
</div>
<a class="content-title" id="name-comment-block">
<span>用户评价</span>
</a>
<? logic('comment')->show_summary($product['id']) ?>
</div>
<div class="deal-buy-bottom">
<div class="price">&yen;<?=$product['nowprice']?></div>
<table>
<tbody>
<tr>
<th>市场价</th>
<th>折扣</th>
<th>已<?=TUANGOU_STR?></th>
</tr>
<tr>
<td><span>&yen;</span><del><?=$product['price']?></del></td>
<td><?=$product['discount']?>折</td>
<td>
<? if($product['type'] == 'prize') { ?>
<? echo logic('prize')->allCount($product['id']); ?>
<? } else { ?><?=$product['succ_buyers']?>
<? } ?>
人
</td>
</tr>
</tbody>
</table>
<div class="btn—wrapper">
<? if($product['time_remain'] < 0) { ?>
<a class="btn" style="float:right;" href="javascript:void(0)">已结束</a>
<? } else { ?>
<? if($product['surplus']>0) { ?>
<div class="cj_or_bp">
<? if($product['type'] == 'prize') { ?>
<a class="b_btn" href="?mod=buy&code=checkout&id=<?=$product['id']?>">
立即抽奖
</a>
<? } else { ?><a class="b_btn" href="?mod=buy&code=checkout&id=<?=$product['id']?>">
立即抢购
</a>
<? } ?>
</div>
<? } else { ?><div class="u_btn" style="float:right;">
已售完
</div>
<? } ?>
 
<? } ?>
</div>
</div>
</div>
</div>
</div>
<div class="site-ms__right">
<div class="t_area_out ">
<h1>看了本<?=TUANGOU_STR?>的用户还看了</h1>
<div class="t_area_in">
<ul class="product_list">
<? $cpid = isset($_GET['view']) ? $_GET['view'] : -1; ?>

<? $one_product =  logic('product')->GetOne($cpid); ?>

<? $product_other_list = logic('product')->GetOtherList($one_product['city'], $one_product['category'], $one_product['id'], 10); ?>
<? if(is_array($product_other_list)) { foreach($product_other_list as $i => $product) { ?>
<li>
<p class="pl_img"><a href="?view=<?=$product['id']?>"><img src="<? echo imager($product['imgs']['0'], IMG_Small);; ?>" width="175"/></a></p>
<p class="name"><a href="?view=<?=$product['id']?>"><?=$product['name']?></a></p>
<div class="shop">
<div class="pr">
<font class="price">&yen;<?=$product['nowprice']?></font>
<font class="markprice">&nbsp;市场价：&yen;<?=$product['price']?></font>
</div>
<div style="clear:both;"></div>
</div>
</li>
<? } } ?>
</ul>
</div>
</div>
<?=ui('widget')->load('index_detail')?>
</div>
</div>
<? include handler('template')->file('footer'); ?>