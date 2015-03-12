$(document).ready(function(){
	if (templateID == '')
	{
		template_generate();
	}
	else
	{
		template_preview(parseInt(templateID));
	}
});

function pushdPreview(obj)
{
	var target = $('#target').val();
	if (target == '')
	{
		$.notify.alert('请输入发送目标号码！');
		return;
	}
	$(obj).val('正在发送');
	var tplid = templateID;
	$.get('?mod=subscribe&code=push&op=preview&class='+__flag+'&tid='+tplid+'&target='+target+$.rnd.stamp(), function(data){
		if (data == 'ok')
		{
			$(obj).val('发送成功');
		}
		else
		{
			$(obj).val('发送失败');
		}
	});
}

function pushdRequest(flag, obj)
{
	var tplid = templateID;
	var cityid = $('#citySel_of_'+flag).val();
	$(obj).val('正在推送');
	$.get('?mod=subscribe&code=push&class='+flag+'&tid='+tplid+'&city='+cityid+$.rnd.stamp(), function(data){
		if (data == 'ok')
		{
			$(obj).val('推送成功');
			$(obj).attr('disabled', 'disabled');
		}
		else
		{
			$(obj).val('推送失败');
		}
	});
}

function template_edit()
{
	parent.window.location = 'admin.php?mod=subscribe&code=broadcast&op=edit&id='+templateID;
}

function template_generate()
{
	var obj = $('#templateButton');
	$(obj).val('正在加载');
	$.get('?mod=subscribe&code=generate&op=template&flag='+__flag+'&from='+__from+'&idx='+__idx+$.rnd.stamp(), function(data){
		template_preview(parseInt(data));
		$(obj).val('编辑模板');
	});
}

function template_preview(tplid)
{
	$('#piframe').attr('src', '?mod=subscribe&code=template&op=preview&id='+tplid);
	templateID = tplid;
}
