/** * @copyright (C)2011 Cenwor Inc. * @author Moyo <dev@uuland.org> * @package js * @name ucard.master.js * @date 2011-12-07 13:42:08 */ $(document).ready(function(){
	$('.ucard').bind('mouseenter', ucardMouseOver);
	$('.ucard').bind('mouseleave', ucardMouseOut);
});

var $__ucard_delay_timer = null;
var $__ucard_delay_unload = null;
var $__ucard_delay_ajax = null;
var $__mouse_in_box = false;

function ucardMouseOver()
{
	$__mouse_in_box = true;
	var ele = this;
	if ($__ucard_delay_timer)
	{
		clearTimeout($__ucard_delay_timer);
	}
	$__ucard_delay_timer = setTimeout(function(){ucardBoxLoad(ele);}, 300);
}

function ucardMouseOut()
{
	$__mouse_in_box = false;
	var ele = this;
	if ($__ucard_delay_timer)
	{
		clearTimeout($__ucard_delay_timer);
	}
	if ($__ucard_delay_unload)
	{
		clearTimeout($__ucard_delay_unload);
	}
	$__ucard_delay_unload = setTimeout(function(){ucardBoxUnload(ele, false);}, 300);
}

function ucardBoxLoad(ele)
{
	var uid = $(ele).attr('uid');
	if (uid == null || uid == '' || isNaN(uid)) return;
	var box = ucardBoxCreate(ele, uid);
	if ($__ucard_delay_ajax)
	{
		clearTimeout($__ucard_delay_ajax);
	}
	$__ucard_delay_ajax = setTimeout(function(){ucardBoxAjax(uid, ele, box);}, 300);
}

function ucardBoxAjax(uid, ele, box)
{
	$.get('admin.php?mod=app&code=lpc&master=ucard&processor=ajax&uid='+uid.toString()+$.rnd.stamp(), function(data){
		var docWidth = $('body').width() - 30;
		box.html(data);
		var boxLeft = parseInt(box.css('left')) + box.width();
		if (boxLeft > docWidth)
		{
			var movLeft = parseInt(box.css('left')) - box.width();
			box.css('left', movLeft.toString()+'px');
		}
	});
}

function ucardBoxCreate(alink, uid)
{
	var allBox = $('.ucard_box').css('display', 'none');
	var __movTop = 0;
	var __movLeft = 0;
	var $_ucard_id = 'ucard_id_'+uid.toString();
	var ele = $('#'+$_ucard_id);
	if (ele.html() != null)
	{
		ele.html('<div class="ucard_box_loading"></div>');
	}
	else
	{
		$('body').append('<div class="ucard_box" id="'+$_ucard_id+'" style="display:none;"><div class="ucard_box_loading"></div></div>');
		ele = $('#'+$_ucard_id);
	}
	ele.css('top', $(alink).offset().top + $(alink).height() + __movTop);
	ele.css('left', $(alink).offset().left + $(alink).width() + __movLeft);
	ele.bind('mouseenter', function (){$__mouse_in_box = true;});
	ele.bind('mouseleave', function (){ucardBoxUnload(alink, true);});
	ele.fadeIn('fast');
	return ele;
}

function ucardBoxUnload(ele_A, fromBox)
{
	var uid = $(ele_A).attr('uid');
	var $_ucard_id = 'ucard_id_'+uid.toString();
	var ele = $('#'+$_ucard_id);
	if (!fromBox)
	{
		if ($__mouse_in_box) return;
	}
	if ($__ucard_delay_ajax)
	{
		clearTimeout($__ucard_delay_ajax);
	}
	if (ele.css('display') != 'none') ele.fadeOut('fast');
}