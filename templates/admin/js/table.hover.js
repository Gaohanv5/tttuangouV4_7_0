/** * @copyright (C)2011 Cenwor Inc. * @author Moyo <dev@uuland.org> * @package js * @name table.hover.js * @date 2012-02-01 14:57:55 */ $(document).ready(function(){
	makeTableTR2Hover();
});

function makeTableTR2Hover()
{
	$.each($('tr'), function(){
		if (!$(this).attr('class'))
		{
			$(this)
			.unbind()
			.bind('mouseover', function(){
				$(this).addClass('tr_hover');
			})
			.bind('mouseout', function(){
				$(this).removeClass();
			});
		}
	});
}