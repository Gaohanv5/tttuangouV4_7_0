<?php

/**
 * 逻辑区：消息推送
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package logic
 * @name push.logic.php
 * @version 1.0
 */

class PushLogic
{
	
	public function add($type, $target, $data, $pr = 1)
	{
		$queue = array(
			'type' => $type,
			'target' => $target,
			'title' => $data['subject'],
			'content' => $data['content'],
			'update' => time(),
			'pr' => $pr,
			'guid' => $this->guid(),
			'workd' => 'idle'
		);
		dbc()->SetTable(table('push_queue'));
		return dbc()->Insert($queue);
	}
	
	public function addi($type, $target, $data)
	{
		$runCode = 'run_'.$type;
		return $this->$runCode($target, $data);
	}
	
	public function run($max, $class = null)
	{
		$lck = 'logic.push.running'.(is_null($class)?'.mix':'.'.$class);
		$MT = ini('service.push.mthread');
		if (!$MT && locked($lck))
		{
			return array('status' => 'error', 'msg' => 'locked');
		}
		$MT || locked($lck, true);
		ignore_user_abort(1);
		set_time_limit(0);
		$safe_time = 0;
		$max_time = ini_get('max_execution_time');
		if ($max_time > 0)
		{
			$safe_time = $max_time;
		}
		$time_start = time();
		$ix = 0;
		$rps = array();
		while ($ix < $max)
		{
			$qhandler = dbc(DBCMax)->select('push_queue');
						$class && $qhandler->where(array('type' => $class));
						$qhandler->where(array('worked' => 'idle', 'rund' => 'false'));
						$qhandler->order('pr.desc');
						$queue = $qhandler->limit(1)->done();
			if ($queue)
			{
												$guid = (string)microtime(true);
								$locked_request_time = microtime(true);
								$signal_busying = dbc(DBCMax)->update('push_queue')
												->where(array('id' => $queue['id'], 'worked' => 'idle'))
												->data(array('guid' => $guid, 'worked' => 'busying'))
												->done();
				if ($signal_busying)
				{
										$locked_finish_time = microtime(true);
					$locking_time = $locked_finish_time - $locked_request_time;
										if ($locking_time < 1)
					{
						if (is_numeric($signal_busying) && $signal_busying === 1)
						{
														$queuemsg = 'Q:'.$queue['id'].'+'.(string)round($locking_time, 5);
														$runCode = 'run_'.$queue['type'];
							$result = $this->$runCode($queue['target'], logic('push')->datapas($queue, 'run'), $queuemsg);
														$signal_completed = dbc(DBCMax)->update('push_queue')
															->where(array('id' => $queue['id'], 'worked' => 'busying'))
															->data(array('rund' => 'true', 'worked' => 'completed'))
															->done();
							if ($signal_completed)
							{
								if (dbc(DBCMax)->update('push_queue')->where(array('id' => $queue['id']))->data(array('result' => $result, 'update' => time()))->done())
								{
									$rps['Q:'.$queue['id']] = 'ok:result/'.$result;
								}
								else
								{
									$rps['Q:'.$queue['id']] = 'error:result/record';
								}
							}
							else
							{
								$rps['Q:'.$queue['id']] = 'error:queue/make-completed';
							}
														$ix ++;
														if ($safe_time > 0)
							{
								if (time() - $time_start > $safe_time)
								{
									$rps['Q:'.$queue['id']] = 'error:time/overdue';
									break;
								}
							}
						}
						else
						{
							$rps['Q:'.$queue['id']] = 'error:queue/busying-illegal';
						}
					}
					else
					{
						$rps['Q:'.$queue['id']] = 'error:queue/busying-slow';
					}
				}
				else
				{
					$rps['Q:'.$queue['id']] = 'error:queue/make-busying';
				}
			}
			else
			{
				break;
			}
		}
		$this->__clear();
		$MT || locked($lck, false);
		$rps && $this->run_log_error_scan($rps);
		return array('status' => 'ok', 'pool' => $rps);
	}
	
	private function run_log_error_scan($rps)
	{
		$ds = array();
		foreach ($rps as $qid => $log)
		{
			if (substr($log, 0, 5) == 'error')
			{
				$ds[] = $qid.' = '.$log;
			}
		}
		if ($ds)
		{
			zlog('error')->found('queue', implode('<br/>', $ds));
		}
	}
	
	public function log($type, $driver, $target, $data, $result, $queuemsg = '')
	{
		if (is_string($result) && substr($result, 0, 5) == '@exps')
		{
			$target = $result;
			$result = array(
				'message' => '群发已拆分',
				'logger' => 'true',
				'status' => 'system'
			);
		}
		
		$data = array(
			'type' => $type,
			'driver' => $driver,
			'target' => $target,
			'title' => $data['title'],
			'content' => $data['content'],
			'result' => $result['message'],
			'queuemsg' => $queuemsg,
			'update' => time()
		);
		$exts = array(
			'raw' => 'result_raw',
			'logger' => 'logger',
			'status' => 'status'
		);
		foreach ($exts as $k => $m)
		{
			if (isset($result[$k]))
			{
				$data[$m] = $result[$k];
			}
		}
		dbc()->SetTable(table('push_log'));
		return dbc()->Insert($data);
	}
	
