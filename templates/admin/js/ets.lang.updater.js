$(document).ready(function(){
	$.get('?mod=notify&code=ets&op=check'+$.rnd.stamp(), function(data){
		if (data != 'noups')
		{
			$('#etsChecker').text('检测到更新，正在自动同步...');
			$.get('?mod=notify&code=ets&op=update'+$.rnd.stamp(), function(data){
				if (data != 'ok')
				{
					$('#etsChecker').text('标签语言包同步失败，请刷新重试！');
				}
				else
				{
					$('#etsChecker').text('标签语言包同步完成！');
				}
			});
		}
		else
		{
			$('#etsChecker').text('当前标签语言包已经是最新状态！');
		}
	});
});