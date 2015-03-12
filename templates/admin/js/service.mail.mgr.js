$(document).ready(function(){
	mail_cfg_area_reload(
		$('#mail_type_list').bind('change', function(){
			mail_cfg_area_reload($(this).val());
		}).val()
	);
});

function mail_cfg_area_reload(type)
{
	if (type != 'SMTP')
	{
		$('.for_SMTP').hide();
	}
	else
	{
		$('.for_SMTP').show();
	}
}