	private function __clear()
	{
		$flagKey = 'push.logic.clear.flag';
		$timeInv = dfTimer('com.push.queue.clean');
		$flag = fcache($flagKey, $timeInv);
		if (!$flag)
		{
			$timeBefore = time() - $timeInv;
			dbc()->SetTable(table('push_queue'));
			dbc()->Delete('', 'rund="true" AND `update`<'.$timeBefore);
			fcache($flagKey, 'mark');
		}
	}
	
	private function run_mail($target, $data, $queuemsg = '')
	{
		$result = logic('service')->mail()->Send($target, $data['subject'], $data['content'], $queuemsg);
		if ($result == 1)
		{
			$result = __('邮件发送成功！');
		}
		return $result;
	}
	
	private function run_sms($target, $data, $queuemsg = '')
	{
		return logic('service')->sms()->Send($target, $data['content'], $queuemsg);
	}
	
	public function template()
	{
		return loadInstance('logic.push.template', 'PushLogic_Template');
	}
	
	public function ListQueue($rund = 'false', $type = null)
	{
		$sql_limit_type = '1';
		if ($type !== null)
		{
			$sql_limit_type = 'type="'.$type.'"';
		}
		$sql = 'SELECT * FROM '.table('push_queue').' WHERE '.$sql_limit_type.' AND rund="'.$rund.'" ORDER BY id DESC';
		$sql = page_moyo($sql);
		$r = dbc(DBCMax)->query($sql)->done();
		if (!$r) return array();
		foreach ($r as $i => $o)
		{
			$r[$i]['data'] = logic('push')->datapas($o, 'de');
		}
		return $r;
	}
	
	public function ListLog($type = null, $exsql = false)
	{
		$sql_limit_type = '1';
		if ($type !== null)
		{
			$sql_limit_type = 'type="'.$type.'"';
		}
		$sql = 'SELECT * FROM '.table('push_log').' WHERE '.$sql_limit_type.' AND '.($exsql ? $exsql : '1').' ORDER BY id DESC';
		$sql = page_moyo($sql);
		$r = dbc(DBCMax)->query($sql)->done();
		if ($r)
		{
			foreach ($r as $i => $o)
			{
				$r[$i]['data'] = logic('push')->datapas($o, 'de');
			}
		}
		return $r;
	}
	
	public function GetLog($id)
	{
		return dbc(DBCMax)->select('push_log')->where(array('id' => $id))->limit(1)->done();
	}
	
	public function query()
	{
		return loadInstance('logic.push.query', 'PushLogic_Query');
	}
	
	public function datapas($data, $dir = 'de')
	{
				if ($dir == 'de' || $dir == 'run')
		{
			if (isset($data['data']) && $data['data'])
			{
				$data = $data['data'];
				$array = unserialize(substr($data, 0, 2) == 'a:' ? str_replace("\r", "", $data) : base64_decode($data));
				if ($array && $dir != 'run')
				{
					foreach ($array as $key => $val)
					{
						$array[$key] = stripcslashes($val);
					}
				}
				return $array;
			}
			else
			{
				return array(
					'subject' => $data['title'],
					'content' => $data['content']
				);
			}
		}
	}
	
	private function guid()
	{
		$row = dbc(DBCMax)->query('select uuid() as guid')->limit(1)->done();
		if ($row && isset($row['guid']) && $row['guid'])
		{
			return $row['guid'];
		}
		else
		{
			return 'GID-'.md5((string)microtime(true));
		}
	}
}

/**
 * 扩充类：模板管理
 * @author Moyo <dev@uuland.org>
 */
class PushLogic_Template
{
	public function GetList($type = null)
	{
		$sql_limit_type = '1';
		if (!is_null($type))
		{
			$sql_limit_type = 'type="'.$type.'"';
		}
		$sql = 'SELECT * FROM '.table('push_template').' WHERE '.$sql_limit_type.' ORDER BY `update` DESC';
		$sql = page_moyo($sql);
		return dbc(DBCMax)->query($sql)->done();
	}
	public function GetOne($id)
	{
		$sql = 'SELECT * FROM '.table('push_template').' WHERE id='.$id;
		return dbc(DBCMax)->query($sql)->limit(1)->done();
	}
	
	public function Search($field, $value, $getOne = true)
	{
		$dbc = dbc(DBCMax)->select('push_template')->where(array($field=>$value));
		if ($getOne)
		{
			$dbc->limit(1);
		}
		return $dbc->done();
	}
	public function Update($id, $data)
	{
		dbc()->SetTable(table('push_template'));
		$data['update'] = time();
		if ($id == 0)
		{
			$result = dbc()->Insert($data);
		}
		else
		{
			$result = dbc()->Update($data, 'id='.$id);
		}
		return $result;
	}
	public function Del($id)
	{
		dbc()->SetTable(table('push_template'));
		dbc()->Delete('', 'id='.$id);
	}
}


/**
 * 扩充类：数据查询
 * @author Moyo <dev@uuland.org>
 */
class PushLogic_Query
{
	private $queryTable = 'queue';
	public function from($table)
	{
		$this->queryTable = 'push_'.$table;
		return $this;
	}
	public function where($where, $limit = 1)
	{
		return dbc(DBCMax)->select($this->queryTable)->where($where)->limit($limit)->done();
	}
	public function delete($where)
	{
		return dbc(DBCMax)->delete($this->queryTable)->where($where)->done();
	}
}

?>