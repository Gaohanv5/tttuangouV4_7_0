$(document).ready(function(){
	$.each($('.status'), function(){
		var id = $(this).attr('title');
		$(this).attr('id', 'tmp_status_'+id);
		$(this).html('<br/>loading...');
		$.get('?mod=service&code=sms&op=status&id='+id+$.rnd.stamp(), function(data){
			$('#tmp_status_'+id).html('<br/>'+data);
		});
	});
});