<div class="product_wrap">    
<? if(is_array($product)) { foreach($product as $cata) { ?>
<div class="cat-floor__head">
<div class="sub-categories">
<? if($cata['subclass']) { ?>
<? if(is_array($cata['subclass'])) { foreach($cata['subclass'] as $subc) { ?>
<a href="<?=$subc['url']?>"><?=$subc['name']?></a>
<? } } ?>
<? } ?>
<a class="sub-categories_last" href="<?=$cata['url']?>">全部</a>
</div>
<a href="<?=$cata['url']?>" class="sub-name"><?=$cata['name']?></a>
</div>
<div class="product_item">
<style type="text/css">
.deal-tile__title{ padding:10px 15px 0;}
.deal-tile__detail{ padding:0 15px;height: 35px;}
</style>
<? if($cata['product']) { ?>
<? if(is_array($cata['product'])) { foreach($cata['product'] as $item) { ?>
<? $icc++ ?>
<div class="t_area_out template2">
<a class="product_item_a" href="?view=<?=$item['id']?>" target="_blank" title="<?=$item['name']?>">
<div class="t_area_in" >
<div class="deal-tile__cover">
<div class="deal-tile__cover_img mt">
<? ui('iimager')->single($item['id'], $item['imgs']['0']) ?>
</div>
<div class="deal-tile__cover_tag">
<? if($item['linkid']) { ?>
<i><span title="多个套餐">多种套餐</span></i>
<? } ?>
<? logic('product_tag')->html($item['id']) ?>
</div>
</div>
<div class="deal-tile__title"><?=$item['name']?></div>
<div class="deal-tile__detail">
<div class="price">&yen;<?=$item['nowprice']?></div>
<div class="at_shuzi">
<span>原价:</span><b class="prime_cost ">&yen;<?=$item['price']?></b>
</div>
<? if($item['presell']) { ?>
<div class="yufu">
<span><?=TUANGOU_STR?>价:</span><b>&yen;<?=$item['presell']['price_full']?></b>
</div>
<? } ?>
</div>
<div class="deal-tile__extra">
<? if(false != ($summary=logic('comment')->front_get_summary($item['id']))) { ?>
<div id="name-comment" class="product-summary B">                              
<em><?=$summary['count']?></em>条评价   
<ul class="comment-info__rating">
<li style="z-index:0; display: list-item; width:<? echo $summary['average'] * 20; ?>%;"></li>
</ul>                         
<em><?=$summary['average']?></em>分
</div> 
<? } ?>
<div id="tuanState" class="mb_0626">已售<b><?=$item['sells_count']?></b></div>
</div>	
</div>
</a>
</div>
<? } } ?>
<? } ?>
</div>
<a class="category-floor__foot " href="<?=$cata['url']?>">查看全部<b><?=$cata['name']?></b></a>
<? } } ?>
</div>
</div>
<div class="site-ms__right">
<?=ui('widget')->load('index_home')?>
</div>
</div>