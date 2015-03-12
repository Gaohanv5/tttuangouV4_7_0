/** * @copyright (C)2011 Cenwor Inc. * @author Moyo <dev@uuland.org> * @package js * @name express.cdp.sync.js * @date 2011-11-15 13:48:33 */ $(document).ready(function(){
	$.get('?mod=express&code=cdp&op=sync'+$.rnd.stamp(), function(data){
		if(data != 'cached')
		{
			cdpSyncProcess('正在同步打印模板...');
			$.get('?mod=express&code=cdp&op=sync_download'+$.rnd.stamp(), function(data){
				cdpSyncProcess('同步完成！', true);
				try {eval('var rps='+data)} catch(e) {var rps={'status':'failed'}}
				if (rps.status == 'ok')
				{
					$.each(rps.list, function(i, one){
						var obj = $('#cdp_import_of_'+i.toString());
						if (obj.attr('href'))
						{
							obj.show();
						}
					});
				}
				else if (rps.status == 'denied')
				{
					var options = {
							lock: true,
							fixed: true,
							icon: 'warning',
							content: '打印模板同步功能仅对商业授权用户开放！<a href="http://www.tttuangou.net/price.html" target="_blank">详情</a><br/><br/>（如果您是商业授权用户，请在控制面板首页更新授权）',
							yesText: '知道了',
							yesFn: function(){},
							noText: '7天内不再尝试同步',
							noFn: function(){cdpSyncNoAlert();}
						};
					art.dialog(options);
				}
			});
		}
	});
});

function cdpSyncNoAlert()
{
	$.get('?mod=express&code=cdp&op=sync_noAlert', function(data){});
}

function cdpSyncProcess(string, autoHide)
{
	$('#cdpSyncStatus').html(string).fadeIn();
	if (autoHide)
	{
		setTimeout(function(){$('#cdpSyncStatus').fadeOut()}, 3000);
	}
}