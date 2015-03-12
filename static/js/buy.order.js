/** * @copyright (C)2011 Cenwor Inc. * @author Moyo <dev@uuland.org> * @package js * @name buy.order.js * @date 2014-05-08 15:05:45 */ var _allow_to_submit = true;

$(document).ready(function(){
	
});

function fizinit()
{
	$('#order_submit').bind('submit', function(){
		if($("#express_id").length > 0 && $('#express_id').val()==0){
			$('#submit_status').css('display', 'inline');
			$('#submit_status').text('请选择一个有效的配送方式！');
			return false;
		}else{
			$('#submit_status').css('display', 'none');
			$('#submit_status').text('');
		}
		return order_submit()
	});
}
function selectors(){
	$("input[name=payment_id]").bind("click",function(event){  
        
        var xuan =$(this).parent().parent().next().children().attr('colspan');
        $("input[name=PaymentType]").attr('checked',false);
        if(xuan == undefined){
	        $("#order_submit").removeAttr("target");
	        $("#order_submit > input[name=payment_id]").remove();
	        $("#order_submit > #ibank").remove();
	        $("#order_submit").removeAttr("onsubmit");
	        $('#order_submit').bind('submit', function(){return order_submit()});
        }
    });  
}
function order_field_append(name, value)
{
	$('#order_id').after('<input type="hidden" name="'+name+'" value="'+value+'" />');
}

function order_submit()
{
	$.hook.call('order_submit');
	if (_allow_to_submit)
	{
		$('#order_submit').ajaxSubmit({
			beforeSubmit: function()
			{
				$('#order_submit_button').attr('disabled', true);
				$('#submit_status').text('正在为您配置支付方式，请稍候...');
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
				if (data.status != 'ok')
				{
					$('#submit_status').text(data.msg);
				}
				else
				{
					$('#submit_status').text('已经完成配置，正在跳转到支付页面...');
					order_finish(data.tourl);
				}
				$('#order_submit_button').attr('disabled', false);
			}
		});
	}
	return false;
}
