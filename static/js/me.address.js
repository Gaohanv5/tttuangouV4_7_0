/** * @copyright (C)2011 Cenwor Inc. * @author Moyo <dev@uuland.org> * @package js * @name me.address.js * @date 2011-11-15 13:48:33 */ var __a_s_post_extend = '';
$(document).ready(function(){
	$('#address_new select').bind('change', function(){region_loads($(this))});
	$('#address_submit_button').bind('click', function(){address_form_submit()});
	$('#address_cancel_button').bind('click', function(){$('#address_new').slideUp()});
	$('#address_new_form').validationEngine();
    if (typeof($__EXPRESS_ADDRESS_MODE) != 'undefined')
    {
        __a_s_post_extend = '&from=admin';
    }
});
var __a_s_form_loaded = false;
function address_add()
{
	if (!__a_s_form_loaded) address_form_init();
	$('#addr_id').val('0');
	var field = new Array('address', 'zip', 'name', 'phone');
	$.each(field, function(i, n){
		$('#addr_'+n).val('');
	});
	$('#address_new').slideDown();
}
function address_edit(aid)
{
	if (!__a_s_form_loaded) address_form_init();
	$.post(
		'index.php?mod=misc&code=address&op=get'+__a_s_post_extend,
		{id: aid},
		function (data){
			eval('var data='+data);
			if (data.status != 'ok')
			{
				alert(data.msg);
				return;
			}
			$.each(data.addr, function (key, val){
				if (key != 'city' && key != 'country')
				{
					$('#addr_'+key).val(val);
				}
			});
			region_loads($('#addr_province'), function(){
				$('#addr_city').val(data.addr.city);
				region_loads($('#addr_city'), function(){
					$('#addr_country').val(data.addr.country);
				});
			});
			$('#address_new').slideDown();
		}
	);
}
function address_del(aid)
{
	if (!confirm('您确认要删除吗？')) return;
	$.post(
		'index.php?mod=misc&code=address&op=del'+__a_s_post_extend,
		{id: aid},
		function(data){
			eval('var data='+data);
			if (data.status != 'ok')
			{
				alert(data.msg);
				return;
			}
			alert('地址已经删除！');
			window.location = window.location;
		}
	);
}
function address_form_submit()
{
	var options = {
		beforeSubmit: function(){
			var checks = $('#address_new').validationEngine('validate');
			if (!checks) return false;
			$('#address_submit_button').attr('disabled', true);
			$('#address_submit_result').text('正在保存...');
		},
		url: 'index.php?mod=misc&code=address&op=save'+__a_s_post_extend,
		success: function(data){
			eval('var data='+data);
			if (data.status != 'ok')
			{
				$('#address_submit_result').text(data.msg);
			}
			else
			{
				$('#address_submit_result').text('保存成功！');
				//setTimeout(function(){$('#address_new').slideUp()}, 1000);
				window.location = window.location;
			}
			$('#address_submit_button').attr('disabled', false);
		}
	};
	$('#address_new_form').ajaxSubmit(options);
}
function address_form_init()
{
	region_loads(null);
	__a_s_form_loaded = true;
}
function region_loads(obj, callback)
{
	var tpl_select = '<option value="">请选择</option>';
	var tpl_loader = '<option value="">加载中</option>';
	if (obj == null)
	{
		$('#addr_province').html(tpl_loader);
		$('#addr_city').html(tpl_select);
		$('#addr_country').html(tpl_select);
		$.get('index.php?mod=misc&code=region&parent=0', function(data){
			$('#addr_province').html(tpl_select+data);
		});
		return;
	}
	var id = obj.attr('id');
	if (id == 'addr_country') return;
	var parent = obj.val();
	if (parent == 0) return;
	if (id == 'addr_province')
	{
		$('#addr_city').html(tpl_loader);
		$('#addr_country').html(tpl_select);
		$.get('index.php?mod=misc&code=region&parent='+parent, function(data){
			$('#addr_city').html(tpl_select+data);
			if (callback != undefined)
			{
				callback();
			}
		});
	}
	else if (id == 'addr_city')
	{
		$('#addr_country').html(tpl_loader);
		$.get('index.php?mod=misc&code=region&parent='+parent, function(data){
			$('#addr_country').html(tpl_select+data);
			if (callback != undefined)
			{
				callback();
			}
		});
	}
}