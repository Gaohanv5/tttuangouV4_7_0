/** * @copyright (C)2011 Cenwor Inc. * @author Moyo <dev@uuland.org> * @package js * @name cplace.inputer.js * @date 2013-07-12 18:10:30 */ $(document).ready(function(){
	$.hook.add('pro.city.sel.change', function(){
        cplace_regions_reload();
    });
});

function cplace_regions_reload()
{
	var city_id = $('#allCityList').val();
	$('#__cplace_region').html('<option>正在加载</option>');
	$('#__cplace_street').html('<option value="0">全部</option>');
	$.get('admin.php?mod=city&code=place&op=ajaxlist&type=city&id='+city_id.toString(), function(html){
		$('#__cplace_region').html(html);
	});
}

function cplace_region_change()
{
	var region_id = $('#__cplace_region').val();
	$('#__cplace_street').html('<option>正在加载</option>');
	$.get('admin.php?mod=city&code=place&op=ajaxlist&type=region&id='+region_id.toString(), function(html){
		$('#__cplace_street').html(html);
	});
}