/** * @copyright (C)2014 Cenwor Inc. * @author Cenwor <www.cenwor.com> * @package js * @name express.selector.js * @date 2014-12-11 14:44:49 */ var __express_for_address_id = '';
var __express_Sel = {};

$(document).ready(function(){
	var effectiveRadioGroup = 0;
	var howManyRadioGroup = 0;
	$.hook.add('address_change', function(aid){express_display(aid)});
	$.hook.add('address_rewrite', function(){express_display_none()});
	$.hook.add('checkout_submit', function(){
		var expObj = $('input[name^="express_id_"]');//.val();
		var howManyRadioGroup = 0;
		var tmpProductidObj = {};
		//计算当前地址的radio组数
		$.each(expObj,  function (i,n){
			var productid = $(n).attr('productid');
			if(tmpProductidObj[productid] == undefined)
			{
				tmpProductidObj[productid] = productid;
				howManyRadioGroup++;
			}
		});
		//有效组数
		var effectiveRadioGroup = 0;
		$.each( tmpProductidObj, function(i, n){
			  //Item #0: exp_sel_1
			  //Item #1: exp_sel_4
				var exp = $('input[name="express_id_'+n+'"]:checked').val();
				var productid = n;
				if (exp == 0 || exp == undefined)
				{
					$('input[name=express_id_'+productid+']:first').tipTip({
						content:"请选择一个有效的快递方式！",
						keepAlive:true,
						activation:"focus",
						defaultPosition:"top",
						edgeOffset:8,
						maxWidth:"300px"
					});
					$('input[name=express_id_'+productid+']:first').focus();
					df_allow_to_submit('express.selector', false);
				}
				else
				{
					checkout_field_append('express_id['+productid+']', exp);
					df_allow_to_submit('express.selector', true);
				}
		});
		
	});
	if(effectiveRadioGroup < howManyRadioGroup)
		df_allow_to_submit('express.selector', false);
	$.hook.add('buys_num_change', function(num,productid){
		express_price_calc(num,productid);
	});
});

//@param fu 首重 
//@param fp 首重金额 
//@param cu 续重 
//@param cp 续重金额 
//@param 隐藏参数 productIdNum 该商品的个数
function express_price_change(fu, fp, cu, cp)
{
	var productidS = arguments[4] ? arguments[4] : false; 
	//var str = productid.trim();
		var productid=String(productidS).split("-"); 
		for (i=0;i<productid.length ;i++ )
		{
			__express_Sel[productid[i]] = {};
			__express_Sel[productid[i]].fu = fu;
			__express_Sel[productid[i]].fp = fp;
			__express_Sel[productid[i]].cu = cu;
			__express_Sel[productid[i]].cp = cp;
			var num = $("#num_buys\\["+productid[i]+"\\]").val();
			express_price_calc(parseInt(num),productid[i]);
		}
}

function express_price_calc(num)
{   
	var productid = arguments[1] ? arguments[1] : false;
	if( __express_Sel[productid] == undefined)
	{
		__express_Sel[productid] = {};
		__express_Sel[productid].fu = 1;
		__express_Sel[productid].fp = 0;
		__express_Sel[productid].cu = 2;
		__express_Sel[productid].cp = 0;
	}
	var weight = 0;
	var AW = 0;
	for(i in product_array)
	{
		//小背篓定制，屏蔽，不可删除
//		if(product_array[i].productid == productid)
//		{
			
			
			if(product_array[i].weight != undefined)
			{
				var num = $("#num_buys\\["+product_array[i].productid+"\\]").val();
				weight = product_array[i].weight;
				AW = AW + num * weight;
			}
//		}
	}
//	var AW = num * weight;
	var price = __express_Sel[productid].fp;
	if (AW > __express_Sel[productid].fu)
	{
		var LW = AW - __express_Sel[productid].fu;
		if (__express_Sel[productid].cu <=0)
		{
			__express_Sel[productid].cu = 1;
		}
		price += Math.ceil(LW / __express_Sel[productid].cu) * __express_Sel[productid].cp;
	}
//	price_type_reg('express['+productid+']', '快递费用');
//	price_change('express['+productid+']', price);
	price_type_reg('express', '快递费用');
	price_change('express', price);
}

//@param aid 地址id
//@param pid 产品id
//@example express_display(516);
//         express_display(516,71);
function express_display(aid)
{
	var productid = arguments[1] ? arguments[1] : false; 
	//仅仅适用于一层，不适合地址ui多次循环
	//if (aid == __express_for_address_id) return false;
	if ($.cache.check(aid+productid))
	{
		$('#express_list_'+productid).html($.cache.get(aid+productid)).show();
		$('#address_first_'+productid).css('display', 'none');
		__express_for_address_id = aid+productid;
	}
	else
	{
		$('#address_first').text('正在根据您的地址加载配送方式...').css('display', 'block');
		$.get('?mod=misc&code=express&op=list&aid='+aid+'&pid='+productid+$.rnd.stamp(), function(data){
			try {
				eval('var data='+data);
			} catch(e) {
				$('#address_first').text('服务端错误，请重试！');
				return;
			}
			if (data.status != 'ok')
			{
				$('#address_first').text('无法加载配送列表！');
			}
			else
			{
				var tmp = product_array;
				var html = '<ul>';
				$.each(data.html, function(i, exp){
					html += '\
					<li>\
						<input productid="'+productid+'" id="exp_sel_'+exp.id+'" type="radio" name="express_id_'+productid+'" value="'+exp.id+'" onclick="express_price_change('+exp.firstunit+', '+exp.firstprice+','+exp.continueunit+','+exp.continueprice+','+productid+');">\
						<label for="exp_sel_'+exp.id+'" style="float:none">\
						'+exp.name+' - 首重：'+(exp.firstunit>=1000?(Math.round(exp.firstunit/10)/100)+' Kg':exp.firstunit+' g')+'/'+exp.firstprice+'元，续重：'+(exp.continueunit>=1000?exp.continueunit/1000+' Kg':exp.continueunit+' g')+'/'+exp.continueprice+'元\
						</label>\
						<p class="detail"'+(exp.detail == '' ? ' style="display:none;"' : '')+'>'+exp.detail+'</p>\
					</li>';
				});
				html += '</ul>';
				if (html == '<ul></ul>')
				{
					$('#address_first').text('对不起，您的地址暂时无法配送！');
				}
				else
				{
					$.cache.set(aid+productid, html);
					express_display(aid,productid);
				}
			}
		});
	}
	return true;
}

function express_display_none()
{
	$('#express_list').hide();
	$('#address_first').text('请先选择收货地址！').css('display', 'block');
	__express_for_address_id = 0;
}
