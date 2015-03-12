/** * @copyright (C)2011 Cenwor Inc. * @author Moyo <dev@uuland.org> * @package js * @name order.process.js * @date 2011-11-15 13:48:33 */ var IMG_LOADING = 'templates/admin/images/btn_loading.gif';
var $__doServiceURIExt = '';

$(document).ready(function(data){
	 tb_init('a.thickbox');
	 $('.service').bind('click', function(){
		if (!orderProcessSpecial(this)) if (!confirm('确认提交吗？'))return;else doService(this);
	 });
});

function OrderRemark()
{
	var old = $('#remark').attr('title');
	var mark = $('#remark').val();
	if (mark == old)
	{
		return;
	}
	var TMP_id = 'img_loading_remark';
	$('#remark_btn').after('<img id="'+TMP_id+'" style="margin-left:10px;" src="'+IMG_LOADING+'" />');
	$.get('?mod=order&code=remark&oid='+__Global_OID+'&text='+encodeURIComponent(mark)+$.rnd.stamp(), function(data){
		if (data == 'ok')
		{
			$('#remark').attr('title', mark);
		}
		else
		{
			$.notify.failed('更新失败！');
		}
		$('#'+TMP_id).remove();
	});
}

function OrderExtmsgReply()
{
	var old = $('#extmsg_reply').attr('title');
	var mark = $('#extmsg_reply').val();
	if (mark == old)
	{
		return;
	}
	var TMP_id = 'img_loading_extmsg_reply';
	$('#extmsg_reply_btn').after('<img id="'+TMP_id+'" style="margin-left:10px;" src="'+IMG_LOADING+'" />');
	$.get('?mod=order&code=extmsg&op=reply&oid='+__Global_OID+'&text='+encodeURIComponent(mark)+$.rnd.stamp(), function(data){
		if (data == 'ok')
		{
			$('#extmsg_reply').attr('title', mark);
		}
		else
		{
			$.notify.failed('更新失败！');
		}
		$('#'+TMP_id).remove();
	});
}

function doService(obj)
{
	var mark = $('#opmark').val();
	$('#service_result').text('正在提交，请稍候...');
	var lnk = $(obj).attr('href')+$__doServiceURIExt;
	$.get(lnk+'&oid='+__Global_OID+'&mark='+encodeURIComponent(mark)+$.rnd.stamp(), function(data){
		if (data == 'ok')
		{
			$.notify.loading('提交成功，正在刷新状态...');
			setTimeout(function(){wf_page_loading(false);window.location = window.location;}, 500);
		}
		else
		{
			$.notify.alert(data ? data : '提交失败！请刷新重试');
		}
		$__doServiceURIExt = '';
	});
}

function orderProcessSpecial(obj)
{
	var lnk = $(obj).attr('href');
	if (lnk == '?mod=order&code=refund' || lnk == '?mod=order&code=cancel')
	{
		if (__ORDER_PAID == false)
		{
			// 未支付的订单不需要退款
			return false;
		}
		art.dialog.confirm(document.getElementById('service_refund_area'), function(){
			var rfm = parseFloat($('#service_refund_money').val()).toFixed(2);
			if (isNaN(rfm))
			{
				$.notify.alert('退款金额输入无效，请重新操作！');
				return;
			}
			$.notify.loading('正在提交中，请稍候...');
			$__doServiceURIExt = '&refundMoney='+rfm.toString();
			doService(obj);
		});
		return true;
	}
	return false;
}