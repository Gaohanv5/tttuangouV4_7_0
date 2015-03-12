/** * @copyright (C)2014 Cenwor Inc. * @author Cenwor <www.cenwor.com> * @package js * @name buy.checkout.js * @date 2014-12-11 14:44:49 */ var _allow_to_submit = {};
var __reg_price_list = {};

$(document).ready(function(){
	
});

function fizinit()
{
	$("input[name^='num_buys']").bind('blur', function(){change_of_num_buys(product_array);});
	change_of_num_buys(product_array);
	$('#checkout_submit').bind('submit', function(){return checkout_submit();});
}

function change_of_num_buys(productsId)
{
	//价格复位为空
	delete __reg_price_list['product'];
	$.each( productsId, function(i, n){
		  _change_of_num_buys(product_array,i);
		});
	//_change_of_num_buys()
}

//@param product_array
//@param key = product_array[key]
function _change_of_num_buys(product_array,key)
{
	var product = product_array[key];
	var obj = $('#num_buys\\['+product.productid+'\\]');
	var num = parseInt(obj.val());
	var oncemin = product.oncemin;
	var oncemax = product.oncemax;
	var surplus = product.surplus;
	var price   = product.price;
	var tips = '';
	if (isNaN(num))
	{
		obj.val(oncemin);
	}
	else if (num < oncemin)
	{
		tips = '您最少需要购买'+oncemin+'件商品才能参团！';
		obj.val(oncemin);
	}
	else if (oncemax > 0 && num > oncemax)
	{
		tips = '您最多只能购买'+oncemax+'件商品！';
		obj.val(oncemax);
	}
	else if (num > surplus)
	{
		tips = '本次'+ tuangou_str +'只剩余'+surplus+'件商品了！';
		obj.val(surplus);
	}
	if (tips != '')
	{
		obj.tipTip({
			content:tips,
			keepAlive:true,
			activation:"focus",
			defaultPosition:"top",
			edgeOffset:8,
			maxWidth:"300px"
		});
		obj.focus();
		num = parseInt(obj.val());
		//订单总价
		if(typeof(__reg_price_list['product']) == 'undefined')
		{
			__reg_price_list['product'] = {value : price*num};
		}
		else
		{
			__reg_price_list['product'] = {value : __reg_price_list['product'].value + price*num};
		}
		$.hook.callEx('attrs_num_change', product.productid);
		$.hook.callEx('buys_num_change', num,product.productid);
	}
	else
	{
		//订单总价
		if(typeof(__reg_price_list['product']) == 'undefined')
		{
			__reg_price_list['product'] = {value : price*num};
		}
		else
		{
			__reg_price_list['product'] = {value : __reg_price_list['product'].value + price*num};
		}
		$.hook.callEx('attrs_num_change', product.productid);
//		$.hook.call('buys_num_change', num);
		$.hook.callEx('buys_num_change', num,product.productid);
//		express_price_calc(num,product.productid);
	}
	
	//每种商品总价
//	__reg_price_list['product']['product'+product.productid] = {value : price*num};
//	 id="price_product_{$product['id']}"
	$('#price_product_' + product.productid).text(price*num.toFixed(2));
	
//	price_calc();
	price_calc(num,product.productid);
    if (typeof(product.weight) != 'undefined')
    {
        weight_calc(num,product.productid);
    }
}
/**
 * 统计价格
 */
function price_calc(num,pid)
{
	if (parseInt(num) > 0)
	{

	}
	else
	{
		var num_buys_id = escape_string('#num_buys['+pid+']')
		num = parseInt($(num_buys_id).val());
	}
	var total = 0;
	$.each(__reg_price_list, function(item, price) {
		if (!isNaN(parseFloat(price.value)))
		{
			//product、express数据
			if(item.match(/cat/gi) == null)
			{
				var i_psv = parseFloat(price.value);
			}
			else//cat_f数据
			{
				var i_psv = price.csingle ? (parseFloat(price.value) * price.num) : parseFloat(price.value);
			}
			var item = escape_string(item);
			$('#price_' + item).text(i_psv.toFixed(2));
			total += i_psv;
		}
	});	//二次开发 start	var card_price = $('#card_price').text();	total -= parseFloat(card_price);	if (total < 0){		$('#price_total').text('0.00');	}else{		$('#price_total').text(total.toFixed(2));	}	//二次开发 end
	//$('#price_total').text(total.toFixed(2));
}
/**
* 计算重量
*/
function weight_calc(num)
{
	var productid = arguments[1] ? arguments[1] : false;
//	alert(num+'='+productid);
	
	for(i in product_array)
	{
		if(product_array[i].productid == productid)
		{
			if(product_array[i].weight != undefined)
			{
				//product_weight_17
				var wall = product_array[i].weight * num;
			    var dsp =  wall>=1000?(Math.round(wall/10)/100)+' Kg':wall+' g';
			    $('#product_weight_'+productid).html(dsp);
			}
		}
	}
	
//    var wall = weight * num;
//    var dsp =  wall>=1000?(Math.round(wall/10)/100)+' Kg':wall+' g';
//    $('#product_weight_'+productid).html(dsp);
}
/**
 * 注册价格计费种类
 * @param string id
 * @param string name
 * @param string calcMode
 */
