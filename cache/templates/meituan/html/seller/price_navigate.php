<div class="site-fs__cell">
<div class="site-fs__cell-title">人均消费：</div>
<div class="site-fs__cell-son1">
<? if(is_array($price_navs)) { foreach($price_navs as $pn) { ?>
<a href="<?=$pn['url']?>" title="<?=$pn['title']?>" class="topclass-name 
<? if($pn['selected']) { ?>
selected
<? } ?>
"><?=$pn['name']?></a>
<? } } ?>
<div style="clear: both;"></div>
</div>
<div style="clear: both; height:0px; overflow:hidden;"></div>
</div>