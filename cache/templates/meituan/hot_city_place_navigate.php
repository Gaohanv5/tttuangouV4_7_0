<? if($places) { ?>
<div class="site-fs__cell">	
<div class="site-fs__cell-title i-shangquan">热门商圈：</div>
<div class="site-fs__cell-son1">
<? if(is_array($places)) { foreach($places as $i => $region) { ?>
<a href="<?=$region['url']?>" class="topclass-name 
<? if($region['selected']) { ?>
selected
<? } ?>
"><font color="<?=$region['fontcolor']?>"><?=$region['name']?></font></a>
<? } } ?>
<div style="clear: both;"></div>
</div>
<div style="clear: both; height:0px; overflow:hidden;"></div>
</div>
<? } ?>