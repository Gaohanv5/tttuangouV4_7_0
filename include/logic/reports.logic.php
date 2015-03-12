<?php

/**
 * 逻辑区：报表管理
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package logic
 * @name reports.logic.php
 * @version 1.0
 */

class ReportsManageLogic
{
	
	public function get_channels($service)
	{
		list($service, $hoster) = $this->get_service_hoster($service);
				$where = array('service' => $service);
		is_null($hoster) || $where['hoster'] = $hoster;
		$result = dbc(DBCMax)->select('reports')->in('distinct channel')->where($where)->done();
		if ($result)
		{
			$channels = array();
			foreach ($result as $i => $channel)
			{
				$channels[$channel['channel']] = $this->api($service)->get_channel_name($channel['channel']);
			}
			return $channels;
		}
		else
		{
			return array();
		}
	}
	
	public function get_data($service, $channel, $date_begin = null, $date_finish = null)
	{
		list($service, $hoster) = $this->get_service_hoster($service);
		$where = array('service' => $service);
		if ($hoster)
		{
			$where['hoster'] = $hoster;
		}
		$where['channel'] = $channel;
		$where_date = 'dateline >= '.$date_begin.' and dateline <= '.$date_finish;
		$reports = dbc(DBCMax)->select('reports')->in('*, sum(data) as DTS')->where($where)->where($where_date)->group('dateline')->order('dateline asc')->done();
		$table = array();
		$table['title'] = date('Y-m-d', $date_begin).'到'.date('Y-m-d', $date_finish).'，【'.$this->api($service)->get_hoster_name($hoster).'】的数据报表';
		$table['service'] = $service;
		$table['channel'] = $channel;
		$table['channel_name'] = $this->api($service)->get_channel_name($channel);
		$table['hoster'] = $hoster;
		$table['date_begin'] = date('Y-m-d', $date_begin);
		$table['date_finish'] = date('Y-m-d', $date_finish);
		$table['data'] = array();
		foreach ($reports as $report)
		{
			$table['data'][] = array(
				'dateline' => $report['dateline'],
				'date' => date('Y-m-d', $report['dateline']),
				'data' => $report['DTS']
			);
		}
		return $table;
	}
	
	public function datelines($service)
	{
		list($service, $hoster) = $this->get_service_hoster($service);
		return $this->api($service)->get_datelines($hoster);
	}
	
	public function build_datelines($first_timeline)
	{
				$dl_current = $this->format_dateline('c');
				$dl_first = $this->format_dateline($first_timeline);
				$datelines_count = ($dl_current - $dl_first) / 86400;
		$datelines = array();
		for ($i = 0; $i <= $datelines_count; $i ++)
		{
			$datelines[] = $dl_first + 86400 * $i;
		}
		return $datelines;
	}
	
	public function is_newest($service)
	{
		$reports_last_dateline = $this->get_last_dateline($service);
		$reports_curr_dateline = $this->format_dateline('c');
		return $reports_last_dateline == $reports_curr_dateline;
	}
	
	public function datelines_left($service)
	{
		$lines_all = $this->datelines($service);
		$line_last = $this->get_last_dateline($service);
		$lines_left = array();
		foreach ($lines_all as $line)
		{
			$line > $line_last && $lines_left[] = $line;
		}
		return $lines_left;
	}
	
	public function generate($service, $dateline = null, $force = false)
	{
		if (is_null($dateline))
		{
			$datelines = $this->datelines($service);
		}
		else
		{
			$datelines[] = $dateline;
		}
		$reports_last_dateline = $force ? 0 : $this->get_last_dateline($service);
		list($service, $hoster) = $this->get_service_hoster($service);
		foreach ($datelines as $dateline)
		{
						if ($dateline > $reports_last_dateline)
			{
				$this->api($service)->generate($dateline, $hoster);
			}
		}
	}
	
	public function build_reports($service, $channels, $dateline, $hoster, $hlinks, $fixer = false)
	{
		if ($service && $channels && $hoster && $hlinks)
		{
			foreach ($channels as $channel => $filter)
			{
				foreach ($hlinks as $hlink)
				{
					$result = dbc(DBCMax)
					->select($filter['table'])
					->in($filter['in'])
					->where($filter['where'])
					->where('`'.$filter['dateliner'].'` >= '.$dateline.' and `'.$filter['dateliner'].'` < '.($dateline + 86400))
					->where(array($hoster => $hlink))
					->limit(1)
					->done();

					if ($result)
					{
						$data = $result['RDATA'];
						$fixer && call_user_func_array($fixer, array($dateline, $hlink, &$data));
						logic('reports')->update($service, $channel, $hlink, $data, $dateline);
					}
				}
			}
		}
	}
	
	public function format_dateline($time)
	{
		if (is_string($time) && $time == 'c')
		{
						$time = time() - 86400;
		}
		return $time - ($time % 86400) - (3600 * 8);
	}
	
	public function update($service, $channel, $hoster, $data, $dateline)
	{
		$idx = array('service' => $service, 'channel' => $channel, 'hoster' => $hoster, 'dateline' => $dateline);
		$dat = array('data' => is_null($data) ? 0 : $data);
		$last = dbc(DBCMax)->select('reports')->where($idx)->limit(1)->done();
		if ($last)
		{
			dbc(DBCMax)->update('reports')->data($dat)->where(array('id' => $last['id']))->done();
		}
		else
		{
			dbc(DBCMax)->insert('reports')->data(array_merge($idx, $dat))->done();
		}
	}
	
	private function get_service_hoster($service_mix)
	{
		if (is_array($service_mix))
		{
			return $service_mix;
		}
		else
		{
			return array($service_mix, null);
		}
	}
	
	private function get_last_dateline($service_mix)
	{
		list($service, $hoster) = $this->get_service_hoster($service_mix);
		$where = array('service' => $service);
		is_null($hoster) || $where['hoster'] = $hoster;
		$report = dbc(DBCMax)->select('reports')->where($where)->order('dateline.desc')->limit(1)->done();
		if ($report)
		{
			return $report['dateline'];
		}
		else
		{
			return 0;
		}
	}
	
	private function api($service)
	{
		engine_class_file_load(INCLUDE_PATH.'reports/'.$service);
		return loadInstance('logic.reports.'.$service, 'reports_unit_'.$service);
	}
}

?>