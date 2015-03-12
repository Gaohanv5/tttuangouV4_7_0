/** * @copyright (C)2014 Cenwor Inc. * @author Cenwor <www.cenwor.com> * @package js * @name coupon.add.js * @date 2014-09-01 17:24:23 */ $(document).ready(function(){
	$('#btn_random').bind('click', GenerateNAP);
	$('#btn_create').bind('click', GenerateCoupon);
});

function GenerateNAP()
{
	$('#number').val(__rand_key(9));
	$('#password').val(__rand_key(3));
}

function GenerateCoupon()
{
	if (!confirm('确认生成？')) return;
	var uid = $('#uid').val();
	var pid = $('#pid').val();
	var oid = $('#oid').val();
	if (uid == '' || pid == '' || oid == '')
	{
		$.notify.alert('UID或PID或OID不能为空！');
		return;
	}
	$('#generate_result').text('正在生成...');
	var number = $('#number').val();
	var password = $('#password').val();
	var mutis = $('#mutis').val();
	$.get('?mod=coupon&code=add&op=save&uid='+uid+'&pid='+pid+'&oid='+oid+'&number='+number+'&password='+password+'&mutis='+mutis+$.rnd.stamp(), function(data){
		if (data == 'ok')
		{
			$('#generate_result').html('<font color="green"><b>已经生成！</b></font>');
			setTimeout(function(){$('#generate_result').text('等待生成');}, 2000);
		}
		else
		{
			$('#generate_result').html(data);
		}
	});
}

/**
 * 随机字符
 */
function __rand_key(length)
{
	var salt = '0123456789';
	var str = '';
	for(var i=0; i<length; i++)
	{
		str += salt.charAt(Math.ceil(Math.random()*100000000)%salt.length);
	}
	return str;
}