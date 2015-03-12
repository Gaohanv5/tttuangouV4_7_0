/** * @copyright (C)2011 Cenwor Inc. * @author Moyo <dev@uuland.org> * @package js * @name recharge.card.ops.js * @date 2013-07-31 16:18:09 */ function rechargeCardDelete(id)
{
    if (!confirm('确认删除吗？')) return;
    recardOping(id);
    $.get('?mod=recharge&code=card&op=delete&id='+id+$.rnd.stamp(), function(data){
        if (data == 'ok')
        {
            recardOping(id, 'close');
        }
        else
        {
            $.notify.failed('删除失败！');
            recardOping(id, 'end');
        }
    });
}

function recardOping(id, op)
{
    if (op == undefined)
    {
        $('#rc_on_'+id).removeClass().addClass('tips');
        return;
    }
    if (op == 'end')
    {
        $('#rc_on_'+id).removeClass();
        return;
    }
    if (op == 'close')
    {
        $('#rc_on_'+id).fadeOut();
        return;
    }
}