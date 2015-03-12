/** * @copyright (C)2014 Cenwor Inc. * @author Cenwor <www.cenwor.com> * @package js * @name push.template.mgr.js * @date 2014-09-01 17:24:23 */ $(document).ready(function() {
	var push_mgr_editor;
	$('#contentType').bind('change', function() {
		if ($(this).val() == 'html')
		{
			push_mgr_editor = KindEditor.create('#content');
			push_mgr_editor.focus();
		}
		else
		{
			KindEditor.remove('#content');
		}
	});
	$('#content').css('width', '500px').css('height', '120px');
});
