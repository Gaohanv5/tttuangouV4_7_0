<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name task_core.task.php
 * @date 2014-09-01 17:24:22
 */
 



class TaskCore
{
	var $DatabaseHandler=null;

	var $log = array(
		'message' =>  __('已成功执行'),
		'error' => 0
	);

	function TaskCore()
	{
		$this->DatabaseHandler=&Obj::registry("DatabaseHandler");
	}

	function SqlError($sql,$file='',$line='')
	{
		$this->log['message'] = __("<b>SQL查询语句错误</b>").
				"\r\n<br><br>错误语句:<br>[{$line}]{$file}<code>$sql</code>".
				"\r\n<br><br>错误编号:".$this->DatabaseHandler->GetLastErrorNo().
				"\r\n<br><br>错误信息:<br>".$this->DatabaseHandler->GetLastErrorString()."<br>";

		$this->log['error']=E_USER_ERROR;
	}

	function log($message,$error=0)
	{
		$this->log['message']=$message;
		$this->log['error']=$error;
	}

	function request($url)
	{
		$config=&Obj::registry('config');
		if(strpos($url,':/'.'/')===false) {
			$url=$config['site_url'].'/'.$url;
		}

		if ((defined('ROBOT_NAME') && false!==ROBOT_NAME) || 			('remote_script' == $_REQUEST['request_from']) || 			(!$_SERVER['HTTP_USER_AGENT']) || 			(!$_COOKIE)) {
			@dfopen($url,-1,$post,$cookie,true,3);
			@usleep(rand(10000,100000)); 		} else {
			$GLOBALS['iframe'] .="<iframe src='{$url}' border=0 width=0 height=0></iframe>";
		}
	}
}
?>