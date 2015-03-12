<?php

/**
 * ZLOG-APIZ：产品相关
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package zlog
 * @name product.apiz.php
 * @version 1.0
 */

class productZLOG extends iMasterZLOG
{
	protected $zlogType = 'product';
	public function publish($pid, $data)
	{
		if ($data['saveHandler'] == 'draft')
		{
			$this->zlogCreate($pid, '已经为产品（ID='.$data['draft'].'）生成一份草稿存档：'.$data['flag']);
		}
		else
		{
			$this->zlogCreate($pid, '产品（'.$data['flag'].'）已经发布');
		}
	}
	public function delete($pid, $data)
	{
		$dType = ($data['saveHandler'] == 'draft') ? '草稿' : '产品';
		$this->zlogCreate($pid, $dType.'（'.$data['flag'].'）已被删除');
	}
	public function maintain($affected_rows)
	{
		$this->zlogCreate('system', '已经自动完成对产品的状态维护，影响产品数：'.$affected_rows);
	}
	public function draftClear($sql, $aff)
	{
		$aff > 0 && $this->zlogCreate('system', '已经自动清理无用的产品草稿，清理数：'.$aff, 'SQL筛选：'.addslashes($sql));
	}
	public function saveError($pid, $because)
	{
		$this->zlogCreate($pid, '保存时出错：'.$because);
	}
	public function update($pid, $newData)
	{
				if ($newData['saveHandler'] == 'draft') return;
		if (isset($newData['@extra']))
		{
			$newData = array_merge($newData, $newData['@extra']);
			unset($newData['@extra']);
		}
		$r = $this->dataCompare($pid, $newData);
		if ($r['cstring'] == '')
		{
			return;
		}
				$this->zlogCreate($pid, '产品（'.$r['data']['flag'].'）数据更新', $r['cstring']);
	}
	private function dataCompare($pid, $newData)
	{
		$cString = '';
		$oldData = logic('product')->SrcOne($pid);
		$oldData = array_merge($oldData, $this->getOldExtraData($pid));
		foreach ($newData as $k => $v)
		{
			$ov = isset($oldData[$k]) ? $oldData[$k] : false;
			if (!$ov) continue;
			if ($ov == $v) continue;
			if (is_string($v) && (strlen($ov) > 100 || strlen($v) > 100))
			{
				continue;
			}
			else
			{
				$cString .= '修改了 “<i><b>'.$this->dataFieldName($k).'</b></i>” ，之前是 “<strike>'.thtmlspecialchars($ov).'</strike>” ，现在是 “'.thtmlspecialchars($v).'”';
			}
			$cString .= '<br/>';
		}
		return array('data' => $oldData, 'cstring' => $cString);
	}
	
	private function getOldExtraData($pid)
	{
		$r = array();
		if (post('__catalog_subclass_old'))
		{
			$r['category'] = post('__catalog_subclass_old', 'int');
		}
		else
		{
			$r['category'] = 0;
		}
		$r['hideseller'] = meta('p_hs_'.$pid) ? 'true' : 'false';
		$r['irebates'] = meta('p_ir_'.$pid) ? 'true' : 'false';
		$oExpressList = meta('expresslist_of_'.$pid);
		$r['expresslist'] = $oExpressList ? $oExpressList : '';
		$oPaymentList = meta('paymentlist_of_'.$pid);
		$r['specialPayment'] = $oPaymentList ? 'true' : 'false';
		$r['specialPaymentSel'] = $oPaymentList;
		return $r;
	}
	private function dataFieldName($k)
	{
		$m = array(
			'name' => '产品标题',
			'flag' => '简短名称',
			'city' => '投放城市',
			'display' => '显示方式',
			'sellerid' => '合作商家',
			'order' => '显示优先级',
			'price' => '原价',
			'nowprice' => TUANGOU_STR . '价',
			'maxnum' => '产品总数量',
			'begintime' => TUANGOU_STR . '开始时间',
			'overtime' => TUANGOU_STR . '结束时间',
			'type' => TUANGOU_STR . '类型',
			'perioddate' => TUANGOU_STR . '券有效期',
			'allinone' => '多券合一',
			'weight' => '产品重量',
			'successnum' => '成功'. TUANGOU_STR . '人数',
			'virtualnum' => '虚拟购买人数',
			'oncemax' => '一次最多购买数量',
			'oncemin' => '一次最少购买数量',
			'img' => '产品图片编号',
			'multibuy' => '是否允许多次购买',
			'category' => '产品分类',
			'hideseller' => '是否隐藏商家信息',
			'irebates' => '是否参与邀请返利',
			'expresslist' => '指定配送方式列表',
			'specialPayment' => '是否使用统一的支付方式',
			'specialPaymentSel' => '指定支付方式列表'
		);
		return isset($m[$k]) ? $m[$k] : $k;
	}
}

?>