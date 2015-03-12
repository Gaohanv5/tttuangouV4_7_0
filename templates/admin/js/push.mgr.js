/** * @copyright (C)2011 Cenwor Inc. * @author Moyo <dev@uuland.org> * @package js * @name push.mgr.js * @date 2013-07-12 18:10:30 */ function push_data_delete(dsp, table, id)
{
    if (!confirm('您确认要删除吗？')) return false;
    $(dsp).html('正在删除...');
    $.get('admin.php?mod=push&code=manage&op=delete&table='+table+'&id='+id+$.rnd.stamp(), function(data){
        if (data == 'ok')
        {
            $(dsp).html('已经删除！');
            setTimeout(function(){$('#tr_of_'+id).fadeOut()}, 300);
        }
        else
        {
            $(dsp).html('删除失败！');
        }
    });
}
function push_resend(id)
{
	artDialog.open('admin.php?mod=push&code=manage&op=resend&table=log&id='+id+'&~iiframe=yes');
}