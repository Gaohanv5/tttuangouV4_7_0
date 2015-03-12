/** * @copyright (C)2011 Cenwor Inc. * @author Moyo <dev@uuland.org> * @package js * @name widget.area.blocks.sort.js * @date 2011-08-31 14:10:12 */ $(document).ready(function(){
    $('#wablist').dragsort({dragSelector: 'div', placeHolderTemplate: '<li class="dragHolder"></li>'});
});

function area_block_sort_save($areaName)
{
    $.notify.loading('正在保存中...');
    var wabString = '';
    $('#wablist li').each(function(i, li){
        wabString += $(li).attr('id')+',';
    });
    wabString = wabString.substr(0, wabString.length-1);
    $.get('admin.php?mod=widget&code=config&op=sort_save&flag='+$areaName+'&list='+encodeURIComponent(wabString)+$.rnd.stamp(), function(data){
        if (data == 'ok')
        {
            $.notify.show('保存成功！');
        }
        else
        {
            $.notify.failed(data);
        }
        $.notify.loading();
    });
}