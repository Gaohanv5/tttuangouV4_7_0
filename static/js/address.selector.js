/** * @copyright (C)2014 Cenwor Inc. * @author Cenwor <www.cenwor.com> * @package js * @name address.selector.js * @date 2014-12-11 14:44:49 */ var __a_s_new_loaded = false;

$(document).ready(function(){
	$('input[name^="address_id"]').bind(
				'click',
				function(){change_of_address_select(this)});
	$('#address_new select').bind('change', function(){region_loads($(this))});
	$('#address_submit_button').bind('click', function(){address_submit()});
	$('#address_new_form').validationEngine();
	$.hook.add('checkout_submit', function(){
		var addrObj = $('input[name^="address_id_"]');//.val();
		var howManyRadioGroup = 0;
		var tmpProductidObj = {};
		//计算当前地址的radio组数
		$.each(addrObj,  function (i,n){
			var productid = $(n).attr('productid');
			if(tmpProductidObj[productid] == undefined)
			{
				tmpProductidObj[productid] = productid;
				howManyRadioGroup++;
			}
		});
		//有效组数
		var effectiveRadioGroup = 0;
		$.each(tmpProductidObj , function (i,n){
			var addr = $('input[name="address_id_'+n+'"]:checked').val();
			var productid = n;
			if (addr == 0 || addr == undefined)
			{
				$('input[name=address_id_'+productid+']:first').tipTip({
					content:"请选择一个有效的收货地址！",
					keepAlive:true,
					activation:"focus",
					defaultPosition:"top",
					edgeOffset:8,
					maxWidth:"300px"
				});
				$('input[name=address_id_'+productid+']:first').focus();
				df_allow_to_submit('address.selector', false);
			}
			else
			{
				effectiveRadioGroup++;
				checkout_field_append('address_id['+productid+']', addr);
				df_allow_to_submit('address.selector', true);
			}
			
		});
		if(effectiveRadioGroup < howManyRadioGroup)
			df_allow_to_submit('address.selector', false);
		
	});
	// check default selected delay 1s
//	setTimeout(change_of_address_select, 1000);
});

//firefox下刷新，单选依然选中，需取消
function _clean_all_checkbox()
{
//	$("input[type=radio]").attr('checked',false);
}

function change_of_address_select(obj)
{
	var productid = $(obj).attr('productid');
	var addr = $('input[name="address_id_'+productid+'"]:checked').val();
	if (addr == undefined)
	{
		_clean_all_checkbox();
		return;
	}
	if(productid == undefined)
	{
		_clean_all_checkbox();
	}
	if (addr == 0)
	{
//		$.hook.call('address_rewrite');
		express_display_none();
		$('#address_new').slideDown();
		if (!__a_s_new_loaded) address_form_init();
	}
	else
	{
		$('#address_new').slideUp();
//		$.hook.call('address_change', addr);
		
		var str = productid.trim();
		var arr=str.split(","); 
		for (i=0;i<arr.length ;i++ ){
			//因为小背篓，所以屏蔽
			//express_display(addr,arr[i]);
		}
		
		//express_display(addr,productid);
	}
}
function address_form_init()
{
	region_loads(null);
	__a_s_new_loaded = true;
}
function region_loads(obj)
{
	var tpl_select = '<option value="">请选择</option>';
	var tpl_loader = '<option value="">加载中</option>';
	if (obj == null)
	{
		$('#addr_province').html(tpl_loader);
		$('#addr_city').html(tpl_select);
		$('#addr_country').html(tpl_select);
		$.get('?mod=misc&code=region&parent=0', function(data){
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
		$.get('?mod=misc&code=region&parent='+parent, function(data){
			$('#addr_city').html(tpl_select+data);
		});
	}
	else if (id == 'addr_city')
	{
		$('#addr_country').html(tpl_loader);
		$.get('?mod=misc&code=region&parent='+parent, function(data){
			$('#addr_country').html(tpl_select+data);
		});
	}
}
function address_submit()
{
	var options = {
		beforeSubmit: function(){
			var checks = $('#address_new').validationEngine('validate');
			if (!checks) return false;
			$('#address_submit_button').attr('disabled', true);
		},
		url: '?mod=misc&code=address&op=save',
		success: function(data){
			eval('var data='+data);
			if (data.status != 'ok')
			{
				$('#address_submit_result').text(data.msg);
			}
			else
			{
				window.location = window.location;
				var li_radio = '';
				li_radio += $('#addr_name').val()+' - ';
				li_radio += $('#addr_province option:selected').text()+' ';
				li_radio += $('#addr_city option:selected').text()+' ';
				var li_radio_country = $('#addr_country option:selected').text();
				if (li_radio_country != '请选择')
				{
					li_radio += li_radio_country+' ';
				}
				li_radio += $('#addr_address').val()+' - ';
				li_radio += $('#addr_callphone').val();
				$('#li_address_new').before('<li><input type="radio" name="address_id" value="'+data.id+'" checked="checked" /> '+li_radio+'</li>');
				change_of_address_select();
			}
			$('#address_submit_button').attr('disabled', false);
		}
	};
	$('#address_new_form').ajaxSubmit(options);
}
