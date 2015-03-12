/** * @copyright (C)2014 Cenwor Inc. * @author Cenwor <www.cenwor.com> * @package js * @name tttuangou_seller.mgr.js * @date 2014-09-01 17:24:23 */ var IMG_LOADING = 'templates/admin/images/btn_loading.gif';

var __img_last_id = '';
var __img_control_d = false;
var __click_from_submit = false;
var __editor_allow_exit = false;
var __editor_allow_close = false;

$(document).ready(function(){
	// hook for Swfupload
	$.hook.add('swfuploaded', function(file){InsertImage(file)});

    // city
    $('#allCityList').bind('change', function(){
        $.hook.call('pro.city.sel.change');
    });
    $.hook.add('pro.city.sel.change', function(){
        cplace_regions_reload();
    });
    loadCitys(__Default_CityID);
});

function exitConfirm()
{
	if (confirm('确认退出编辑器？'))
	{
		$.hook.call('productEditorExit');
	}
}

function introFocus(obj)
{
	$(obj).attr('last', $(obj).val());
}

function introChange(id, obj)
{
	if ($(obj).attr('last') == $(obj).val())
	{
		return;
	}
	$(obj).attr('last', $(obj).val());
	var TMP_id = 'img_loading_'+__rand_key();
	$(obj).after('<img id="'+TMP_id+'" src="'+IMG_LOADING+'" />');
	$.get('?mod=product&code=save&op=intro&id='+id+'&intro='+encodeURIComponent($(obj).val())+$.rnd.stamp(), function(data){
		if (data != 'ok')
		{
			$.notify.failed('保存失败！');
		}
		$('#'+TMP_id).remove();
	});
}

function InsertImage(file)
{
	if (__Global_SID == '')
	{
		$('#imgs').val($('#imgs').val()+file.id+',');
		ShowUploadImage(file);
		return;
	}
	$.get('?mod=seller&code=add&op=image&seller_id='+__Global_SID+'&id='+file.id+$.rnd.stamp(), function(data){
		if (data == 'ok')
		{
			ShowUploadImage(file);
		}
	});
}

function ShowUploadImage(file)
{
	var tpl = $('#img_li_TPL').html();
	tpl = tpl.replace(/\[id\]/g, file.id);
	tpl = tpl.replace(/#http\:\/\/\[url\]\//g, file.url);
	$('#img_li_TPL').before('<li id="img_li_for_'+file.id+'">'+tpl+'</li>');
}

function DeleteImage(id)
{
	if (!confirm('确认删除？')) return;
	$.get('?mod=seller&code=del&op=image&seller_id='+__Global_SID+'&id='+id+$.rnd.stamp(), function(data){
		if (data == 'ok')
		{
			if (__Global_SID == '')
			{
				$('#imgs').val($('#imgs').val().replace(id+',', ''));
			}
			$('#img_li_for_'+id).slideUp();
		}
	});
}

/**
 * 随机字符
 */
function __rand_key()
{
	var salt = '0123456789qwertyuioplkjhgfdsazxcvbnm';
	var str = '';
	for(var i=0; i<6; i++)
	{
		str += salt.charAt(Math.ceil(Math.random()*100000000)%salt.length);
	}
	return str;
}

function loadCitys(cid)
{
    $('#allCityList').html('<option value="-1">正在加载</option>');
    $.get('admin.php?mod=product&code=quick&op=listCity&icity='+cid+$.rnd.stamp(), function(data){
        $('#allCityList').html(data);
    });
}
