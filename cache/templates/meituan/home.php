<? include $this->TemplateHandler->template("header"); ?>
<div class="site-ms__left"> 
<div class="site-mast__search-result" 
<? if($kw) { ?>
 style="display:block;"
<? } else { ?>style="display:none;"
<? } ?>
>找到“<?=$kw?>”相关的商品信息如下</div>
<? echo ui('loader')->css($this->Module.'.'.$this->Code) ?>
<? if(INDEX_DEFAULT === true) { ?>
<style>#site-mast__site-cla-main-box{ display:block;}.site-fs__cell .selected{ background:none; color:#666;}</style>
<script>
$(document).ready(function() {		
$("#site-mast__site-cla-main-box").show();
$(".site-mast__site-cla-main").mouseover(function() {  
$("#site-mast__site-cla-main-box").show();  
}).mouseout(function() {  
$("#site-mast__site-cla-main-box").show();  
}); 
})
</script>
<div class="site-mast_da">
<div class="site-mast_land">
<div class="site-fs">
<?=ui('catalog')->hot_display()?>
<?=logic('city')->place_navigate()?>
<?=logic('city')->hot_place_navigate()?>
<script>
$(".site-fs__cell-fixson").mouseover(function() { 
$(".site-fs__cell-fix").addClass("site-fs__cell-fix_re");
//		$(".site-fs__cell-location a").css("background-position","-17px -182px");
});
$(".site-fs__cell-fixson").mouseout(function() {
$(".site-fs__cell-fix").removeClass("site-fs__cell-fix_re");
//		$(".site-fs__cell-location a").css("background-position","0 -182px");
});
</script>   
</div>
<div class="site-new_slider">
<?=ui('loader')->js('jquery.carouFredSel')?>
<script type="text/javascript">
$(function() {
$('#carousel ul').carouFredSel({
prev: '#prev',
next: '#next',
pagination: "#pager",
scroll: 1000
});
});
</script>
<h5>本周推荐<?=TUANGOU_STR?>11</h5>
<div id="carousel">
<ul>
<? if(is_array($new_product)) { foreach($new_product as $item) { ?>
        
<li style="width:900px;" >
	<a href="?view=<?=$item['id']?>" class="a_img" style="width:900px;" ><img style="width:900px;" src="<? echo imager($item['img'], IMG_Normal); ?>"></a>
	<div class="a_txt">
		<span class="a_name"><a href="?view=<?=$item['id']?>" title="<?=$item['name']?>"><?=$item['name']?></a></span>
		<span class="a_pr">&yen;<?=$item['nowprice']?></span>
	</div>
	<div class="deal-tile__cover_tag">
<? if($item['linkid']) { ?>
<i><span title="多个套餐">多种套餐</span></i>
<? } ?>
<? logic('product_tag')->html($item['id']) ?>
</div>
</li>
<? } } ?>
</ul>
<div class="clearfix"></div>
<a id="prev" class="prev" href="#">&lt;</a>
<a id="next" class="next" href="#">&gt;</a>
<div id="pager" class="pager"></div>
</div>
</div>
</div>
</div>
<? } else { ?><div class="site-mast_da def_b">
<div class="site-fs">
<?=ui('catalog')->hot_display()?>
<?=logic('city')->place_navigate()?>
<?=logic('city')->hot_place_navigate()?>
<?=ui('catalog')->display()?>
<div class="promo_tab">
<?=logic('sort')->product_navigate()?>
<?=logic('tag')->navigate()?>
</div>
</div>
</div>
<? } ?>
<div class="site-ad">
<?=ui('ad')->load('howdo')?>
<?=ui('ad')->load('howdom')?>
<?=ui('ad')->load('howdot')?>
<?=ui('ad')->load('howdof')?>
<?=ui('ad')->load('howdos')?>
<?=ui('ad')->load('howparallel')?>
</div>
<script type="text/javascript">
var __Timer_lesser_auto_accuracy = <? echo ini('ui.igos.litetimer') ? 'true' : 'false'; ?>;
var __Timer_lesser_worker_max = <? echo (int)ini('ui.igos.litetimer_wm'); ?>;
</script>
<?=ui('loader')->js('@time.lesser')?>
<? ui('igos')->load($product) ?>
<?=ui('iimager')->single_lazy()?>
<? include $this->TemplateHandler->template("footer"); ?>