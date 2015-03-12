<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name robot.logic.php
 * @date 2014-09-01 17:24:22
 */
 


include_once(LOGIC_PATH.'mysql.logic.php');
Class RobotLogic extends MysqlLogic{
	var $robotName="";
	var $agent="";
	var $regular="Bot|Spider|Twiceler|Crawl|ia_archiver|Slurp|ZyBorg|MSIECrawler|UdmSearch|IRLbo";
	var $tableName="";
	var $fieldList=array();
	function RobotLogic()
	{
		$this->setAgent(addslashes($_SERVER['HTTP_USER_AGENT']));
		$this->tableName=TABLE_PREFIX."system_robot";
		$this->MysqlLogic();
	}
	
	function setAgent($agent)
	{
		$this->agent=$agent;
	}
	
	function isRobot()
	{
		$version="(!:[\/\-]?\d+(\.\d+)+)?";
		$spiders="/[a-z\s!]*?[\w\-]*(?:{$this->regular})[a-z\-]*[a-z\s]*$version/i";
		if($this->agent=="") {
			return false;
		}
		preg_match($spiders,$this->agent,$match);
		if($match==false) {
			return false;
		}
		$this->robotName=trim($match[0]," \-");
		return $this->robotName;
	}
	
	function statistic()
	{
		if(empty($this->tableName))return false;
		$ip=$_SERVER['REMOTE_ADDR'];
		$timestamp=time();
		$sql="UPDATE ".$this->tableName."
			set
			`times`=`times`+1,
			`last_visit`='$timestamp'
			where `name`='$this->robotName'";
		$query=$this->DatabaseHandler->Query($sql,"SKIP_ERROR");
		$errno=$this->DatabaseHandler->getlastErrorNo();
		if($query==false && ($errno==ERROR_TABLE_NOT_EXIST || $errno==ERROR_UNKNOWN_COLUMN))
		{
			$this->createOrAlterTable($this->tableName,$this->getFieldList(),$sql);
		}
		$result= $this->DatabaseHandler->AffectedRows();
		if($result<1)
		{
			$sql="insert into $this->tableName(`name`,`times`,`first_visit`,`last_visit`,`agent`)
			values('{$this->robotName}','1','{$timestamp}','$timestamp','$this->agent')";
			$query = $this->DatabaseHandler->Query($sql);
			$result=(boolean)$this->DatabaseHandler->AffectedRows();
		}


				if($result)
		{
			$sql="
			UPDATE {$this->tableName}_ip
			set
				`times`=`times`+1,
				`last_visit`='$timestamp'
			where
				`ip`='$ip'
			";
			$query=$this->DatabaseHandler->Query($sql,"SKIP_ERROR");
			$errno=$this->DatabaseHandler->getlastErrorNo();
			if($query==false && ($errno==ERROR_TABLE_NOT_EXIST || $errno==ERROR_UNKNOWN_COLUMN))
			{
		     	$field_list=array(
			     	 "ip"=>"`ip` char(15) NOT NULL,PRIMARY KEY  (`ip`)",
			     	 "name"=>"`name` char(50) NOT NULL",
				     "times"=>"`times` int(10) unsigned NOT NULL default '0'",
		             "first_visit"=>"`first_visit` int(10) NOT NULL default '0'",
		             "last_visit"=>"`last_visit` int(10) NOT NULL default '0'"
	             );
				$this->createOrAlterTable($this->tableName."_ip",$field_list,$sql);
			}
			$result= $this->DatabaseHandler->AffectedRows();
			if($result<1)
			{
				$sql="insert into
				{$this->tableName}_ip(`ip`,`name`,`times`,`first_visit`,`last_visit`)
				values('$ip','{$this->robotName}','1','{$timestamp}','$timestamp')";
				$query = $this->DatabaseHandler->Query($sql);
				$result=(boolean)$this->DatabaseHandler->AffectedRows();
			}
		}
		return $result;
	}

	
	function getRobotName()
	{
		return $this->robotName;
	}
	function getFieldList()
	{
		$this->fieldList=array
		(
		     "name"=>"`name` char(50) NOT NULL,PRIMARY KEY  (`name`)",
		     "times"=>"`times` int(10) unsigned NOT NULL default '0'",
             "first_visit"=>"`first_visit` int(10) NOT NULL default '0'",
             "last_visit"=>"`last_visit` int(10) NOT NULL default '0'",
		     "agent"=>"`agent` char(255) NOT NULL",
			 "disallow"=>"`disallow` tinyint(1) NOT NULL default '0'",
		);
		return $this->fieldList;
	}
}
?>