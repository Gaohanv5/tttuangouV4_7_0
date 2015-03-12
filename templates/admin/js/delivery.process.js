/** * @copyright (C)2011 Cenwor Inc. * @author Moyo <dev@uuland.org> * @package js * @name delivery.process.js * @date 2011-11-15 13:48:33 */ $(document).ready(function(){
	$('#submiter').bind('click', function(){submitTrackingNo(false)});
	$('#cdpServiceButton').bind('click', function(){cdpServiceOpen(false)});
});

function submitTrackingNo(BTN, OID, TNO)
{
	if (!confirm('确定提交吗？')) return;
	var submiter = BTN ? $(BTN) : $('#submiter');
	submiter.val('正在登记').attr('disabled', 'disabled');
	var trackingno = TNO ? TNO : $('#trackingno').val();
	OID = OID ? OID : __Global_OID.toString();
	$.get('?mod=delivery&code=upload&op=single&oid='+OID+'&no='+trackingno+$.rnd.stamp(), function(data){
		if (data != 'ok')
		{
			submiter.val('保存失败');
		}
		else
		{
			submiter.val('保存成功');
		}
	});
}

function cdpServiceOpen(OID)
{
	var sender = $('#cdpAddressID').val();
	if (!sender)
	{
		alert('您还没有选择发货人，不能进行运单打印！');
		return;
	}
	OID = OID ? OID : __Global_OID.toString();
	window.open('?mod=print&code=delivery&oid='+OID+'&sender='+sender.toString()+$.rnd.stamp());
}