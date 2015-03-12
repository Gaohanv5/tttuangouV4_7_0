/** * @copyright (C)2014 Cenwor Inc. * @author Cenwor <www.cenwor.com> * @package js * @name product.mgr.autoSave.js * @date 2014-05-15 17:39:21 */ var $__draft_accessed = false;
var $__autoSaveObj = null;
var $__master_submitted = false;
var $__this_draft_id = false;

$(document).ready(function(){
	// bind click
	$('#draftButton').bind('click', function(){
		productSubmitCheck(true) && proIfo_autoSave();
	});
	// bind hook
	$.hook.add('productIfoSubmit', function(){
		// check if master allowed submit
		if (!productSubmitCheck(false)) return;
		// normal saveHandelr
		proIfo_field_set('saveHandler', 'normal');
		// save ifo by draft-ID
		proIfo_field_set('productID', __draft_ID);
		if (__Global_PID != '')
		{
			// real Save to edit-Product
			proIfo_field_set('productID', __Global_PID);
		}
		if ($__this_draft_id)
		{
			proIfo_field_set('draft-pro-id', $__this_draft_id);
		}
		$__master_submitted = true;
	});
	// event @ editor.Close
	$.hook.add('productEditorExit', function(){
		__editor_allow_close = true;
		if (!$__draft_accessed)
		{
			window.location = '?mod=product&code=vlist';
			return;
		}
		$.notify.loading('正在清理草稿数据...');
		proIfoClearDraft(function(data){window.location = '?mod=product&code=vlist';});
	});
	// check if has draft already
	if (__exists_Draft_ID != '' && parseInt(__draft_ID) == 0)
	{
		art.dialog.confirm('发现本产品存在草稿数据，是否恢复？', function(){
			proIfoRestore(__exists_Draft_ID);
		}, function(){
			proIfoClearDraft(function(data){
				draftSaveResult('已经清理草稿数据！', 2000);
			});
		});
	}
	// clear productID and then can apply for New draft area
	__Global_PID != '' && proIfo_field_set('productID', '');
	// if from draft then save to draft
	if (parseInt(__draft_ID) > 0)
	{
		proIfo_field_set('productID', __draft_ID);
		// from draft
		$__draft_accessed = true;
	}
	__draft_ID = __Global_PID != '' ? __Global_PID : 0;
	// auto save in 60s
	$__autoSaveObj = setInterval(proIfo_autoSave, 1000*60);
});

function proIfo_autoSave()
{
	return;// check
	if (!productSubmitCheck(false)) return;
	if (isMasterSubmitted()) return;
	// make flag
	$__draft_accessed = true;
	// pre parse
	proIfo_field_set('saveHandler', 'draft');
	proIfo_field_set('draftID', __draft_ID);
	draftSaveResult('正在保存草稿...');
	// submit
	$('#productIfoForm').ajaxSubmit({
		success: function(data)
		{
			if (isMasterSubmitted()) return;
			eval('var rps='+data);
			if (rps.status == 'ok')
			{
				// set target pid
				proIfo_field_set('productID', rps.pid);
				// set draft-ID
				__draft_ID = __Global_PID != '' ? __Global_PID : rps.pid;
				// set draft-product-ID
				if (!$__this_draft_id) $__this_draft_id = rps.pid;
				// get time
				var timeString = '';
				var cDate = new Date();
				timeString = cDate.getHours()+':'+cDate.getMinutes()+':'+cDate.getSeconds();
				// save ok
				draftSaveResult('草稿保存于 '+timeString+' - <a href="#proIfoRestore" onclick="proIfoRestore('+rps.pid+');return false;">[ 恢复 ]</a> &nbsp;&nbsp;&nbsp;&nbsp;');
			}
			else
			{
				// fails
				draftSaveResult('草稿保存失败！', 2000);
				if (rps.msg)
				{
					$.notify.alert(rps.msg);
				}
			}
		}
	});
}

function isMasterSubmitted()
{
	if ($__master_submitted)
	{
		draftSaveResult('正在提交中，草稿暂停保存！');
		return true;
	}
	else
	{
		return false;
	}
}

function proIfo_field_set(kid, val)
{
	if($('#'+kid).length == 0)
	{
		$('#productID').after('<input type="hidden" id="'+kid+'" name="'+kid+'" value="'+val+'" />');
	}
	else
	{
		$('#'+kid).val(val);
	}
}

function draftSaveResult(text, autoHide)
{
	$('#autoSaveStatus').html(text);
	if (autoHide)
	{
		setTimeout(function(){
			$('#autoSaveStatus').fadeOut();
		}, autoHide);
	}
}

function proIfoRestore(draftID)
{
	// close Timer
	clearInterval($__autoSaveObj);
	$.notify.loading('正在恢复中...');
	$.get('admin.php?mod=product&code=draft&op=restore&pid='+__Global_PID+'&did='+draftID+$.rnd.stamp(), function(data){
		setTimeout(function(){
			__editor_allow_close = true;
			window.location = data;
		}, 500);
	});
}

function proIfoClearDraft(callback)
{
	$.get('admin.php?mod=product&code=draft&op=clear&pid='+__Global_PID+'&did='+__draft_ID+$.rnd.stamp(), function(data){
		callback(data);
	});
}