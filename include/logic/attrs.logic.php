<?php

/**
 * 逻辑区：属性管理
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package logic
 * @name attrs.logic.php
 * @version 1.0
 */

class AttrsManageLogic
{
	
	public function get($product_id = null)
	{
		$cats = $this->cat_list($product_id);
		foreach ($cats as $i => $cat)
		{
			$cats[$i]['attrs'] = $this->attr_list($cat['id']);
		}
		return $cats;
	}
	
	public function is_product_has_attrs($product_id)
	{
		return dbc(DBCMax)->select('attrs_cat')->where(array('product_id' => $product_id))->limit(1)->done() ? true : false;
	}
	
	public function ProductSubmit($product_id)
	{
		$cat = post('oocix-cat');
		$attr = post('oocix-attr');
		if ($cat && $attr)
		{
			foreach ($cat as $ckey => $cdata)
			{
				$cat_id = $this->cat_get_id($product_id, $cdata);
				if ($cat_id > 0)
				{
					$cat_attrs = isset($attr[$ckey]) ? $attr[$ckey] : array();
					foreach ($cat_attrs as $akey => $adata)
					{
						$attr_id = $this->attr_get_id($cat_id, $adata);
					}
				}
				else
				{
					return false;
				}
			}
		}
	}
	
	public function cat_get_id($product_id, $cat_data)
	{
		if ($cat_data['id'])
		{
			$cwhere = array('id' => $cat_data['id']);
		}
		else
		{
			$cwhere = array('product_id' => $product_id, 'name' => $cat_data['name']);
		}
		$cat_db = dbc(DBCMax)->select('attrs_cat')->where($cwhere)->limit(1)->done();
		if ((int)$cat_db['id'] > 0)
		{
			if ($cat_db['name'] != $cat_data['name'] || $cat_db['required'] != $cat_data['required'])
			{
								$efr = dbc(DBCMax)->update('attrs_cat')->where(array('id' => $cat_db['id']))->data($cat_data)->done();
				return $efr ? $cat_db['id'] : -1;
			}
			else
			{
				return $cat_db['id'];
			}
		}
		else
		{
			$cat_id = dbc(DBCMax)->insert('attrs_cat')->data(array('product_id' => $product_id, 'name' => $cat_data['name'], 'required' => $cat_data['required']))->done();
			if ($cat_id > 0)
			{
				return $cat_id;
			}
			else
			{
				return -1;
			}
		}
	}
	
	public function attr_get_id($cat_id, $attr_data)
	{
		if (isset($attr_data['pricemoves']))
		{
			$attr_data['price_moves'] = $attr_data['pricemoves'];
			unset($attr_data['pricemoves']);
		}
		if ($attr_data['id'])
		{
			$cwhere = array('id' => $attr_data['id']);
		}
		else
		{
			$cwhere = array('cat_id' => $cat_id, 'name' => $attr_data['name']);
		}
		$attr_db = dbc(DBCMax)->select('attrs')->where($cwhere)->limit(1)->done();
		if ((int)$attr_db['id'] > 0)
		{
			if ($attr_db['name'] != $attr_data['name'] || $attr_db['price_moves'] != $attr_data['price_moves'] || $attr_db['binding'] != $attr_data['binding'])
			{
								$efr = dbc(DBCMax)->update('attrs')->where(array('id' => $attr_db['id']))->data($attr_data)->done();
				return $efr ? $attr_db['id'] : -1;
			}
			else
			{
				return $attr_db['id'];
			}
		}
		else
		{
			$attr_id = dbc(DBCMax)->insert('attrs')->data(array('cat_id' => $cat_id, 'name' => $attr_data['name'], 'price_moves' => $attr_data['price_moves'], 'binding' => $attr_data['binding']))->done();
			if ($attr_id > 0)
			{
				return $attr_id;
			}
			else
			{
				return -1;
			}
		}
	}
	
	public function cat_list($product_id)
	{
		$r = dbc(DBCMax)->select('attrs_cat')->where(array('product_id' => $product_id))->done();
		return $r ? $r : array();
	}
	
	public function cat_one($cat_id)
	{
		$r = dbc(DBCMax)->select('attrs_cat')->where(array('id' => $cat_id))->limit(1)->done();
		return $r ? $r : array();
	}
	
	public function attr_one($attr_id)
	{
		$r = dbc(DBCMax)->select('attrs')->where(array('id' => $attr_id))->limit(1)->done();
		return $r ? $r : array();
	}
	
	public function attr_list($cat_id)
	{
		$r = dbc(DBCMax)->select('attrs')->where(array('cat_id' => $cat_id))->done();
		return $r ? $r : array();
	}
	
	public function cat_remove($cat_id)
	{
		$ef1 = dbc(DBCMax)->delete('attrs_cat')->where(array('id' => $cat_id))->done();
		$ef2 = dbc(DBCMax)->delete('attrs')->where(array('cat_id' => $cat_id))->done();
		return $ef1 + $ef2;
	}
	
