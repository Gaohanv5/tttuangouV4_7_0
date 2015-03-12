/** * @copyright (C)2011 Cenwor Inc. * @author Moyo <dev@uuland.org> * @package js * @name subscribe.broadcast.js * @date 2013-07-12 18:10:30 */ var __g_class = '';
var __g_tid = '';
var __g_dialog = null;

function pushd(clsname, tid)
{
	__g_class = clsname;
	__g_tid = tid;
	__g_dialog = $.dialog({title:'城市群发',content:$('#pushdSpace').html()});
}

function pushd_direct(clsname, tid)
{
	__g_class = clsname;
	__g_tid = tid;
	__g_dialog = $.dialog({title:'定向投递',content:$('#pushdSpace-Direct').html()});
}

function pushdRequest()
{
	var cityID = $('#citySel').val();
	var url = '?mod=subscribe&code=push&class='+__g_class+'&tid='+__g_tid+'&city='+cityID+$.rnd.stamp();
	$.notify.loading('正在提交...');
	$.get(url, function(data){
		$.notify.loading();
		if (data == 'ok')
		{
			$.notify.success('提交成功！');
			__g_dialog.close();
		}
		else
		{
			$.notify.failed(data);
		}
	});
}

function pushdRequest_direct()
{
	var targets = $('#pushd_targets').val();
	if (targets.length < 11)
	{
		$.notify.alert('请输入正确的推送目标！');
		return;
	}
	var FORMHASH = $('#pushd_form input[name=FORMHASH]').val();
	var url = '?mod=subscribe&code=push&op=direct&class='+__g_class+'&tid='+__g_tid+$.rnd.stamp();
	$.notify.loading('正在提交...');
	$.post(url, {FORMHASH:FORMHASH,targets:targets}, function(data){
		$.notify.loading();
		if (data == 'ok')
		{
			$.notify.success('提交成功！');
			__g_dialog.close();
		}
		else
		{
			$.notify.failed(data);
		}
	});
}