/** * @copyright (C)2011 Cenwor Inc. * @author Moyo <dev@uuland.org> * @package js * @name reports.mgr.js * @date 2013-07-12 18:10:30 */ function reports_view_user()
{
	artDialog.prompt('请输入需要查看的用户名', function(name) {
		$.notify.loading('正在查询中...');
		$.get('admin.php?mod=reports&code=queryuserid&username='+name, function(data){
			$.notify.loading();
			if (parseInt(data) > 0)
			{
				window.location = 'admin.php?mod=reports&code=view&service=user&hoster='+data;
			}
			else
			{
				$.notify.alert('没有找到叫 '+name+' 的用户！');
			}
		});
	});
}

function reports_load()
{
	if (report_source.newest)
	{
		reports_detect_channel();
	}
	else
	{
		reports_generating();
	}
}

var reports_channel = null;

function reports_detect_channel()
{
	if (reports_channel == null)
	{
		reports_get_chanels();
	}
	else
	{
		reports_load_data();
	}
}

function reports_get_chanels()
{
	$('#channel-area').html('正在加载频道...');
	$.getJSON('admin.php?mod=reports&code=channel&service='+report_source.service+'&hoster='+report_source.hoster, function(data){
		var html = '';
		var first_c = false;
		$.each(data, function(channel, name){
			first_c = first_c ? first_c : channel;
			html += '<a id="a-c-'+channel+'" class="a-cs"  href="#switch-channel" onclick="reports_switch_channel(\''+channel+'\');return false;">'+name+'</a>';
		});
		$('#channel-area').html(html);
		// load channel data
		reports_switch_channel(first_c);
	});
}

function reports_switch_channel(channel)
{
	// ui
	$('.a-cs').removeClass('selected');
	$('#a-c-'+channel).addClass('selected');
	// load
	reports_channel = channel;
	reports_load_data();
}

function reports_load_data()
{
	var date_begin = $('#datebegin').val();
	var date_finish = $('#datefinish').val();
	var channel = reports_channel;
	reports_load_from_date(date_begin, date_finish, channel);
}

function reports_load_from_date(bg, fn, ch)
{
	$('#charting-area').html('正在加载数据...');
	$.getJSON('admin.php?mod=reports&code=data&service='+report_source.service+'&hoster='+report_source.hoster+'&channel='+ch+'&begin='+bg+'&finish='+fn, function(data) {
		$('#datebegin').val(data.date_begin);
		$('#datefinish').val(data.date_finish);
		// charts display
		reports_do_charts(data);
	});
}

var reports_g_queue = {};

function reports_generating()
{
	$('#datepicker-area').hide();
	reports_g_status('正在准备生成报表数据，请稍候...');
	$.getJSON('admin.php?mod=reports&code=datelines&service='+report_source.service+'&hoster='+report_source.hoster, function(data) {
		reports_g_queue.ms = 1000;
		reports_g_queue.data = data;
		reports_g_queue.count = data.length;
		reports_g_queue.point = 0;
		reports_g_run();
	});
}

function reports_g_run()
{
	var c_dateline = reports_g_queue.data[reports_g_queue.point];
	if (c_dateline)
	{
		var per = Math.round((reports_g_queue.point / reports_g_queue.count) * 100);
		var spd = Math.round(1000 / reports_g_queue.ms);
		reports_g_status('总计有 '+reports_g_queue.count+' 个报表需要处理，当前已经处理了 '+reports_g_queue.point+' 个...进度 '+per+'%，速度 '+spd+' 个/秒');
		$.getJSON('admin.php?mod=reports&code=run&service='+report_source.service+'&hoster='+report_source.hoster+'&dateline='+c_dateline, function(data) {
			reports_g_queue.ms = data.ms;
			reports_g_queue.point ++;
			reports_g_run();
		});
	}
	else
	{
		reports_g_over();
	}
}

function reports_g_over()
{
	reports_g_status('报表创建完成，正在重新加载...');
	window.location = window.location;
}

function reports_g_status(msg)
{
	$('#updating-area').html(msg);
}

/**
 * 制作报表
 */
function reports_do_charts(data)
{
	// table
	var html = '';
	html += '<table id="chart-table-data" data-graph-container="#charting-area" data-graph-type="line"><caption>'+data.title+'</caption>';
	html += '<thead><tr><th>日期</th><th>'+data.channel_name+'</th></tr></thead>';
	html += '<tbody>';
	for (i in data.data)
	{
		var row = data.data[i];
		html += '<tr><td>'+row.date+'</td><td>'+row.data+'</td></tr>';
	}
	html += '</tbody>';
	html += '</table>';
	// write
	$('#table-area').html(html);
	// convert
	$('#chart-table-data').highchartTable();
}