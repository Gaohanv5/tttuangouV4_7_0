/*
 * SDB 服务端数据前端化处理器
 */

var IMG_ENABLE = 'templates/admin/images/btn_enable.png';
var IMG_DISABLE = 'templates/admin/images/btn_disable.gif';
var IMG_LOADING = 'templates/admin/images/btn_loading.gif';

var __SDATA = {};
var listSDBDriver = new Array('ini', 'dbf', 'lgc');

$(document).ready(function(){
	// 图片预加载
	var listPreLoads = new Array(IMG_ENABLE, IMG_DISABLE, IMG_LOADING);
	$.each(listPreLoads, function(i, path){
		var plImg = new Image();
			plImg.src = path;
	});
	$.each(listSDBDriver, function(i, name){
		$.each($('.'+name), function(){
			HookSDBParser(name, this);
		});
	});
	$.each($('._flag_img'), function(){
		var src = $(this).attr('key');
		switch (src)
		{
			case 'enable':
				$(this).attr('src', IMG_ENABLE);
			break;
			case 'disable':
				$(this).attr('src', IMG_DISABLE);
			break;
			case 'loading':
				$(this).attr('src', IMG_LOADING);
			break;
		}
	});
});

function HookSDBParser(src, obj)
{
	if ($(obj).html() != '')
	{
		ExistSDBParser(src, obj);
		return;
	}
	var iid = 'sdb_'+__rand_key();
	$(obj).attr('id', iid);
	$('#'+iid).html('<img src="'+IMG_LOADING+'" />');
	var path = $(obj).attr('src');
	var rel = $(obj).attr('rel');
	if (rel == undefined)
	{
		rel = '';
	}
	$.get('?mod='+src+'&code=get&path='+path+$.rnd.stamp(), function(data){
		// storage data
		__SDATA[iid] = {};
		__SDATA[iid].rel = rel;
		__SDATA[iid].src = src;
		__SDATA[iid].path = path;
		__SDATA[iid].data = eval(data);
		SDBParser(iid);
	});
}

function ExistSDBParser(src, obj)
{
	var iid = 'sdb_'+__rand_key();
	$(obj).attr('id', iid);
	var rel = $(obj).attr('rel');
	if (rel == undefined)
	{
		rel = '';
	}
	// storage data
	__SDATA[iid] = {};
	__SDATA[iid].rel = rel;
	__SDATA[iid].src = src;
	__SDATA[iid].path = $(obj).attr('src');
	var text = $(obj).html();
	var data;
	if (text == 'true' || text == 'false')
	{
		// boolean
		data = (text == 'true') ? true : false;
	}
	else
	{
		// super string ^ ^
		data = text;
	}
	__SDATA[iid].data = data;
	SDBParser(iid);
}

function SDBParser(iid)
{
	var subParser = '';
	var rel = __SDATA[iid].rel;
	if (rel == '')
	{
		subParser = 'Parser_'+typeof(__SDATA[iid].data);
	}
	else
	{
		subParser = 'Parser_'+rel;
	}
	eval(subParser+'(iid)');
}

// Parser.boolean
function Parser_boolean(iid)
{
	var area = $('#'+iid);
	var staPRE = '当前状态为：';
	var chsPRE = '，点此快速 ';
	if (__SDATA[iid].data)
	{
		__SDATA[iid].data = false;
		var tips = staPRE+'开启'+chsPRE+'关闭';
		area.html('<a href="#void" onclick="javascript:Set_boolean(\''+iid+'\');return false;" title="'+tips+'"><img src="'+IMG_ENABLE+'" /></a>');
	}
	else
	{
		__SDATA[iid].data = true;
		var tips = staPRE+'关闭'+chsPRE+'开启';
		area.html('<a href="#void" onclick="javascript:Set_boolean(\''+iid+'\');return false;" title="'+tips+'"><img src="'+IMG_DISABLE+'" /></a>');
	}
}
function Set_boolean(iid, set)
{
	$('#'+iid).html('<img src="'+IMG_LOADING+'" />');
	$.get('?mod='+__SDATA[iid].src+'&code=set&path='+__SDATA[iid].path+'&data='+encodeURIComponent(__SDATA[iid].data)+$.rnd.stamp(), function(data){
		Parser_boolean(iid);
	});
}

// Parser.string
function Parser_string(iid)
{
	var area = $('#'+iid);
	var data = __SDATA[iid].data;
	var dataLen = data.toString().length;
	var tips = '编辑完成后，系统会自动保存';
	if (dataLen < 18)
	{
		area.html('<input type="text" value="'+data+'" onblur="javascript:Set_string(\''+iid+'\',this);" title="'+tips+'" />');
	}
	else
	{
		area.html('<textarea onblur="javascript:Set_string(\''+iid+'\',this);" title="'+tips+'">'+data+'</textarea>');
	}
}
function Set_string(iid, area)
{
	var text = $(area).val();
	if (text == __SDATA[iid].data)
	{
		return;
	}
	// pre set
	__SDATA[iid].data = text;
	var TMP_id = 'img_loading_'+__rand_key();
	$('#'+iid).after('<img id="'+TMP_id+'" style="margin-left:10px;" src="'+IMG_LOADING+'" />');
	$.get('?mod='+__SDATA[iid].src+'&code=set&path='+__SDATA[iid].path+'&data='+encodeURIComponent(__SDATA[iid].data)+$.rnd.stamp(), function(data){
		$('#'+TMP_id).remove();
	});
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
