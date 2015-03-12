<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name voa.mod.php
 * @date 2014-09-01 17:24:23
 */
 



class ModuleObject extends MasterObject
{
	function ModuleObject($config)
	{
		$this->MasterObject($config);		Load::logic('product');
		$this->ProductLogic = new ProductLogic();
		Load::logic('pay');
		$this->PayLogic = new PayLogic();
		Load::logic('me');
		$this->MeLogic = new MeLogic();
		Load::logic('order');
		$this->OrderLogic = new OrderLogic();
		$this->config =$config;
		Load::moduleCode($this);$this->Execute();
	}
	function Execute()
	{
		switch($this->Code)
		{
			case 'orderverify':
				$this->OrderVerify();
				break;
			default:
				$this->Main();
				break;
		}
	}
	function Main()
	{
		$this->CheckAdminPrivs('ordermanage');
		$this->OrderVerify();
	}
	function OrderVerify()
	{
		$this->CheckAdminPrivs('ordermanage');
		$op = $_GET['op'];
		if ('create' == $op)
		{
			extract($this->Get);
			$mov = (int)$mov;
			for ($i=1; $i<=$mov; $i++)
			{
				logic('coupon')->Create($pid, $oid, $uid);
			}
			$i --;
			echo '已补单'.$i.'个';
			return;
		}
		$sql = 'SELECT o.*,m.username,m.phone,p.name FROM '.TABLE_PREFIX.'tttuangou_order o LEFT JOIN '.TABLE_PREFIX.'system_members m ON(m.uid=o.userid) LEFT JOIN '.TABLE_PREFIX.'tttuangou_product p ON(p.id=o.productid) WHERE p.type="ticket" AND o.pay=1 AND o.status='.ORD_STA_Normal.' AND o.process="TRADE_FINISHED"';
		$query = $this->DatabaseHandler->Query($sql);
		if ($query)
		{
			$orders = $query->GetAll();
			$finds = array();
			foreach ($orders as $i => $order)
			{
				$tickSql = 'SELECT COUNT(*) AS tickCount FROM '.TABLE_PREFIX.'tttuangou_ticket WHERE orderid='.$order['orderid'].' AND uid='.$order['userid'];
				$result = $this->DatabaseHandler->Query($tickSql)->GetRow();
				$tickCount = $result['tickCount'];
				if ($order['productnum'] != $tickCount)
				{
					$order['tickCount'] = $tickCount;
					$order['tickMov'] = $order['productnum']-$tickCount;
					$finds[] = $order;
				}
			}
		}
		include(handler('template')->file('@admin/voa_order_verify'));
	}
}
?>