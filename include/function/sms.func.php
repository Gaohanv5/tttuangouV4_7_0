<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name sms.func.php
 * @date 2014-09-01 17:24:22
 */
 


function sms_send($phone, $content, $retry=true)
{
	$content = iconv('UTF-8', 'GB2312/'.'/IGNORE', $content);
	$sms = ConfigHandler::get('sms');
	$sms['server'] = sms_server_init();
	$data = 'name='.$sms['account'].'&password='.md5($sms['password']).'&troughid=&priorityid=&timing=&mobile='.$phone.'&content='.rawurlencode($content).'&splitsuffix=0';
	$result = dfopen($sms['server'].'?method=sendsms&'.$data, 10485760, '', '', true);
	if ($retry && '' == $result)
	{
				for ($i=0;$i<3;$i++)
		{
			$result = dfopen($sms['server'].'?method=sendsms&'.$data, 10485760, '', '', true);
			if ($result != '') break;
		}
	}
	$result = iconv('GB2312', 'UTF-8/'.'/IGNORE', $result);
	preg_match('/<code>(.*?)<\/code>/', $result, $match);
	$state = $match[1];
	$msgid = 'SMS'.time();
	preg_match('/<message>(.*?)<\/message>/', $result, $match);
	$msgstate = $match[1];
	if ('' == $msgstate)
	{
		$msgstate = __('服务器不稳定，发送失败！');
	}
	return array('state'=>$state, 'msgid'=>$msgid, 'msgstate'=>$msgstate);
}

function sms_remain()
{
	$sms = ConfigHandler::get('sms');
	$sms['server'] = sms_server_init();
	$data = 'name='.$sms['account'].'&password='.md5($sms['password']);
	$result = dfopen($sms['server'].'?method=remaincount&'.$data, 10485760, '', '', true);
	$result = iconv('GB2312', 'UTF-8/'.'/IGNORE', $result);
	preg_match('/<describe>(.*?)<\/describe>/', $result, $match);
	$status = $match[1];
	preg_match('/<count>(.*?)<\/count>/', $result, $match);
	$remain = (int)$match[1]/10;
	if ($match[0] == '')
	{
		preg_match('/<message>(.*?)<\/message>/', $result, $match);
		$remain = $match[1];
	}
	return array('status'=>$status, 'remain'=>$remain);
}

?>