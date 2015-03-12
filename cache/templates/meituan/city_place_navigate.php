<? if($places) { ?>
<div class="site-fs__cell site-fs__cell-fix">	
<div class="site-fs__cell-fixson">	
<div class="site-fs__cell-title i-quyu">全部区域：</div>
<div class="site-fs__cell-son1 fix-son1">
<? if(is_array($places)) { foreach($places as $i => $region) { ?>
<a href="<?=$region['url']?>" class="topclass-name 
<? if($region['selected']) { ?>
selected
<? } ?>
"><?=$region['name']?></a>
<? if($region['selected']) { ?>
<? $streets = $region['streets'] ?>
<? } ?>
<? } } ?>
<div style="clear: both;"></div>
</div>
<div class="site-fs__cell-location">
<a href="index.php?region=0" target="_blank" title="点击查看更多">更多<i></i></a>
</div> 
</div>  
<? if($streets && count($streets) > 1) { ?>
<div class="site-fs__cell-son2">
<? if(is_array($streets)) { foreach($streets as $ii => $street) { ?>
<a href="<?=$street['url']?>" class="topclass-name 
<? if($street['selected']) { ?>
selected
<? } ?>
"><?=$street['name']?></a>
<? } } ?>
<div style="clear: both;"></div>
</div>
<? } ?>
</div>
<? } ?>