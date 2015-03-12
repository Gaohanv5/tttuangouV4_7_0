/** * @copyright (C)2011 Cenwor Inc. * @author Moyo <dev@uuland.org> * @package js * @name recharge.order.ops.js * @date 2014-05-08 15:05:45 */ $(document).ready(function(){
    rechargeOrderClean();
});

function rechargeOrderConfirm(orderid)
{
	if (!confirm('确认充值后资金会充入账户余额，确定要继续吗？'))
	{
		return;
	}
	$.notify.loading('正在确认中...');
	$.get('?mod=recharge&code=order&op=confirm&orderid='+orderid, function(data){
		$.notify.loading();
		if (data == 'ok')
		{
			$.notify.success('确认充值成功！');
		}
		else
		{
			$.notify.failed(data);
		}
	});
}

function rechargeOrderClean()
{
    $.get('admin.php?mod=recharge&code=order&op=clean'+$.rnd.stamp(), function(data){
        if (data != 'no')
        {
            $('#recharge_order_clean').html(data).css('display', 'none').fadeIn();
            setTimeout(function(){$('#recharge_order_clean').css('position', 'absolute').animate({top:'-300px'})}, 3000);
        }
    });
}