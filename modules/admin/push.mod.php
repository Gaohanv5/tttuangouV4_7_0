<?php

/**
 * 模块：推送信息管理
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package module
 * @name push.mod.php
 * @version 1.0
 */

class ModuleObject extends MasterObject
{
    public function ModuleObject( $config )
    {
        $this->MasterObject($config);
        $runCode = Load::moduleCode($this);
        $this->$runCode();
    }
    public function Main()
    {
        $this->CheckAdminPrivs('push');
		$this->Queue();
    }
    public function Queue()
    {
        $this->CheckAdminPrivs('push');
		$rund = get('rund', 'txt');
        if (!$rund) $rund = 'false';
        $list = logic('push')->ListQueue($rund);
        include handler('template')->file('@admin/push_queue');
    }
    public function Queue_clear()
    {
        $this->CheckAdminPrivs('push');
		$rund = get('rund', 'txt');
        $sql_limit_time = '`update` '.$this->__sql_clear_time();
        $sql = 'DELETE FROM '.table('push_queue').' WHERE '.$sql_limit_time.' AND rund="'.$rund.'"';
        dbc()->Query($sql);
        $this->Messager('操作完成！', '?mod=push&code=queue&rund='.$rund);
    }
    public function Log()
    {
        $this->CheckAdminPrivs('push');
		$type = get('type');
		$status = get('status');
		if ($status && $status != 'failed')
		{
			$status = 'failed';
		}
        $qType = $type ? $type : null;
		$exsql = $status ? 'status="'.$status.'"' : false;
        $list = logic('push')->ListLog($qType, $exsql);
        include handler('template')->file('@admin/push_log');
	}
    public function Log_clear()
    {
        $this->CheckAdminPrivs('push');
		        $_POST['clear_time'] = 7;
        $_POST['clear_unit'] = 'd';
        $_POST['clear_type'] = 'out';
        $sql_limit_time = 'type="mail" AND `update` '.$this->__sql_clear_time();
        $sql = 'DELETE FROM '.table('push_log').' WHERE '.$sql_limit_time;
        dbc()->Query($sql);
        $this->Messager('操作完成！', '?mod=push&code=log');
    }
	public function Log_reverse()
	{
		$this->CheckAdminPrivs('push');
		$id = get('id', 'int');
		$log = logic('push')->GetLog($id);
		if (substr($log['target'], 0, 1) == '@')
		{
			$path = '未知状态，请返回';
			$flag = substr($log['target'], 1, 4);
			if ($flag == 'exps')
			{
				$path = '群发拆包详情';
			}
			if ($flag == 'logs')
			{
				$path = '群发状态详情';
			}
			preg_match('/^@'.$flag.'\[(.*?)\]$/i', $log['target'], $mchs);
			$ids = $mchs[1];
			$ids || $ids = '-1';
			$list = logic('push')->ListLog(null, 'id IN('.$ids.')');
			include handler('template')->file('@admin/push_log');
		}
		else
		{
			$this->Messager('数据不存在，无法查看群发明细！', '?mod=push&code=log&type=sms');
		}
	}
    public function Manage_preview()
    {
        $this->CheckAdminPrivs('push','ajax');
		$table = get('table', 'text');
        $id = get('id', 'int');
        $push = logic('push')->query()->from($table)->where('id='.$id);
        $data = logic('push')->datapas($push, 'de');
        exit($data['content']);
    }
    public function Manage_delete()
    {
        $this->CheckAdminPrivs('push','ajax');
		$table = get('table', 'text');
        $id = get('id', 'int');
        logic('push')->query()->from($table)->delete('id='.$id);
        exit('ok');
    }
    public function Manage_resend()
    {
        $this->CheckAdminPrivs('push');
		$table = get('table', 'text');
        $id = get('id', 'int');
        $push = logic('push')->query()->from($table)->where('id='.$id);
        if ($push['target'] == 'Broadcast')
        {
            exit('对不起，此条内容为群发模式，不可以进行重发！');
        }
        $data = logic('push')->datapas($push, 'run');
        include handler('template')->file('@admin/push_resend');
    }
    public function Manage_resend_done()
    {
        $this->CheckAdminPrivs('push','ajax');
		$table = get('table', 'text');
        $id = get('id', 'int');
        $push_old = logic('push')->query()->from($table)->where('id='.$id);
        $data_old = logic('push')->datapas($push_old, 'de');
        $data = array('content'=>post('content')?post('content', 'text'):addslashes($data_old['content']));
        if ($push_old['type'] == 'mail')
        {
            $data['subject'] = addslashes($data_old['subject']);
        }
        $type = $push_old['type'];
		$target = post('target', 'text');
		if ($target)
		{
			if (strstr($target, "\n"))
			{
				$target = str_replace(array("\r", "\n"), array('', ';'), $target);
			}
		}
		else
		{
			$target = $push_old['target'];
		}
        logic('push')->add($type, $target, $data, 7);
        exit('重发请求已经写入队列，您现在可以关闭此窗口了！');
    }
    private function __sql_clear_time()
    {
        $time = post('clear_time', 'int');
        $unit = post('clear_unit', 'txt');
        $type = post('clear_type', 'txt');
        $time_unit = array(
            's' => 1,
            'm' => 60,
            'h' => 3600,
            'd' => 86400
        );
        $now = time();
        $pox = $now - $time * $time_unit[$unit];
        if ($type == 'in')
        {
            return '>= '.$pox;
        }
        else
        {
            return '<= '.$pox;
        }
    }
	
    private function __broadcast_count($id)
	{
				$html  = '';
		$html .= '拆包：xx个';
		$html .= '成功：xx个';
		$html .= '失败：xx个';
		$html .= '';
	}
}

?>