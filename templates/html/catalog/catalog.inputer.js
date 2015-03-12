function catalog_master_change()
{
    var topid = $('#__catalog_topclass').val();
    catalog_subclass_loading();
    $.get('admin.php?mod=catalog&code=subclass&op=list_ajax&parent='+topid.toString()+'&category='+catalog_product_category+$.rnd.stamp(), function(data){
        catalog_subclass_fill(data);
    });
}
function catalog_subclass_add()
{
    var topid = $('#__catalog_topclass').val();
    __catalog_add(topid);
}
function catalog_subclass_loading()
{
    $('#__catalog_subclass').html('<option value="-1">正在加载</option>');
}
function catalog_subclass_fill(html)
{
    $('#__catalog_subclass').html(html);
}