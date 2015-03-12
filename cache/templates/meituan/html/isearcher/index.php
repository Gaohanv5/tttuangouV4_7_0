<?=ui('loader')->css('#html/isearcher/style')?>
<?=ui('loader')->js('#html/isearcher/main')?>
<?=ui('loader')->addon('picker.date')?>
<div class="isearcher">
<p class="searcher_li">
<select id="iscp_fids" class="isearcher_filter_list">
<? if(is_array($fids)) { foreach($fids as $fid) { ?>
<option value="<?=$fid?>"><?=$map[$fid]['name']?></option>
<? } } ?>
</select>
<input id="iscp_input" class="isearcher_input_words" value="<?=$iscp_input_value?>" type="text"/>
<input id="iscp_search" type="hidden" />
<input id="iscp_button" class="btn btn-primary btn-small" type="button" value="搜索" />
<div id="iscp_iresult" class="isearcher_instant_result">
<ul id="iscp_iresult_list">
</ul>
</div>
</p>
<? if($tvss) { ?>
<? if(is_array($tvss)) { foreach($tvss as $tvi => $tvk) { ?>
<? if(isset($timev[$tvk])) { ?>
<p class="searcher_li">
<select id="iscp_timev_key_<?=$tvk?>">
<? if(is_array($timev[$tvk])) { foreach($timev[$tvk] as $tvdata) { ?>
<option value="<?=$tvdata['key']?>" 
<? if(get('iscp_tvfield_'.$tvk) == $tvdata['key']) { ?>
selected="selected"
<? } ?>
><?=$tvdata['name']?></option>
<? } } ?>
</select>
开始：<input type="text" onfocus="WdatePicker({isShowClear:false,readOnly:true,dateFmt:'yyyy-MM-dd',startDate:'<?=$tvinputs[$tvk]['begin']?>',vel:'iscp_timev_begintime_<?=$tvk?>'})" size="11" class="Wdate" value="<?=$tvinputs[$tvk]['begin']?>" />
<input name="iscp_timev_begintime" type="hidden" id="iscp_timev_begintime_<?=$tvk?>" value="<?=$tvinputs[$tvk]['begin']?>" />
结束：<input type="text" onfocus="WdatePicker({isShowClear:false,readOnly:true,dateFmt:'yyyy-MM-dd',startDate:'<?=$tvinputs[$tvk]['finish']?>',vel:'iscp_timev_finishtime_<?=$tvk?>'})" size="11" class="Wdate" value="<?=$tvinputs[$tvk]['finish']?>" />
<input name="iscp_timev_finishtime" type="hidden" id="iscp_timev_finishtime_<?=$tvk?>" value="<?=$tvinputs[$tvk]['finish']?>" />
<input type="button" class="btn btn-primary btn-small" onclick="iscp_timev_goto('<?=$tvk?>')" value="确认" />
</p>
<? } ?>
<? } } ?>
<? } ?>
<? if($ffsString) { ?>
<p class="searcher_li">
筛选：
<? $frcKeys = '' ?>
<? if(is_array($frcs)) { foreach($frcs as $fcid) { ?>
<? if(!$filter[$fcid]) { ?>
<? continue ?>
<? } else { ?><? $frc = $filter[$fcid] ?>
<? } ?>
<? $frcKeys .= $frc['key'].',' ?>
<?=$frc['name']?>
<select id="iscp_frc_<?=$frc['key']?>" key="<?=$frc['key']?>" class="isearcher_filter_list">
<option value="###">全部数据</option>
<? if(is_array($frc['list'])) { foreach($frc['list'] as $val => $key) { ?>
<option value="<?=$val?>"><?=$key?></option>
<? } } ?>
</select>&nbsp;
<? } } ?>
<? } ?>
</div>
<script type="text/javascript">
var frcKeys = '<? echo substr($frcKeys, 0, -1); ?>';
</script>