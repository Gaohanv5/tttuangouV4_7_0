<?=ui('loader')->js('#admin/js/cplace.inputer')?>
<select id="__cplace_region" name="__cplace_region" onchange="cplace_region_change()"> <option value="0">全部</option>
<? if(is_array($places)) { foreach($places as $i => $region) { ?>
<option value="<?=$region['id']?>"
<? if($region['selected']) { ?>
 selected="selected"
<? $regiond=$region['id'] ?>
<? } ?>
><?=$region['name']?></option>
<? } } ?>
</select> <select id="__cplace_street" name="__cplace_street"> <option value="0">全部</option>
<? if($regiond && isset($places[$regiond])) { ?>
<? if(is_array($places[$regiond]['streets'])) { foreach($places[$regiond]['streets'] as $street) { ?>
<option value="<?=$street['id']?>"
<? if($street['selected']) { ?>
 selected="selected"
<? } ?>
><?=$street['name']?></option>
<? } } ?>
<? } ?>
</select>
&nbsp;&nbsp;&nbsp;&nbsp;
[ <a href="admin.php?mod=tttuangou&code=city" target="_blank">添加区域</a> ]