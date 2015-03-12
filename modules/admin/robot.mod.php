<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name robot.mod.php
 * @date 2014-11-04 13:51:55
 */
 




class ModuleObject extends MasterObject
{
	
	var $ad=false;
	
	function ModuleObject($config)
	{
		$this->MasterObject($config);
		$this->ID = $this->Get['id']?(int)$this->Get['id']:(int)$this->Post['id'];
		$this->configPath=CONFIG_PATH;
		Load::moduleCode($this);$this->Execute();
	}

	
	function Execute()
	{
		switch($this->Code)
		{
			case 'domodify':
				$this->DoModify();
				break;
			case 'view':
				$this->view();
				break;
			case 'viewip':
				$this->viewIP();
				break;
			case 'deleteip':
				$this->deleteIP();
				break;
			case 'disallow0':
			case 'disallow1':
				$this->Disallow();
				break;
			default:
				$this->Main();
				break;
		}
	}

	
	function Main()
	{
		$this->CheckAdminPrivs('robot');
		$config=ConfigHandler::get('robot');

				$order_by=$this->Get['order_by']?$this->Get['order_by']:"today_times";
		$order_type=$this->Get['order_type']?$this->Get['order_type']:"desc";
		$toggle_order_type=$order_type=="desc"?"asc":"desc";
		$$order_by="order_".$order_type;

		include_once(LOGIC_PATH.'robot.logic.php');
		$RobotRogic=new RobotLogic();
		$turnon_radio=FormHandler::YesNoRadio('config[turnon]',(int)$config['turnon'],'','class="radio"');

		if ($config['turnon'])
		{
			$sql="SELECT * FROM ".$RobotRogic->tableName;
			$query=$this->DatabaseHandler->Query($sql,"SKIP_ERROR");
			if($query==false && $this->DatabaseHandler->getlastErrorNo()==ERROR_TABLE_NOT_EXIST)
			{
				$query=$RobotRogic->createTable($RobotRogic->tableName,$RobotRogic->getFieldList(),$sql);
			}
			$robot_list=array();
			$name_list=array();
			while ($row=$query->GetRow())
			{
				$row['link']=preg_replace("/.*?(((((https?|ftp|gopher|news|telnet|rtsp|mms|callto|bctp|ed2k):\/\/)|(www\.))([^\[\"'\s\)\(\;]+))|([a-z0-9\_\-.]+@[a-z0-9]+\.[a-z0-9\.]{2,}))*/i","\\1",$row['agent']);
				if(strpos($row['link'],'@')!==false)$row['link']="mailto:".$row['link'];
				if($row['link'] && strpos($row['link'],":")===false)$row['link']="http:/"."/".$row['link'];
				$row['first_visit']=my_date_format($row['first_visit']);
				$row['last_visit']=my_date_format($row['last_visit']);
				if($this->ad)
				{
					$show_ad=isset($config['list'][$row['name']]['show_ad'])
							?(int)$config['list'][$row['name']]['show_ad']:
							1;
					$row['show_ad_radio']=FormHandler::YesNoRadio("config[list][{$row['name']}][show_ad]",$show_ad,'',"class='radio'");
				}
				$row['today_times']=0;
				$name_list[]=$row['name'];
				$row['name']=trim($row['name']);
				$robot_list[$row['name']]=$row;
			}

						if(sizeof($name_list)>=0)
			{
				$names=$this->DatabaseHandler->BuildIn($name_list,"");
				include_once LOGIC_PATH.'robot_log.logic.php';
				$RobotLogLogic=new RobotLogLogic("");
				$sql="SELECT * FROM {$RobotLogLogic->tableName}
				where
					`name` in($names)
					and `date`='{$RobotLogLogic->date}'
				order by times desc";
				$query=$this->DatabaseHandler->Query($sql,"SKIP_ERROR");
				if($query==false && $this->DatabaseHandler->getlastErrorNo()==ERROR_TABLE_NOT_EXIST)
				{
					$query=$RobotRogic->createTable($RobotLogLogic->tableName,$RobotLogLogic->getFieldList(),$sql);
				}
				$today_robot_list=array();
				while ($row=$query->GetRow())
				{
					if(isset($robot_list[$row['name']]))
					$robot_list[$row['name']]['today_times']=$row['times'];
				}
			}

						if(is_array($robot_list) && sizeof($robot_list)>0)
			{
				foreach ($robot_list as $key=>$value)
				{
					$order_by_list[$key]=$value[$order_by];
				}
				array_multisort($order_by_list,constant(strtoupper("sort_".$order_type)),$robot_list);
			}
						if(sizeof($robot_list)>0)
			{
				$robot_ip_list=array();
				$sql="SELECT ip,name from {$RobotRogic->tableName}_ip order by `ip`";
				$query=$this->DatabaseHandler->Query($sql,"SKIP_ERROR");
				if($query!=false)
				{
					while ($row=$query->GetRow())
					{
						$robot_ip_list[$row['name']][]=$row['ip'];
					}
					if(!empty($robot_ip_list))
					{
						foreach ($robot_ip_list as $_robot=>$_ip_list)
						{
							if(sizeof($_ip_list)>5)
							{
								$ip_list=array();
								$ip_list_count=0;
								foreach ($_ip_list as $_ip)
								{
									$ip=substr($_ip,0,strrpos($_ip,".")).".*";
									$ip_list[$ip]=$ip;
									$ip_list_count++;
									if($ip_list_count>10)break;
								}
								$robot_ip_list[$_robot]=$ip_list;
							}
						}
					}
				}
			}
		}
		include handler('template')->file("@admin/robot");
	}
	function doModify()
	{
		$this->CheckAdminPrivs('robot');
		$delete_list=(array)$this->Post['delete'];

		@$robot_config=ConfigHandler::get('robot');
		$robot_config['turnon'] = (boolean) $this->Post['config']['turnon'];

		if(sizeof($delete_list))
		{
			include_once(LOGIC_PATH.'robot.logic.php');
			$RobotRogic=new RobotLogic();

			include_once LOGIC_PATH.'robot_log.logic.php';
			$RobotLogLogic=new RobotLogLogic("");

			foreach ($delete_list as $name)
			{
				unset($robot_config['list'][$name]);
				$sql="DELETE from ".$RobotRogic->tableName." where name='".$name."'";
				$query = $this->DatabaseHandler->Query($sql);
				$sql="DELETE from ".$RobotLogLogic->tableName." where name='".$name."'";
				$query=$this->DatabaseHandler->Query($sql,"SKIP_ERROR");
			}
		}
		$configHandler = new ConfigHandler();
		$configHandler->set('robot',$robot_config);
		$this->Messager("修改成功");
	}
	function view()
	{
		$this->CheckAdminPrivs('robot');
		$name=trim($this->Get['name']);
		$day=(int)($this->Get['day']);
		if($name=="")$this->Messager("名称不能为空",null);
		if($day<1)$this->Messager("时间周期不能小于1",null);
		$date_from=date("Ymd",time()-($day*86400));
		$date_to=date("Ymd");
		include_once LOGIC_PATH.'robot_log.logic.php';
		$RobotLogLogic=new RobotLogLogic("");
		$sql="SELECT *
		FROM {$RobotLogLogic->tableName}
		where
			`name` ='$name'
			and (`date`>{$date_from} and `date`<={$date_to})";
		$query = $this->DatabaseHandler->Query($sql);
		$times=0;
		while ($row=$query->GetRow())
		{
			$row['first_visit']=my_date_format($row['first_visit'],"H:i:s");
			$row['last_visit']=my_date_format($row['last_visit'],"H:i:s");
			$times+=$row['times'];
			$log_list[]=$row;
		}
		$count=sizeof($log_list);
		include handler('template')->file('@admin/robot_view');
	}

