/** * @copyright (C)2011 Cenwor Inc. * @author Moyo <dev@uuland.org> * @package js * @name account.active.js * @date 2011-08-31 14:10:12 */ $(document).ready(function(){
    var dData2Check = new Array('username', 'email', 'phone');
    $.each(dData2Check, function(i, id){
        $('#'+id).trigger('blur');
    });
});
function accconfig()
{
	$('#accintro').fadeOut();
	$('#accdetail').slideDown();
}