function price_type_reg(id, name, calcMode)
{
	var fid = 'price_'+id;
	var price = __reg_price_list[id];
	if (price != undefined) return false;
	__reg_price_list[id] = {value : 0, csingle : calcMode == 'single' ? true : false};
	// 增加显示
	$('#tr_price_total').before('<tr id="tr_'+fid+'"><td class="left"><font id="'+fid+'_name">'+name+'</font></td><td class="right">&yen; <font id="'+fid+'" class="price">0</font></td></tr>');
}
/**
 * 检查计费名称是否存在
 * @param string id
 */
function price_type_exists(id)
{
	return __reg_price_list[id] == undefined ? false : true;
}
/**
 * 更改计费名称
 * @param string id
 * @param string newName
 * @param string calcMode
 */
function price_type_change(id, newName, calcMode)
{
	var fid = 'price_'+id+'_name';
	if ($('#'+fid).length < 1)
	{
		price_type_reg(id, newName, calcMode);
	}
	else
	{
		$('#'+fid).text(newName);
	}
}
/**
 * 删除计费名称
 * @param string id
 */
function price_type_remove(id)
{
	delete __reg_price_list[id];
	var id = id.replace(/\[/g,'\\[');
		id = id.replace(/\]/g,'\\]');
	$('#tr_price_'+id).remove();
	price_calc(0,id);
}
/**
 * 更改计费价格
 * @param string id
 * @param integer price
 * @param string calcMode
 */
function price_change(id, price, calcMode)
{
	__reg_price_list[id].value = price;
	if (calcMode)
	{
		__reg_price_list[id].csingle = calcMode == 'single' ? true : false;
	}
	var pid = id.match(/\[([^\[\]]*)\]?/gi);
	//id有的在_后，有的在[]中
	if(pid == null)//_后
	{
		pid = id;
	}
	else//[]中
	{
		pid = trimc(trimc(pid[0],'['),']');
	}
	
	var num = $("#num_buys\\["+pid+"\\]").val();
	__reg_price_list[id].num = num;
	price_calc(num,pid);
}
//去除字符串前后指定的字符
//@example trim('[22]','[') = '22]'
function trimc(s,delS)
{
	return s.replace(delS,"");
}

/**
 * 查询表单字段是否存在
 * @param string name
 */
function checkout_field_exists(name)
{
	var fid = 'field_'+name;
	return $('#'+fid).length > 0 ? true : false;
}
/**
 * 添加一个表单字段
 * @param {Object} name
 * @param {Object} value
 */
function checkout_field_append(name, value)
{
	if (checkout_field_exists(name))
	{
		checkout_field_update(name, value);
	}
	else
	{
		var fid = 'field_'+name;
		$('#product_id').after('<input id="'+fid+'" type="hidden" name="'+name+'" value="'+value+'" />');
	}
}
/**
 * 更新表单数据
 * @param string name
 * @param mixed value
 */
function checkout_field_update(name, value)
{
	if (checkout_field_exists(name))
	{
		var fid = 'field_'+name;
		$('#'+fid).val(value);
	}
	else
	{
		checkout_field_append(name, value);
	}
}
/**
 * 删除表单字段
 * @param string name
 */
function checkout_field_remove(name)
{
	if (checkout_field_exists(name))
	{
		var fid = 'field_'+name;
		$('#'+fid).remove();
	}
}
/**
 * 订单提交
 */
function checkout_submit()
{
	$.hook.call('checkout_submit');
	if (if_allow_to_submit())
	{
		var num = parseInt($('#num_buys').val());
		
		var num_array = '';
		$("input[name*='num_buys']").each(function(i){
		   checkout_field_append('num_buys['+$(this).attr('productid')+']', $(this).val());
		 });		//二次开发 start		var arr_card_id = '';		$("input[name*='card_id']:checked").each(function(){			checkout_field_append('card_price_id[]', $(this).val());		});		//二次开发 end
		$('#checkout_submit').ajaxSubmit({
			beforeSubmit: function()
			{
				$('#checkout_submit_button').attr('disabled', true);
				$('#submit_status').text('正在为您生成订单，请稍候...');
				$('#submit_status').css('display', 'inline');
			},
			success: function(data)
			{
				try {
					eval('var data='+data);
				} catch(e) {
					$('#submit_status').text('服务端错误，请重试！');
					return;
				}
				if (data.status == 'fail')
				{
					$('#submit_status').text(data.msg);
				}
				else if(data.status == 'ok_fail')
				{
					$('#submit_status').text('部分订单出现'+data.msg+'错误,对于已经获取到订单，正在跳转...');
					order_finish(data.id);
				}
				else
				{
					$('#submit_status').text('已经获取到订单，正在跳转...');
					order_finish(data.id);
				}
				$('#checkout_submit_button').attr('disabled', false);
			}
		});
	}
	return false;
}

function df_allow_to_submit(key, allow)
{
    _allow_to_submit[key] = allow;
}

function if_allow_to_submit()
{
//	return true;//yyf
    var _allow = true;
    $.each(_allow_to_submit, function(key, allow){
        if (!allow)
        {
            _allow = false;
        }
    });
    return _allow;
}

//将字符串中的[和]字符前加上转义符/
function escape_string(string)
{
	var string = string.replace(/\[/g,'\\[');
	return string.replace(/\]/g,'\\]');
}