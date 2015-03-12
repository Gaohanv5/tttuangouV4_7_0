<?php

/**
 * 应用：短信剩余量预警
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package app
 * @name smsw.load.php
 * @version 1.0
 */

class SMSSWarningAPP
{
	
	public function test_send()
	{
		return $this->warning('xx', true);
	}
	
	public function config($data = null)
	{
		$pox = 'service.__app__.smsw';
		if (is_null($data))
		{
			return ini($pox);
		}
		if ((int)$data['interval'] <= 0) $data['interval'] = 12;
		if ((int)$data['surplus'] <= 0) $data['surplus'] = 100;
		if ((float)$data['phone'] <= 0) $data['serviceID'] = 0;
		return ini($pox, $data);
	}
	
	public function detecting()
	{
		$cfg = $this->config();
		if ((int)$cfg['serviceID'] == 0) return 'disabled';
		$ckey = 'service.app.smsw.check';
		$lastCheck = $this->fcache($ckey, $cfg['interval']*3600);
		if ($lastCheck) return 'checked';
				$status = logic('service')->sms()->Status($cfg['serviceID']);
		preg_match('/短信剩余：(\d+) 条/', $status, $mch);
		if (is_numeric($mch[1]))
		{
			$surplus = (int)$mch[1];
			if ($surplus < $cfg['surplus'])
			{
				$this->warning($surplus);
			}
			$this->fcache($ckey, 'TIME:DNA:'.(string)time());
		}
		return 'ok';
	}
	
	private function warning($surplus, $ignoreCache = false)
	{
		$cfg = $this->config();
		$ckey = 'service.app.smsw.warning';
		if ($ignoreCache)
		{
			if ((int)$cfg['serviceID'] == 0) return 'disabled';
			$lastSend = false;
		}
		else
		{
			$lastSend = $this->fcache($ckey, 3600);
		}
		if ($lastSend) return 'checked';
		$smsC = '尊敬的管理员您好，系统检测到当前短信剩余量仅为：'.$surplus.' 条，为了保障站点的正常运营，请及时进行短信充值！【短信预警系统】';
		logic('push')->addi('sms', $cfg['phone'], array('content' => $smsC));
		$this->fcache($ckey, 'TIME:DNA:'.(string)time());
		return 'ok';
	}
	
	private function fcache($key, $mixed)
	{
		return fcache($key, $mixed, DATA_PATH.'fcache/');
	}
}

?>