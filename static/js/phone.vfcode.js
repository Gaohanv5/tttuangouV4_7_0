/** * @copyright (C)2014 Cenwor Inc. * @author Cenwor <www.cenwor.com> * @package js * @name phone.vfcode.js * @date 2014-10-30 10:42:14 */ 
function vfcode_send() {
	var phone = $("#phone").val();
	if('' == phone) {
		phone = $("#sms_phone").val();
	}
	if('' == phone) {
		reg_alert('phone', '手机号不能为空');
		return false;
	}
	if (phone.length != 11) {
		reg_alert('phone', '请输入正确的手机号码！');
		return false;
	}
	$.post('index.php?mod=phone&code=vfsend', { 'phone' : phone }, function (r) {
		if(r) {
			reg_alert('phone', r);
			return false;
		} else {
			reg_success('phone', '验证码已经发送！');
			return true;
		}
	});
	 
}
