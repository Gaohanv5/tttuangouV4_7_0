var __all_step_count = 0;
var __all_steps = {};
function cc2install(button)
{
	$(button).attr('disabled', 'disabled');
	$.each($('.wait'), function(i){
		__all_steps[i] = this;
		__all_step_count ++;
	});
	install_step(0);
}

function install_step(n)
{
	if (n >= __all_step_count)
	{
		install_finish();
		return;
	}
	var obj = __all_steps[n];
	var action = $(obj).attr('title');
	// start
	$(obj).text('正在处理中');
	if (action == 'ends')
	{
		install_lives();
	}
	$.get('?mod=install&code=process&op='+action+$.rnd.stamp(), function(data){
		
		$(obj).text('处理完成').removeClass('wait').addClass('success');
		install_step(n+1);
	});
}

function install_lives()
{
	$.get('?mod=install&code=process&op=lives'+$.rnd.stamp(), function(data){});
}

function install_finish()
{
	$('#final_result').html('<p>安装完成！</p><br/><a href="index.php">访问首页</a> - <a href="admin.php">登录后台</a>');
}
