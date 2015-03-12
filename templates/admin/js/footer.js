/** * @copyright (C)2011 Cenwor Inc. * @author Moyo <dev@uuland.org> * @package js * @name footer.js * @date 2011-11-15 13:48:33 */ var $__SHOW_REQUEST_LOADING = true;
$(document).ready(function(){
	$.notify.loading();
	$(window).data('events')['beforeunload'] || $(window).bind('beforeunload',  wf_beforeunload);
});
function wf_page_loading(ctrl2Switch)
{
	$__SHOW_REQUEST_LOADING = ctrl2Switch;
}
function wf_beforeunload()
{
	if (!$__SHOW_REQUEST_LOADING) return;
	$.notify.loading('正在请求中...', true);
	$.browser.msie && setTimeout(function(){$.notify.loading()}, 300);
}