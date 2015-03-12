<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name sessions.mod.php
 * @date 2014-09-01 17:24:23
 */
 


class ModuleObject extends MasterObject
{

	
	var $Title = 0;


	
	var $ID = 0;


	
	function ModuleObject($config)
	{
		$this->MasterObject($config);

		$this->ID = (int)$this->Post['id']?(int)$this->Post['id']:(int)$this->Get['id'];
		Load::moduleCode($this);$this->Execute();

	}

	
	function Execute()
	{
		switch($this->Code)
		{
			case 'delete':
				$this->Delete();
				break;
			default:
				$this->Main();
				break;
		}

	}


	function Main()
	{
		$this->CheckAdminPrivs('sessions');
		$where_list=array();
		$where="";
		$query_link="admin.php?mod=sessions";

		
				$p=max((int)$this->Get['page'],1);
		$query_link.="&page=".$p;
		$pn=(int)$this->Get['pn']?(int)$this->Get['pn']:20;
		if($pn<1)$pn=20;
		$query_link.='&pn='.$pn;


				$username=trim($this->Get['username']);
		if ($username!="")
		{
			$where_list[]="username like '%$username%'";
			$query_link.="&username=".urlencode($username);
		}

				$ip=trim($this->Get['ip']);
		if (!empty($ip))
		{
			$ip_l=explode('.',$ip);
			$ips='';
			$i=0;
			foreach ($ip_l as $_ip)
			{
				$i++;
				if($_ip=="*" || empty($_ip))continue;
				$ips.=$and."ip{$i}='$_ip'";
				$and=" and ";
			}
			$where_list[]=$ips;
		}

		if($this->Get['order_by'])$query_link.="&order_by=".$this->Get['order_by'];
		if($this->Get['order_type'])$query_link.="&order_type=".$this->Get['order_type'];

		if($where_list)$where=" where ".implode(" AND ",$where_list);

				$order_by_list = array
		(
			'order_by_default' => 'lastactivity',

			'lastactivity' => array
			(
				'name' => '最后访问时间',
				'order_by' => 'lastactivity',
			),
			'ip' => array
			(
				'name' => 'IP地址',
				'order_by' =>"concat_ws('.',ip1,ip2,ip3,ip4)",
			),
			'uid' => array
			(
				'name' => '用户名',
				'order_by' => 'uid',
			),
		);
		$order_array = order($order_by_list,$query_link,array('display_un_href'=>true));
		$order = $order_array['order'];
		$order_html = $order_array['html'];

				$limit="";
		$offset=($p-1)*$pn;

				$sql="SELECT count(1) total from ".TABLE_PREFIX."system_sessions".$where;
		$query = $this->DatabaseHandler->Query($sql);
		$row=$query->GetRow();
		$total=$row['total'];


				$sql="SELECT * from ".TABLE_PREFIX."system_sessions".$where." ".$order." limit $offset,$pn";
		$query = $this->DatabaseHandler->Query($sql);
		$session_list=array();
		while ($row=$query->GetRow())
		{
			$row['dateline']=my_date_format($row['lastactivity']);
			$row['ip']=sprintf("%s.%s.%s.%s",$row['ip1'],$row['ip2'],$row['ip3'],$row['ip4']);
			$session_list[]=$row;
		}

		$robot=array();
		$robot_record=(int)ConfigHandler::get('robot','turnon');
		if($robot_record==1)
		{
						$sql="SELECT * from ".TABLE_PREFIX."system_robot_ip";
			$query=$this->DatabaseHandler->Query($sql,"SKIP_ERROR");
			if($query)
			{
				while ($row=$query->GetRow())
				{
					$robot[$row['ip']]=$row['name'];
				}
			}
		}
		$pages=page($total,$pn,$query_link,array(),"2 10 20 50 100 200 500");
		include handler('template')->file('@admin/sessions');
	}
}
?>