	public function attr_remove($attr_id)
	{
		return dbc(DBCMax)->delete('attrs')->where(array('id' => $attr_id))->done();
	}
	
	public function html($data)
	{
		switch (mocod())
		{
			case 'buy.checkout':
				$pro_attrs = $this->get($data['id']);
				$pro_attrs && include handler('template')->file('attrs_selector');
				break;
			case 'buy.order':
								foreach ($data as $order)
				{
				    $ord_attrs[] = $this->snapshot($order['orderid']);
				}
				$ord_attrs && include handler('template')->file('attrs_displayer');
				break;
		}
	}
	
	public function Accessed($action, &$order)
	{
		if ($action == 'order.save')
		{
			#解析参数
			$attrs = array();
			foreach ($_POST as $pk => $pv)
			{
				if (substr($pk, 0, 6) == 'cat_f_')
				{
					list($cat_id, $attr_id) = explode(':', $pv);
					if (is_numeric($cat_id) && is_numeric($attr_id))
					{
						$attrs[$cat_id] = $attr_id;
					}
				}
			}
			#取出所有该商品属性列表
			$attrs_all = $this->get($order['productid']);
			if (!$attrs_all)
			{
				return true;
			}
			#提交过来的属性合法性判断
			foreach ($attrs_all as $cat)
			{
				if ($cat['required'] == 'true')
				{
					if (isset($attrs[$cat['id']]))
					{

					}
					else
					{
						return false;
					}
				}
			}
			#商品属性快照保存
			if ($attrs)
			{
				if (is_numeric($this->snapshot_save($order['orderid'], $order['productnum'], $attrs)))
				{
					return true;
				}
			}
			return false;
		}
		if ($action == 'order.show')
		{
			$sign = $order['orderid'];
			$snapshot = $this->order_get($sign);
			if ($snapshot)
			{
				$order['price_of_total'] += $snapshot['price'];
			}
		}
	}
	
	public function order_calc($sign, &$price_total)
	{
		$snapshot = $this->order_get($sign);
		if ($snapshot)
		{
			$price_total += $snapshot['price'];
		}
	}
	
	public function snapshot_save($sign, $num, $attrs_idx)
	{
		$price_all = 0.00;
		$saved = array();
		foreach ($attrs_idx as $cat_id => $attr_id)
		{
			$attr = $this->attr_one($attr_id);
			if ($attr)
			{
				$price_all += ($attr['binding'] == 'true' ? $num * $attr['price_moves'] : $attr['price_moves']);
								$data = $this->cat_one($cat_id);
				$data['attr'] = $attr;
				$data['num'] = $num;
				$saved[$cat_id] = $data;
			}
		}
		if ($saved)
		{
			$data = serialize($saved);
			if ($this->order_get($sign))
			{
				$this->order_update($sign, $price_all, $data);
			}
			else
			{
				$this->order_append($sign, $price_all, $data);
			}
		}
		return $price_all ? $price_all : 0;
	}
	
	public function snapshot($sign, $product_id = null)
	{
		if (is_numeric($product_id) && $product_id > 0)
		{
			static $pro_attr_state = array();
			isset($pro_attr_state[$product_id]) || $pro_attr_state[$product_id] = $this->is_product_has_attrs($product_id) ? 1 : 0;
			if ($pro_attr_state[$product_id] == 0)
			{
				return array();
			}
		}
		$snapshot = $this->order_get($sign);
		if ($snapshot)
		{
			$data = unserialize($snapshot['data']);
			$return = array();
			foreach ($data as $cat_id => $cat)
			{
				$return['attrs'][] = array(
					'cat' => array(
						'name' => $cat['name']
					),
					'attr' => array(
						'name' => $cat['attr']['name'],
						'price' => $cat['attr']['binding'] == 'true' ? number_format($cat['attr']['price_moves'] * $cat['num'],2,'.','') : $cat['attr']['price_moves']
					)
				);
				$return['dsp'][] = array(
					'name' => $cat['name'] . ' / ' . $cat['attr']['name'],
					'price' => $cat['attr']['binding'] == 'true' ? number_format($cat['attr']['price_moves'] * $cat['num'],2,'.','') : $cat['attr']['price_moves']
				);
			}
			$return['price_all'] = $snapshot['price'];
						return $return;
		}
		return array();
	}
	
	private function order_get($sign)
	{
		$r = dbc(DBCMax)->select('attrs_order')->where(array('sign' => $sign))->limit(1)->done();
		return $r ? $r : array();
	}
	
	private function order_append($sign, $price, $data)
	{
		return dbc(DBCMax)->insert('attrs_order')->data(array('sign' => $sign, 'price' => $price, 'data' => $data))->done();
	}
	
	private function order_update($sign, $price, $data)
	{
		return dbc(DBCMax)->update('attrs_order')->data(array('price' => $price, 'data' => $data))->where(array('sign' => $sign))->done();
	}
}

?>