/** * @copyright (C)2011 Cenwor Inc. * @author Moyo <dev@uuland.org> * @package js * @name order.process.batch.js * @date 2011-08-17 10:59:54 */ $(document).ready(function(data){
     $('.service').bind('click', function(){
         if (!confirm('确认提交吗？'))
        {
            return;
        }
        doService(this);
     });
});

function doService(obj)
{
    var mark = $('#opmark').val();
    $('#service_result').text('正在提交，请稍候...');
    var lnk = $(obj).attr('href');
    var rurl = __batch_URI+'&action='+lnk+'&mark='+encodeURIComponent(mark)+$.rnd.stamp();
    $.get(rurl, function(data){
        var html = data == 'ok' ? '<a href="admin.php?mod=order&code=vlist">操作完成，点此返回订单列表</a>' : '<a href="javascript:;" onclick="window.location=window.location;">操作失败，点此刷新</a>';
        $('#service_result').html(html);
    });
}