/** * @copyright (C)2011 Cenwor Inc. * @author Moyo <dev@uuland.org> * @package js * @name flash.ops.js * @date 2011-11-15 13:48:33 */ 
var process_request = "正在处理您的请求...";
var todolist_caption = "记事本";
var todolist_autosave = "自动保存";
var todolist_save = "保存";
var todolist_clear = "清除";
var todolist_confirm_save = "是否将更改保存到记事本？";
var todolist_confirm_clear = "是否清空内容？";
var lang_removeconfirm = "您确定要卸载该配送方式吗？";
var shipping_area = "设置区域";
var upload_falid = "错误：文件类型不正确。请上传“%s”类型的文件！";
var upload_del_falid = "错误：删除失败！";
var upload_del_confirm = "提示：您确认删除打印单图片吗？";
var no_select_upload = "错误：您还没有选择打印单图片。请使用“浏览...”按钮选择！";
var no_select_lable = "操作终止！您未选择任何标签。";
var no_add_repeat_lable = "操作失败！不允许添加重复标签。";
var no_select_lable_del = "删除失败！您没有选中任何标签。";
var recovery_default_suer = "您确认恢复默认吗？恢复默认后将显示安装时的内容。";


var display_yes = 'inline';

/**
 * 保存
 */
function save()
{
  //获取表单对象
  var the_form = this_obj("theForm");
  if (typeof(the_form) == "undefined")
  {
	return false; //程序错误
  }

  the_form.config_lable.value = call_flash('lable_Location_info', '');
  the_form.target = '';
  the_form.act.value = 'config_save';
  the_form.submit();
  return true;
}

/**
 * 打印单背景图片删除
 */

function bg_del()
{
  //获取表单对象
  var the_form = this_obj("theForm");
  if (typeof(the_form) == "undefined")
  {
	return false; //程序错误
  }
  if (!confirm(upload_del_confirm))
  {
	return false; //中止执行
  }
  the_form.target = 'bg_upload_hidden';
  the_form.act.value = 'background_delete';
  the_form.submit();
}

/**
 * 打印单背景图片上传
 */
function bg_upload()
{
  //获取表单对象
  var the_form = this_obj("theForm");
  if (typeof(the_form) == "undefined")
  {
	return false; //程序错误
  }
  //判断是否选取了上传文件
  if (the_form.bg.value == '')
  {
	alert(no_select_upload);
	return false;
  }
  the_form.target = 'bg_upload_hidden';
  the_form.act.value = 'print_upload';
  the_form.submit();
  return true;
}

/**
 * 与模板Flash编辑器通信
 */
function call_flash(type, currt_obj)
{
  //获取flash对象
  var obj = this_obj("swfPrint");
  //执行操作
  switch (type)
  {
	case 'bg_delete': //删除打印单背景图片
	  var result_del = obj.bg_delete();
	  //执行成功 修改页面上传窗口为显示 生效
	  if (result_del)
	  {
		document.getElementById('pic_control_upload').style.display = display_yes;
		document.getElementById('pic_control_del').style.display = 'none';
		var the_form = this_obj("theForm");
		the_form.bg.disabled = "";
		the_form.bg.value = "";
		the_form.upload.disabled = "";
		the_form.upload_del.disabled = "disabled";
	  }
	break;
	case 'bg_add': //添加打印单背景图片
	  var result_add = obj.bg_add(currt_obj);
	  //执行成功 修改页面上传窗口为隐藏 失效
	  if (result_add)
	  {
		document.getElementById('pic_control_upload').style.display = 'none';
		document.getElementById('pic_control_del').style.display = display_yes;
		var the_form = this_obj("theForm");
		the_form.bg.disabled = "disabled";
		the_form.upload.disabled = "disabled";
		the_form.upload_del.disabled = "";
	  }
	break;
	case 'lable_add': //插入标签
	  if (typeof(currt_obj) != 'object')
	  {
		return false;
	  }
	  if (currt_obj.value == '')
	  {
		alert(no_select_lable);
		return false;
	  }
	  var result = obj.lable_add('t_' + currt_obj.value, currt_obj.options[currt_obj.selectedIndex].text, 150, 50, 20, 100, 'b_' + currt_obj.value);
	  if (!result)
	  {
		alert(no_add_repeat_lable);
		return false;
	  }
	break;
	case 'lable_del': //删除标签
	  var result_del = obj.lable_del();
	  if (result_del)
	  {
		//alert("删除成功！");
	  }
	  else
	  {
		alert(no_select_lable_del);
	  }
	break;
	case 'lable_Location_info': //获取标签位置信息
	  var result_info = obj.lable_Location_info();
	  return result_info;
	break;
  }
  return true;
}

/**
 * 获取页面Flash编辑器对象
 */
function this_obj(flash_name)
{
  var _obj;
  if ($.browser.msie)
  {
	  _obj = window[flash_name];
  }
  else
  {
	  _obj = document[flash_name];
  }
  if (typeof(_obj) == "undefined")
  {
	_obj = document[flash_name];

  }
  return _obj;
}
