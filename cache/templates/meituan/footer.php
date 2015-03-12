<div class="siteinfo" >
<script type="text/javascript">
$(document).ready(function() {
$("#top_title").click(function() {
$("#show_provinces").toggle();
}).css({ "cursor":"pointer" });	
var last_show = null;
$(".sub_title").mouseover(function() {
if(last_show != null) {
last_show.prev().css({ "color":"#FF6600" , "font-weight":"normal" });
last_show.hide();				
}
last_show = $(this).css({ "color":"#FF6600" }).next();
last_show.show();	
}).css({ "cursor":"pointer" , "text-decoration":"underline" });	
$("li a").click(function() {			
$(".show_citys").hide();
$("#show_provinces").hide();
});
$("#close").click(function() {
$(".show_citys").css({ "color":"#FF6600" }).hide();
$("#show_provinces").css({ "color":"#FF6600" }).hide();
}).mouseover(function() { 
$(this).css({ "color":"#FF6600" });
}).mouseout(function() {
$(this).css({ "color":"#FF6600" });	
}).css({ "cursor":"pointer" });
});
</script>
<div id="site-info-w">
<ul class="site-info">
<li class="site-info__item site-info__item--btm-logo" >
<a id="logo-footer" href="./"></a>
</li>       
<li class="site-info__item">
<h3>商务合作</h3>
<ul>
<? if(MEMBER_ROLE_TYPE=='normal' && $this->Config['selleropen']) { ?>
<li><a href="?mod=seller_join">申请成为商家</a></li>
<? } ?>
<li><a href="?mod=list&code=feedback">意见反馈</a></li>
<li><a href="?mod=list&code=business">商务合作</a></li>
<li><a href="?mod=openapi">开放 API</a></li>
</ul>
</li>
<li class="site-info__item" >
<h3>如何<?=TUANGOU_STR?></h3>
<ul>
<li><a href="?mod=html&code=help"><?=TUANGOU_STR?>指南</a></li>
<li><a href="?mod=html&code=faq">常见问题</a></li>
<li><a href="?mod=subscribe&code=mail">邮件订阅</a></li>
<li><a href="?mod=subscribe&code=sms">短信订阅</a></li>
</ul>
</li>
<li class="site-info__item">
<h3>联系我们</h3>
<ul>
<li><a href="?mod=html&code=contact">联系我们</a></li>
<li><a href="?mod=list&code=ask">在线问答</a></li> 
</ul>
</li>
<li class="site-info__item">
<h3>公司信息</h3>
<ul>
<li><a href="?mod=html&code=about">关于我们</a></li>
<li><a href="?mod=html&code=privacy">隐私保护</a></li>
<li><a href="?mod=html&code=join">加入我们</a></li>
<li><a href="?mod=html&code=terms">用户协议</a></li>
</ul>
</li>
</ul>
<div class="friend_link"
<? if(count(ini('link'))==0) { ?>
 style="display:none;"
<? } ?>
>
<ul class="subList" >
<b>友情链接：</b>
<? if(ini('link')) { ?>
<? if(is_array(ini('link'))) { foreach(ini('link') as $i => $value) { ?>
<li>
<a href="<?=$value['url']?>" title="<?=$value['name']?>" target="_blank">
<? if($value['logo']) { ?>
<img src="<?=$value['logo']?>" style="width:81px;" height="31px" />
<? } else { ?><?=$value['name']?>
<? } ?>
</a>
</li>
<? } } ?>
<? } ?>
</ul>
</div>
<div class="attestation">
<a href="#"><img src="templates/default/images/trust_r1_c3.gif" /></a>
<a href="#"><img src="templates/default/images/trust_r1_c1.gif" /></a>
<a href="#"><img src="templates/default/images/trust_r1_c7.gif" /></a>
<?=logic('acl')->Comlic()?>
</div>
<p class="site_copyright">
<a href="http://www.miibeian.gov.cn/" target="_blank" title="网站备案"><?=$this->Config['icp']?></a><?=$this->Config['tongji']?>&nbsp; <?=$this->Config['copyright']?>&nbsp;
</p>
</div>	
</div>
<div id="stick-qrcode">
<? if(DOWNAPP) { ?>
<div class="stick-qr">
<img src="uploads/apks/img/app_x.png">
<span><a href="?mod=downapp">扫描下载手机版<br>享更多优惠！</a></span>
</div>
<? } ?>
<a id="gotop" class="goTop" title="返回顶部" href="javascript:void(0)"></a>
</div>
<script type="text/javascript">
function a(x,y){
//l = $('.site-mast__user-nav').offset().left;
w = $('.site-mast__user-nav').width();
//$('#stick-qrcode').css('left',(l + w + x) + 'px');
$('#stick-qrcode').css('bottom',y + 'px');
}
function b(){
w_h = $(window).height();
d_t = $(document).scrollTop()*3;
if(d_t > w_h){
$('#gotop').css("display","block");
}else{
$('#gotop').hide();
}
}
$(document).ready(function(e) {		
a(10,120);//#content_us的div距浏览器底部和页面内容区域右侧的距离
b();
$('#gotop').click(function(){
//$(document).scrollTop(0);
$('html, body').animate({scrollTop: 0},300);	
})
});
$(window).resize(function(){
a(10,120);//#content_us的div距浏览器底部和页面内容区域右侧的距离
});
$(window).scroll(function(e){
b();		
})
</script>
<? echo ui('loader')->js('@'.$this->Module.'.'.$this->Code) ?>
<?=ui('loader')->js('@footer')?>
<?=ui('pingfore')->html()?>
<? $this->ob_gzhandler() ?>
</body>
</html>
<?=handler('member')->UpdateSessions()?>