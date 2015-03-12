<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name mail_send.task.php
 * @date 2014-09-01 17:24:22
 */
 



if (class_exists('TaskCore')==false) {
	include_once(TASK_PATH.'task_core.task.php');
}
class TaskItem extends TaskCore
{
	function TaskItem()
	{
		$this->TaskCore();
	}

	function run()
	{
				require("./setting/product.php");
		$set = $config['product'];
				$num = (int)$set['default_mail_maxonce'];
		$sql='select * from '.TABLE_PREFIX.'tttuangou_cron order by addtime ASC Limit 0,'.$num;
		$query = $this->DatabaseHandler->Query($sql);
		$mail=$query->GetAll();
		if(empty($mail))return false;
		foreach($mail as $i => $value)
		{
			sendmail($value['username'], $value['address'], $value['title'], $value['content'], $set);
						$sql='delete from '.TABLE_PREFIX.'tttuangou_cron where id = '.$value['id'];
			$this->DatabaseHandler->Query($sql);
		}
				$this->log("成功发送 {$num} 封邮件！");
		return $num;
	}

}
?>