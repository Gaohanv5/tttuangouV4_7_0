/** * @copyright (C)2014 Cenwor Inc. * @author Cenwor <www.cenwor.com> * @package js * @name product.mgr.js * @date 2014-09-11 18:10:50 */ var IMG_LOADING = 'templates/admin/images/btn_loading.gif';

var __img_last_id = '';
var __img_control_d = false;
var __click_from_submit = false;
var __editor_allow_exit = false;
var __editor_allow_close = false;

$(document).ready(function(){
    document.title = 'Product Editor';
	// thickbox
	tb_init('a.thickbox');
	// hook for Swfupload
	$.hook.add('swfuploaded', function(file){InsertImage(file)});
	// bind button
	$('#submitButton').bind('click', function(){
        __editor_allow_close = true;
		$.hook.call('productIfoSubmit');
	});
	$.hook.add('productIfoSubmit', function(){
		if (productSubmitCheck(true))
		{
			submitClick(true);
		}
	});
	$('#exitButton').bind('click', exitConfirm);
    /*
	$(window).bind('beforeunload',  function(){
        if (!__editor_allow_close) return '直接关闭将丢失您的所有内容！退出请“保存”或者点击“退出编辑器”！';
    });
    */
    // city
    $('#allCityList').bind('change', function(){
        $.hook.call('pro.city.sel.change');
    });
    $.hook.add('pro.city.sel.change', function(){
        loadSellers($('#allCityList').val());
    });
    loadCitys(__Default_CityID);
    loadSellers(__Default_CityID, __Default_SellerID);
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
	if (__Global_PID == '')
	{
		$('#imgs').val($('#imgs').val()+file.id+',');
		ShowUploadImage(file);
		return;
	}
	$.get('?mod=product&code=add&op=image&pid='+__Global_PID+'&id='+file.id+$.rnd.stamp(), function(data){
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
	$.get('?mod=product&code=del&op=image&pid='+__Global_PID+'&id='+id+$.rnd.stamp(), function(data){
		if (data == 'ok')
		{
			if (__Global_PID == '')
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

function productSubmitCheck(showErr)
{
	// check must val
	var mvcList = {
		'productName': '产品标题不能为空！',
		'productFlag': '简短名称不能为空！',
		'allCityList': '请选择产品投放城市！',
		'sellerid': '请指定合作商家！',
		'productPrice': '请输入产品原价！',
		'productNowPrice': '请输入产品现价！'
	};
	if($("#allCityList").length == 0){delete mvcList.allCityList;}
	var foundErr = false;
	var errString = '';
	$.each(mvcList, function(id, err){
		var kval = $('#'+id).val();
		if (id == 'productNowPrice') kval = isNaN(kval) ? 0 : '__';
        kval = typeof(kval) == 'undefined' ? '' : kval;
		if (kval == '' || kval == 0)
		{
			foundErr = true;
			errString = err;
			return false;
		}
	});
	if (foundErr)
	{
		showErr && $.notify.alert(errString);
		return false;
	}
	return true;
}

function submitClick(clk)
{
	__click_from_submit = clk;
}

function checkIfClick()
{
	return __click_from_submit;
}

function loadCitys(cid)
{
    $('#allCityList').html('<option value="-1">正在加载</option>');
    $.get('admin.php?mod=product&code=quick&op=listCity&icity='+cid+$.rnd.stamp(), function(data){
        $('#allCityList').html(data);
    });
}

function proIfoAddCity()
{
    // open dialog
    art.dialog({
        title: '添加城市',
        content: document.getElementById('OPBox_addCity'),
         button: [
            {
                name: '保存',
                callback: function(){
                    $.notify.loading('正在添加...');
                    var $cityName = $('#opb_addcity_name').val();
                    var $cityFlag = $('#opb_addcity_flag').val();
                    var opener = this;
                    $.get('admin.php?mod=product&code=quick&op=addCity&name='+encodeURIComponent($cityName)+'&flag='+encodeURIComponent($cityFlag), function(data){
                        $.notify.loading(false);
                        if (!isNaN(data))
                        {
                            opener.close();
                            loadCitys(data);
                            loadSellers(data);
                        }
                        else
                        {
                            $.notify.failed(data);
                        }
                    });
                    return false;
                }
            },
            {
                name: '关闭',
                callback: function(){
                    this.close();
                }
            }
         ]
    });
}

function loadSellers(cid, sid)
{
    if(cid == '' || cid == 0)
    {
        return;
    }
    sid = (sid != '' && typeof(sid) != 'undefined') ? sid : 0;
    $.get('ajax.php?mod=getseller&city='+cid+'&seller='+sid+$.rnd.stamp(), function(data){
        $('#allSellerList').html(data);
    });
}

function proIfoAddSeller()
{
    // process dialog
    var $cityID = $('#allCityList').val();
    if (isNaN($cityID) || $cityID <= 0)
    {
        $.notify.alert('请先选择投放城市！');
        return;
    }
    $cityID = parseInt($cityID);
    art.dialog({
        title: '添加商家',
        content: document.getElementById('OPBox_addSeller'),
         button: [
            {
                name: '保存',
                callback: function(){
                    $.notify.loading('正在添加...');
                    var $userName = $('#opb_addseller_username').val();
                    var $sellerName = $('#opb_addseller_sellername').val();
                    var opener = this;
                    $.get('admin.php?mod=product&code=quick&op=addSeller&city='+$cityID+'&username='+encodeURIComponent($userName)+'&sellername='+encodeURIComponent($sellerName), function(data){
                        $.notify.loading(false);
                        if (!isNaN(data))
                        {
                            opener.close();
                            loadSellers($cityID, data);
                        }
                        else
                        {
                            $.notify.failed(data);
                        }
                    });
                    return false;
                }
            },
            {
                name: '关闭',
                callback: function(){
                    this.close();
                }
            }
         ]
    });
}

function ifoShowHelper(item)
{
    art.dialog({
        title: '帮助手册',
        icon: 'question',
        lock: true,
        content: document.getElementById('helper_of_'+item),
        yesText: '知道了',
        yesFn: true
    });
}

function dsp_payment_list($DSP)
{
    var tar = $('#dsp_payment_list');
    $DSP ? tar.show() : tar.hide();
}

function load_product_tag(product_id, retryv) {
	$.get('admin.php?mod=tag&code=view&product_id=' + product_id + '&retry=' + retryv, function(data){
		$('#product_tag_view').html(data);
	});	
}
function product_tag_mgr(product_id) {
	// open dialog
    art.dialog({
        title: '标签设置',
        content: document.getElementById('OPBox_productTag'),
         button: [
            {
                name: '保存',
                callback: function(){
                    $.notify.loading('正在保存...');
                    var opener = this;
                    $.post($('#tag_list_mgr_form').attr('action') + '&in_ajax=1', $('#tag_list_mgr_form').serialize(), function(data){
                        $.notify.loading(false);
                        if (!isNaN(data))
                        {
                            opener.close();
							load_product_tag(product_id, 'must');
                        }
                        else
                        {
                            $.notify.failed(data);
                        }
                    });
                    return false;
                }
            },
            {
                name: '关闭',
                callback: function(){
                    this.close();
					load_product_tag(product_id);
                }
            }
         ]
    });
}

function product_tag_delete(product_id, tag_id) {
	// open dialog
    art.dialog({
        title: '标签删除',
        content: '删除后的内容不可恢复，确认删除？',
         button: [
            {
                name: '确认删除',
                callback: function(){
                    $.notify.loading('正在删除...');
                    var opener = this;
                    $.get('admin.php?mod=tag&code=delete&product_id=' + product_id + '&tag_id=' + tag_id, function(data){
                        $.notify.loading(false);
						opener.close();
                        $('#product_' + product_id + '_tag_' + tag_id).remove();
						load_product_tag(product_id);
                    });
                    return false;
                }
            },
            {
                name: '关闭',
                callback: function(){
                    this.close();
                }
            }
         ]
    });
}