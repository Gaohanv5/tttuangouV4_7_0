/** * @copyright (C)2014 Cenwor Inc. * @author Cenwor <www.cenwor.com> * @package js * @name attrs.selector.js * @date 2014-12-11 14:44:49 */ $(document).ready(function() {
	attrs_sel_bind();
	attrs_event_hook();
});

var attrs_sel_user_status = {};

function attrs_sel_bind()
{
	$('.pro-attrs-link').bind('click', function() { attrs_sel_changed(this) });
}

function attrs_event_hook()
{
	$.hook.add('attrs_num_change', function(productid){
		attrs_num_change(productid);
	});
	df_allow_to_submit('attrs.selector', false);
	$.hook.add('checkout_submit', function(){
		var allPassed = true;
		$.each($('.xcat'), function(i, ele){
			var cat_id = $(ele).attr('xcat');
			var cat_required = $(ele).attr('xrequired');
			if (cat_required == 'true')
			{
				var lisel = $('li.pro-attrs-link.selected[catfrom='+cat_id+']');
				if (lisel.length > 0)
				{
				}
				else
				{
					$(".pro-attrs-cat-tips").show();
					$(".pro-attrs-cat-tips").html("请选择商品类型！");

					
//					$('#pro-attrs-cat-'+cat_id).tipTip({
//						content:"请至少选择一个属性规格！",
//						keepAlive:true,
//						activation:"focus",
//						defaultPosition:"top",
//						edgeOffset:8,
//						maxWidth:"300px"
//					}).focus();
					allPassed = false;
				}
			}
		});
		if (allPassed)
		{
			df_allow_to_submit('attrs.selector', true);
		}
	});
}

function attrs_sel_changed(element)
{
	var cat = $(element).attr('catfrom');
	var aid = $(element).attr('attrid');
	var pmv = $(element).attr('pricemoves');
	var pid = $(element).attr('productid');
	if (attrs_sel_user_status[cat])
	{
		var aid_last = attrs_sel_user_status[cat];
		if (aid_last == aid)
		{
			// check if reverse
			if ($('#pro-attrs-item-'+aid_last).hasClass('selected'))
			{
				attrs_sel_price('remove', cat, aid, 13,pid);
				delete attrs_sel_user_status[cat];
				$('#pro-attrs-item-'+aid_last).removeClass('selected');
			}
			else
			{
				// clear
				delete attrs_sel_user_status[cat];
			}
		}
		else
		{
			// remove last attr sel
			attrs_sel_price('remove', cat, aid_last, 13,pid);
			// update current
			attrs_sel_price('update', cat, aid, pmv,pid);
			$('#pro-attrs-item-'+aid_last).removeClass('selected');
			$('#pro-attrs-item-'+aid).addClass('selected');
			attrs_sel_user_status[cat] = aid;
		}
	}
	else
	{
		attrs_sel_price('append', cat, aid, pmv,pid);
		$('#pro-attrs-item-'+aid).addClass('selected');
		attrs_sel_user_status[cat] = aid;
	}
}

function attrs_num_change(pid)
{
	$.each(__reg_price_list,function(i, elements){
		var pidA = i.match(/\[([^\[\]]*)\]?/gi);
		if(pidA == null)
		{
			
		}
		else
		{
			if(pidA[0] == '['+pid+']')
			{
				__reg_price_list[i].num = $('#num_buys\\['+pid+'\\]').val();;
			}
		}
		
		
		
	});
	
	
}

function attrs_sel_price(cmd, cat, idx, price,idp)
{
	var pmv = parseFloat(price).toFixed(2);
	var cat_name = $('#pro-attrs-cat-'+cat).text();
	var attr_name = $('#pro-attrs-item-'+idx).text();
	var attr_binding = $('#pro-attrs-item-'+idx).attr('xbinding');
	if (attr_binding == 'true')
	{
		var calcMode = 'single';
	}
	else
	{
		var calcMode = 'mixed';
	}
//	var ptkey = 'cat_f_'+cat+'_'+idx;
	var ptkey = 'cat_f\['+idp+'\]\['+cat+'_'+idx+'\]';
	var ptname = cat_name+' / '+attr_name;
	switch (cmd)
	{
		case 'append' :
			if (pmv != 0)
			{
				price_type_reg(ptkey, ptname, calcMode);
				price_change(ptkey, pmv, calcMode);
			}
			checkout_field_append(ptkey, cat+':'+idx);
			break;
		case 'update' :
			if (price_type_exists(ptkey) || pmv != 0)
			{
				price_type_change(ptkey, ptname, calcMode);
				price_change(ptkey, pmv, calcMode);
			}
			checkout_field_update(ptkey, cat+':'+idx);
			break;
		case 'remove' :
			if (price_type_exists(ptkey))
			{
				price_type_remove(ptkey);
			}
			checkout_field_remove(ptkey);
			break;
	}
}