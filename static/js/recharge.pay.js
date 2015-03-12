$('#recharge_card').bind('blur', function(){
    var no = $(this).val().toString();
    if (no.length != 12)
    {
        result('请输入正确的充值卡号！', 'red');
        return;
    }
    result('正在查询中...');
    $.get('?mod=misc&code=recharge&op=cardifo&no='+no+$.rnd.stamp(), function(data){
        result(data, 'blue');
    });
});

function result(msg, color)
{
    if (!color) color = '';
    $('#query_result').html(msg).css('color', color);
}