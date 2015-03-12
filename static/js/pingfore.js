$(document).ready(function(){
	pingfore_request_server('sms');
});

function pingfore_request_server(pclass)
{
	$.get('index.php?mod=pingfore&class='+pclass+$.rnd.stamp(), function (data){
		eval('var pdata='+data);
		var next = isNaN(pdata.interval) ? 10 : pdata.interval;
		setTimeout(function(){
			pingfore_request_server(pdata.extend);
		}, next * 1000);
	});
}