	function viewIP()
	{
		$this->CheckAdminPrivs('robot');
		$robot=trim($this->Get['robot']);
		$sql="select * from ".TABLE_PREFIX."system_robot_ip where `name`='$robot' order by `ip`";
		$query = $this->DatabaseHandler->Query($sql);
		$ip_list=array();
		$count=0;
		while ($row=$query->GetRow())
		{
			$count++;
			$row['first_visit']=my_date_format($row['first_visit']);
			$row['last_visit']=my_date_format($row['last_visit']);
			$times+=$row['times'];
			$ip_list[]=$row;
		}
		if(empty($ip_list))$this->Messager("无IP记录");
		include handler('template')->file('@admin/robot_view_ip');
	}
	function deleteIP()
	{
		$this->CheckAdminPrivs('robot');
		$ip=trim($this->Get['ip']);
		if(empty($ip))
		{
			$this->Messager("请指定IP");
		}
		$sql="delete from ".TABLE_PREFIX."system_robot_ip where ip='$ip'";
		$this->DatabaseHandler->Query($sql);
		$this->Messager("删除成功");
	}

	function Disallow()
	{
		$this->CheckAdminPrivs('robot');
		$name = trim($this->Get['name']);
		$disallow = 'disallow1' == $this->Code ? 1 : 0;

		$sql = "update `".TABLE_PREFIX."system_robot` set `disallow`='{$disallow}' where `name`='{$name}'";
		$this->DatabaseHandler->Query($sql);

		$sql = "select `name`,`disallow` from `".TABLE_PREFIX."system_robot` where `disallow`=1";
		$query = $this->DatabaseHandler->Query($sql);
		$robot_config = ConfigHandler::get('robot');
		$robot_config['list'] = array();
		while ($row = $query->GetRow())
		{
			$robot_config['list'][$row['name']]['disallow'] = $row['disallow'];
		}
		$configHandler = new ConfigHandler();
		$configHandler->set('robot',$robot_config);


		$disallow_string = "User-agent: {$name}
Disallow: /

";

		$load = new Load();
		$load->lib('io');
		$IoHandler = new IoHandler();
		$robots_path = ROOT_PATH . 'robots.txt';

		$robots_string_new = $robots_string = $IoHandler->ReadFile($robots_path);
		$disallow_string_strpos = strpos($robots_string,$disallow_string);
		if ($disallow && false===$disallow_string_strpos) {
			$robots_string_new = $disallow_string . $robots_string_new;
		} elseif (!$disallow && false!==$disallow_string_strpos) {
			$robots_string_new = str_replace($disallow_string,"",$robots_string_new);
		}

		if ($robots_string_new!=$robots_string) {
			$return = $IoHandler->WriteFile($robots_path,$robots_string_new);

			if (!$return) {
				$this->Messager("写入 <b>{$robots_path}</b> 文件失败，请检查是否有可读写的权限",null);
			}
		}

		$this->Messager("修改成功");
	}

}
?>
