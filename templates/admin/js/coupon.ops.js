function couponAlert(id)
{
	couponOping(id);
	$.get('?mod=coupon&code=alert&id='+id+$.rnd.stamp(), function(data){
		if (data != 'ok')
		{
			$.notify.alert('提醒失败！');
		}
		couponOping(id, 'end');
		$.notify.success('已经成功提醒！');
	});
}
function couponReissue(id)
{
	couponOping(id);
	$.get('?mod=coupon&code=reissue&id='+id+$.rnd.stamp(), function(data){
		if (data != 'ok')
		{
			$.notify.alert('通知失败！');
		}
		couponOping(id, 'end');
		$.notify.success('已经成功通知！');
	});
}
function couponDelete(id)
{
	if (!confirm('确认删除吗？')) return;
	couponOping(id);
	$.get('?mod=coupon&code=delete&id='+id+$.rnd.stamp(), function(data){
		if (data == 'ok')
		{
			couponOping(id, 'close');
		}
		else
		{
			$.notify.alert('删除失败！');
			couponOping(id, 'end');
		}
	});
}
function couponOping(id, op)
{
	if (op == undefined)
	{
		$('#cp_on_'+id).removeClass().addClass('oping');
		return;
	}
	if (op == 'end')
	{
		$('#cp_on_'+id).removeClass();
		return;
	}
	if (op == 'close')
	{
		$('#cp_on_'+id).fadeOut();
		return;
	}
}