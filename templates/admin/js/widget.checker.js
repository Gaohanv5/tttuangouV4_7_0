/** * @copyright (C)2014 Cenwor Inc. * @author Cenwor <www.cenwor.com> * @package js * @name widget.checker.js * @date 2014-09-01 17:24:23 */ var FMT_Success = -10001;
var FMT_Failed = -10002;

$(document).ready(function(){
	$.each($('.wchecker'), function(i, one){
		var TMP_id = 'check_status_'+__rand_key();
		$(one).after('<font id="'+TMP_id+'" style="margin-left:10px;"></font>');
		$(one).bind('blur', function(){
			wExistChecks(one, TMP_id);
		});
	});
	KindEditor.ready(function(K) {K.create('#content');});
});

function wExistChecks(box, sid)
{
	var val = $(box).val();
	if (val == '')
	{
		Status(sid, '请输入内容！', FMT_Failed);
		return;
	}
	var regx = /^[a-z0-9_]*$/i;
	if (!regx.test(val))
	{
		Status(sid, '标记只能为纯字符（字符a到z，数字0-9，下划线_）', FMT_Failed);
		return;
	}
	Status(sid, '正在检查...');
	var path = $(box).attr('title')+val;
	$.get('?mod=ini&code=get&path='+path+$.rnd.stamp(), function(data){
		if (data == 'false')
		{
			Status(sid, '此标记可以使用！', FMT_Success);
		}
		else
		{
			Status(sid, '此标记已经被使用，请换一个！', FMT_Failed);
		}
	});
}

function Status(sid, text, format)
{
	if (format == FMT_Success)
	{
		$('#'+sid).html('<font color="green">'+text+'</font>');
	}
	else if(format == FMT_Failed)
	{
		$('#'+sid).html('<font color="red">'+text+'</font>');
	}
	else
	{
		$('#'+sid).text(text);
	}
}

/**
 * 随机字符
 */
function __rand_key()
{
	var salt = '0123456789qwertyuioplkjhgfdsazxcvbnm';
	var str = '';
	for(var i=0; i<6; i++)
	{
		str += salt.charAt(Math.ceil(Math.random()*100000000)%salt.length);
	}
	return str;
}