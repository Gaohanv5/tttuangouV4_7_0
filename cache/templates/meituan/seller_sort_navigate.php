<? if($sorts) { ?>
<div class="site-fs__sort_w">
<div class="site-fs__sort">
<div class="site-fs__sort-s ">
<? if(is_array($sorts)) { foreach($sorts as $key => $sort) { ?>
<a href="<?=$sort['url']?>" class="
<? if($sort['selected']) { ?>
selected
<? } ?>
" title="<?=$sort['title']?>"><?=$sort['name']?></a>
<? } } ?>
</div>
<div style="clear: both;  height:0px; overflow:hidden;"></div>
</div>
</div>
<? } ?>