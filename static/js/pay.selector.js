/** * @copyright (C)2014 Cenwor Inc. * @author Cenwor <www.cenwor.com> * @package js * @name pay.selector.js * @date 2014-09-01 17:24:23 */ var __use_surplus = false;
//$(document).ready(function(){
	$.hook.add('order_submit', function(){
		var payd = $('#paytype_list input[name=payment_id]:checked').val();
		if (payd == 0 || payd == undefined)
		{
			$('#paytype_list input[name=payment_id]:first').tipTip({
				content:"请选择一个有效的支付方式",
				keepAlive:true,
				activation:"focus",
				defaultPosition:"bottom",
				edgeOffset:8,
				maxWidth:"300px"
			});
			$('#paytype_list input[name=payment_id]:first').focus();
			_allow_to_submit = false;
		}
		else
		{
			order_field_append('payment_id', payd);
			_allow_to_submit = true;
		}
	});
	$('#payment_use_surplus').bind('change', function(){
		var left = $('#payment_clear').offset().left;
		var showAni = {
			left: left+'px'
		};
		var hideAni = {
			left: '-1000px'
		};
		if ($(this).attr('checked'))
		{
			$('#payment_remain_money').show();
			$('#payment_total_money').hide();
			order_field_append('payment_use_surplus', 'true');
		}
		else
		{
			$('#payment_total_money').show();
			$('#payment_remain_money').hide();
			$("#order_submit > input[name=payment_use_surplus]").remove();
		}
	});
//});

//function pay_tr_mouseover(obj)
//{
//	$(obj).css('background', '#FFFAE3');
//}
//function pay_tr_mouseout(obj)
//{
//	$('.pay_tr').css('background', 'none');
//}
function pay_tr_click(pid)
{
	$('#payment_id_'+pid.toString()).attr('checked', 'checked');
	var xuan =$(this).next().children().attr('colspan');
	$("input[name=PaymentType]").attr('checked',false);
    if(xuan == undefined){
        $("#order_submit").removeAttr("target");
        $("#order_submit > input[name=payment_id]").remove();
        $("#order_submit > #ibank").remove();
        $("#order_submit").removeAttr("onsubmit");
        $('#order_submit').bind('submit', function(){return order_submit()});
    }
}