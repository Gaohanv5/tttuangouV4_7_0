<? include handler('template')->file('@admin/header'); ?>
<form action="?mod=ad&code=config&op=save&flag=<?=$flag?>" method="post"  enctype="multipart/form-data">
<input type="hidden" name="FORMHASH" value='<?=FORMHASH?>'/>
<table cellspacing="1" cellpadding="4" width="100%" align="center" class="tableborder" id="adtable">
<tr class="header">
<td colspan="2">
<a href="?mod=ad&code=vlist">返回广告列表</a> （如果提示 <font color="red">Acces Deined</font> 错误，请检查“全局设置-上传设置”里面对应的图片上传权限是否有打开）
</td>
</tr>
<tr>
<td width="12%"></td>
<td></td>
</tr>
<tr class="tips">
<td colspan="2">通栏单图横幅广告</td>
</tr>
<? if(is_array($cfg['list'])) { foreach($cfg['list'] as $id => $one) { ?>
<tr id="ad_tr_<?=$id?>">
<td class="td_title" style="border-right: 2px solid #ccc;">
<?=$id?><br/>
<a href="#ad_del" onclick="ad_del('<?=$id?>');return false;">&lt; 删除 &gt;</a>
</td>
<td>
<div id="div_pic_<?=$id?>">广告图片：<input type="hidden" name="data[list][<?=$id?>][image]" value="<?=$one['image']?>" /><a href="#ad_load_img" onclick="ad_load_image('<?=$one["image"]?>');return false;"><?=$one['image']?></a> &gt;&gt; <a href="#ad_upload_img" onclick="ad_upload_show('<?=$id?>');return false;">替换图片</a></div>
<div id="div_up_<?=$id?>" style="display: none;">广告图片：<input type="file" name="file_<?=$id?>" /> * 图片分辨率为 1200 x 60</div>
文字说明：<input type="text" name="data[list][<?=$id?>][text]" value="<?=$one['text']?>" size="30" /><br/>
链接地址：<input type="text" name="data[list][<?=$id?>][link]" value="<?=$one['link']?>" size="50" />
<select name="data[list][<?=$id?>][target]"><option value="_self"
<? if($one['target']=='_self') { ?>
 selected="selected"
<? } ?>
>当前页面打开</option><option value="_blank"
<? if($one['target']=='_blank') { ?>
 selected="selected"
<? } ?>
>新建窗口打开</option></select><br/>
显示排序：<input type="text" name="data[list][<?=$id?>][order]" value="<?=$one['order']?>" size="10" /> * 数字越大，显示越靠前
</td>
</tr>
<? } } ?>
<tr id="ad_pox_add_link">
<td></td>
<td><span id="addinput"><a href="#ad_add" onclick="ad_add_new();return false;">&lt; 添加图片 &gt;</a></span></td>
</tr>
<tr>
<td></td>
<td>
<input type="submit" value="保 存" class="button" />
</td>
</tr>
</table>
</form>
<script type="text/javascript">
var __url_base = '<?=ini("settings.site_url")?>';
var __ad_tpl = '\
<tr id="ad_tr_<#ID#>">\
<td class="td_title" style="border-right: 2px solid #ccc;">\
<#ID#><br/>\
<a href="#ad_del" onclick="ad_del(\'<#ID#>\');return false;">&lt; 删除 &gt;</a>\
</td>\
<td>\
<input type="hidden" name="data[list][<#ID#>][image]" value="uploads/images/howdo/h.<#ID#>.gif" />\
广告图片：<input type="file" name="file_<#ID#>" /> * 图片分辨率为 485 x 60<br/>\
文字说明：<input type="text" name="data[list][<#ID#>][text]" value="" size="30" /><br/>\
链接地址：<input type="text" name="data[list][<#ID#>][link]" value="" size="50" />\
<select name="data[list][<#ID#>][target]"><option value="_self">当前页面打开</option><option value="_blank">新建窗口打开</option></select><br/>\
显示排序：<input type="text" name="data[list][<#ID#>][order]" value="" size="10" /> * 数字越大，显示越靠前\
</td>\
</tr>';
function ad_add_new()
{
var rndID = ad_random_id();
var ad_tpl = __ad_tpl.replace(/<#ID#>/ig, rndID);
$('#ad_pox_add_link').before(ad_tpl);
}
function ad_del(aid)
{
if (!confirm('确认删除吗？'))
{
return;
}
$('#ad_tr_'+aid.toString()).remove();
}
function ad_random_id()
{
var salt = '0123456789qwertyuioplkjhgfdsazxcvbnm';
var str = '';
for(var i=0; i<6; i++)
{
str += salt.charAt(Math.ceil(Math.random()*100000000)%salt.length);
}
return str;
}
function ad_load_image(path)
{
$.notify.loading('loading...');
var url = __url_base+'/'+path;
var img = document.createElement('img');
img.src = url;
img.onload = function() {
$.notify.loading(false);
art.dialog({title: url, content: '<img src="'+url+'" />', width: this.width, height: this.height, fixed: true, padding: '0 0'});
};
}
function ad_upload_show(aid)
{
$('#div_pic_'+aid.toString()).hide();
$('#div_up_'+aid.toString()).show();
}
</script>
<? include handler('template')->file('@admin/footer'); ?>