/** * @copyright (C)2011 Cenwor Inc. * @author Moyo <dev@uuland.org> * @package js * @name prize.mgr.js * @date 2011-09-28 17:19:19 */ $(document).ready(function(){
    $('#button_query_user').bind('click', query_win_user);
});

function query_win_user()
{
    var $prize_win = $('#prize_ticket_win').val().toString();
    if ($prize_win=='' || isNaN($prize_win))
    {
        $.notify.alert('请输入一个正确的抽奖号码！（需要纯数字）');
        return;
    }
    query_user_ifo_show('查询中...');
    $.get('?mod=prize&code=ajax&op=query&pid='+__Global_ProductID +'&ticket='+$prize_win+$.rnd.stamp(), function(data){
        query_user_ifo_show(data);
    });
}

function query_user_ifo_show(html)
{
    $('#tr_query_user').show();
    $('#td_query_user').html(html);
}

function public_prize_win()
{
    var $prize_win = $('#prize_ticket_win').val().toString();
    art.dialog.confirm('准备提交的中奖号码：<b>'+$prize_win+'</b><br/><br/>请注意：<br/>1，中奖号码公开后将无法再修改；<br/>2，中奖号码公开后前台会显示中奖号码；<br/><br/>您确认要公开吗？', function(){
         $.notify.loading('正在提交中奖号码...');
         $.get('?mod=prize&code=ajax&op=publish&pid='+__Global_ProductID +'&ticket='+$prize_win+$.rnd.stamp(), function(data){
             $.notify.loading(false);
             if (data == 'ok')
             {
                 $.notify.success('提交成功！');
                 setTimeout(function(){window.location = window.location}, 1000);
             }
             else
             {
                 $.notify.failed(data);
             }
         });
    });
}

function send_sms_notify(phone)
{
    $.notify.loading('正在发送...');
    var verifyHash = document.getElementById('sms_notify_form').FORMHASH.value;
    $.post('?mod=prize&code=ajax&op=notify&phone='+phone+$.rnd.stamp(),
        {'FORMHASH':verifyHash,'content':$('#sms_notify_content').val()},
        function(data)
        {
            $.notify.loading(false);
            if (data == 'ok')
            {
                $.notify.success('发送成功！');
            }
            else
            {
                $.notify.failed(data);
            }
        }
    );
}

function send_sms_broadcast(excUID)
{
    $.notify.loading('正在发送...');
    var verifyHash = document.getElementById('sms_broadcast_form').FORMHASH.value;
    $.post('?mod=prize&code=ajax&op=broadcast&pid='+__Global_ProductID+'&excuid='+excUID+$.rnd.stamp(),
        {'FORMHASH':verifyHash,'content':$('#sms_broadcast_content').val()},
        function(data)
        {
            $.notify.loading(false);
            if (data == 'ok')
            {
                $.notify.success('发送成功！');
            }
            else
            {
                $.notify.failed(data);
            }
        }
    );
}
