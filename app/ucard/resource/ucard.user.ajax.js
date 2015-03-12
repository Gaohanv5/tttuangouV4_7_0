/** * @copyright (C)2011 Cenwor Inc. * @author Moyo <dev@uuland.org> * @package js * @name ucard.user.ajax.js * @date 2011-12-07 13:42:08 */ function ucard_user_recharge(uid)
{
	art.dialog.confirm('请输入充值金额：<input type="text" id="ucard_user_recharge_money" value="0.00" /><br/>本次充值说明：：<input type="text" id="ucard_user_recharge_remark" class="ucard_input_text" value="管理员后台充值" title="前台用户可见" />', function(){
		var rfm = parseFloat($('#ucard_user_recharge_money').val()).toFixed(2);
		if (isNaN(rfm))
		{
			$.notify.alert('充值金额输入无效，请重新操作！');
			return;
		}
		$.notify.loading('正在充值中，请稍候...');
		ucard_user_ajax(uid, 'recharge', '&money='+rfm.toString()+'&remark='+encodeURIComponent($('#ucard_user_recharge_remark').val()));
	});
}
function ucard_user_lessmoney(uid)
{
	art.dialog.confirm('请输入扣费金额：<input type="text" id="ucard_user_lessmoney_money" value="0.00" /><br/>本次扣费说明：：<input type="text" id="ucard_user_lessmoney_remark" class="ucard_input_text" value="管理员后台扣费" title="前台用户可见" />', function(){
		var rfm = parseFloat($('#ucard_user_lessmoney_money').val()).toFixed(2);
		if (isNaN(rfm))
		{
			$.notify.alert('扣费金额输入无效，请重新操作！');
			return;
		}
		$.notify.loading('正在扣费中，请稍候...');
		ucard_user_ajax(uid, 'lessmoney', '&money='+rfm.toString()+'&remark='+encodeURIComponent($('#ucard_user_lessmoney_remark').val()));
	});
}
function ucard_user_send_mail(uid)
{
	art.dialog.confirm('邮件标题：<input type="text" id="ucard_user_mail_title" class="ucard_input_text" value="尊敬的用户您好！" /><br/>邮件内容：<textarea id="ucard_user_mail_content" class="ucard_input_textarea"></textarea>', function(){
		var title = $('#ucard_user_mail_title').val();
		var content = $('#ucard_user_mail_content').val();
		$.notify.loading('正在发送中，请稍候...');
		ucard_user_ajax(uid, 'send_mail', '&title='+encodeURIComponent(title)+'&content='+encodeURIComponent(content));
	});
}
function ucard_user_send_sms(uid)
{
	art.dialog.confirm('短信内容：<textarea id="ucard_user_sms_content" class="ucard_input_textarea"></textarea>', function(){
		var content = $('#ucard_user_sms_content').val();
		$.notify.loading('正在发送中，请稍候...');
		ucard_user_ajax(uid, 'send_sms', '&content='+encodeURIComponent(content));
	});
}

function ucard_user_ajax(uid, processor, uri)
{
	$.get('admin.php?mod=app&code=lpc&master=ucard&processor='+processor+'&uid='+uid.toString()+uri+$.rnd.stamp(), function(data){
		$.notify.loading(false);
		try {
			eval('var rps = '+data);
		}
		catch(e) {
			$.notify.failed('APP[UCard]['+processor+'] 运行错误，请检查相关代码和数据结构！');
			return;
		}
		if (rps.status == 'ok')
		{
			$.notify.success(rps.msg);
		}
		else
		{
			$.notify.alert(rps.msg);
		}
	});
}