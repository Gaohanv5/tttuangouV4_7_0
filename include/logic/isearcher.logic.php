<?php

/**
 *
 * 逻辑区：智能搜索
 *
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package logic
 * @name isearcher.logic.php
 * @version 1.0
 */

class iSearcherLogic
{
    
    function Search($fid, $wd)
    {
        $map = ini('isearcher.map.'.$fid);
        if (!$map)
        {
            return $this->__AJax_NOT_Found();
        }
        $table = $map['table'];
                $field = '';
        $where = '';
        $wheres = array();
        $title_field = '';
        $srcs = (array) $map['src'];
        $wd = trim($wd);
        foreach($srcs as $k) {
        	        	empty($title_field) && $title_field = $k;
        	$k = '`' . $k . '`';
        	$field .= ',' . $k;
        	$wheres[$k] = " ({$k} LIKE '%{$wd}%') ";
        }
        $where = ' ( ' . implode(' OR ', $wheres) . ' ) ';
        $iwField = $map['idx'];
        $exsql = $table == 'product' ? ' AND `saveHandler`="normal" ORDER BY `addtime` desc' : '';
        $sql = 'SELECT '.$iwField.$field.' FROM '.table($table).' WHERE '.$where.$exsql.' LIMIT 0, 10';
        
        $query = dbc()->Query($sql);
        if (!$query)
        {
            return $this->__AJax_NOT_Found();
        }
        $result = $query->GetAll();
        if (count($result) == 0)
        {
            return $this->__AJax_NOT_Found();
        }
        $ops = array(
            'resultCount' => count($result)
        );
        foreach ($result as $i => $one)
        {
            $ops['results'][] = array(
                'title' => $one[$title_field],
                'key' => $map['key'],
                'val' => $one[$iwField]
            );
        }
        return $ops;
    }
    
    public function iiSearch($map, &$key, &$wd)
    {
        if ($key != 'wd')
        {
                        $wd = ' ='.$wd;
            return;
        }
                $key = $map['key'];
        $table = $map['table'];
                $field = '';
        $where = '';
        $wheres = array();
        $srcs = (array) $map['src'];
        $wd = trim($wd);
        foreach($srcs as $k) {
        	        	$k = '`' . $k . '`';
        	$field .= ',' . $k;
        	$wheres[$k] = ($k == '`id`') ? " ({$k} = '{$wd}') " : " ({$k} LIKE '%{$wd}%') ";
        }
        $where = ' ( ' . implode(' OR ', $wheres) . ' ) ';
        $iwField = $map['idx'];
        $exsql = $table == 'product' ? ' AND `saveHandler`="normal" ORDER BY `addtime` desc' : '';
        $sql = 'SELECT '.$iwField.$field.' FROM '.table($table).' WHERE '.$where.$exsql;
        $query = dbc()->Query($sql);
        if (!$query)
        {
            $key = '__404__';
            return;
        }
        $ids = $query->GetAll();
        if (count($ids) == 0)
        {
            $key = '__404__';
            return;
        }
        $idx = '';
        foreach ($ids as $i => $id)
        {
            $idx .= $id[$iwField].',';
        }
        $idx = substr($idx, 0, -1);
        $wd = ' IN('.$idx.')';
    }
    
    private function __AJax_NOT_Found()
    {
        return array(
            'resultCount' => 0,
            'msg' => __('没有找到相关内容！'),
        );
    }
    
    public function Linker(&$sql)
    {
        $swd = get('search','string');
        if ($swd)
        {
            list($key, $wd) = explode(':', $swd);
                                            }
        else
        {
            $key = false;
            $wd = false;
        }
        $ssrc = get('ssrc', 'txt');
        if ($ssrc)
		{
			$map = ini('isearcher.map.'.$ssrc);
		}
		else
		{
			$map = false;
		}
		$mocod = str_replace('.', '_', mocod());
		if ($key && $wd && $map)
		{
			$parser = '_lnk_of_'.$mocod;
			$this->$parser($sql, $key, $wd, $map);
		}
		$this->timev_invades($sql, $mocod, array(
			'order_vlist' => 'o.',
			'coupon_vlist' => 't.',
            'recharge_order' => '',
			'seller_order' => 'o.',
			'seller_ticket' => 't.',
			'salecount_product' => 'o.',
			'salecount_payment' => 'o.',
			'salecount_user' => 'o.',
			'salecount_fund' => 'o.',
            'member_moneylog' => 'u.',
            'cash_order' => '',
		));
    }
	
	public function product_sql_filter()
	{
		$kw = $this->get_kw();
		if ($kw)
		{
			get('page', 'int') > 0 || $_GET['page'] = 1;
			return '(p.flag like "%'.$kw.'%" or p.name like "%'.$kw.'%" or p.intro like "%'.$kw.'%")';
		}
		else
		{
			return '1';
		}
	}
	public function seller_sql_filter()
	{
		$kw = $this->get_kw();
		if ($kw)
		{
			get('page', 'int') > 0 || $_GET['page'] = 1;
			return '(s.sellername like "%'.$kw.'%" or s.selleraddress like "%'.$kw.'%" or s.content like "%'.$kw.'%")';
		}
		else
		{
			return '1';
		}
	}
	
	public function inputer()
	{
		$kw = $this->get_kw();
		include handler('template')->file('isearcher_inputer');
        return $kw;
	}
	
	private function get_kw($key = 'kw', $filter_rule = '/[\~\!\@\#\$\%\^\&\*\(\)\_\+\`\-\=\{\}\:\"\|\<\>\?\[\]\;\\\'\\\\\,\/]/')
	{
		$kw = get($key, 'string');
		if ($filter_rule)
		{
			$kw = preg_replace($filter_rule, '', $kw);
		}
		$kw = trim($kw);
        $ku = ENC_G2U($kw);
        $kg = ENC_U2G($kw);
        if ($kw == $ku)
        {
            $ic = 'utf8';
        }
        elseif ($kw == $kg)
        {
            $ic = 'gbk';
        }
        if (ENC_IS_GBK)
        {
            if ($ic == 'utf8')
            {
                $kw = $kg;
            }
        }
        else
        {
            if ($ic == 'gbk')
            {
                $kw = $ku;
            }
        }
		return $kw;
	}
	
	private function timev_invades(&$sql, $area, $prefixMaps = array())
	{
		if (isset($prefixMaps[$area]))
		{
			$prefix = $prefixMaps[$area];
			if (isset($_GET['iscp_tv_area']))
			{
				$area = get('iscp_tv_area', 'txt');
				$inskey = get('iscp_tvfield_'.$area, 'txt');
				if ($inskey)
				{
					$sd = array();
					$tvss = ini('isearcher.timev.'.$area);
					if ($tvss)
					{
						foreach ($tvss as $tvsd)
						{
							if ($tvsd['key'] == $inskey)
							{
								$sd = $tvsd;
								continue;
							}
						}
					}
					if ($sd)
					{
						$begin = get('iscp_tvbegin_'.$area, 'txt');
						$finish = get('iscp_tvfinish_'.$area, 'txt');
												$dbfield = $prefix.$sd['field'];
												if(in_array($area,array('seller_ticket','coupon_main','coupon_vlist'))){
							$ts_begin = $begin ? date('Ymd',strtotime($begin)) : 0;
							$ts_finish = 0;
							if($finish) {
								$finish_time = strtotime($finish);
								$ts_finish = date('Ymd', mktime(24, 0, 0, date('m', $finish_time), date('d', $finish_time), date('Y', $finish_time)));
							}
                            
						}else{
							$ts_begin = $begin ? strtotime($begin) : 0;
							$ts_finish = $finish ? (strtotime($finish) + 86399) : 0;
						}
						if ($ts_begin || $ts_finish)
						{
							$ts = array();
														$ts[] = $ts_begin ? $dbfield.' >= '.$ts_begin : $dbfield.' >0 ';
							$ts_finish && $ts[] = $dbfield.' <= '.$ts_finish;
							$sql_where = implode(' AND ', $ts);
							if ($sql_where)
							{
								if(strstr($sql,'GROUP BY')){
									$sql = str_ireplace('GROUP BY', ' AND '.$sql_where.' GROUP BY', $sql);
								}else{
									$sql = str_ireplace('ORDER BY', ' AND '.$sql_where.' ORDER BY', $sql);
								}
							}
						}
					}
				}
			}
		}
	}
    
    private function _lnk_of_product_vlist(&$sql, $key, $wd, $map)
    {
        $this->iiSearch($map, $key, $wd);
        switch ($key)
        {
            case 'pid':
                $sql_where = 'p.id'.$wd;
                break;
            case 'sid':
                $sql_where = 'p.sellerid'.$wd;
                break;
            case 'cid':
                $sql_where = 'p.city'.$wd;
                break;
            default:
                $sql_where = '0';
        }
        $sql = str_replace('ORDER BY', ' AND '.$sql_where.' ORDER BY', $sql);
    }
	private function _lnk_of_seller_product(&$sql, $key, $wd, $map)
    {
        $this->iiSearch($map, $key, $wd);
        switch ($key)
        {
            case 'pid':
                $sql_where = 'p.id'.$wd;
                break;
            case 'sid':
                $sql_where = 'p.sellerid'.$wd;
                break;
            case 'cid':
                $sql_where = 'p.city'.$wd;
                break;
            default:
                $sql_where = '0';
        }
        $sql = str_replace('ORDER BY', ' AND '.$sql_where.' ORDER BY', $sql);
    }
    private function _lnk_of_order_vlist(&$sql, $key, $wd, $map)
    {
        $this->iiSearch($map, $key, $wd);
        switch ($key)
        {
            case 'pid':
                $sql_where = 'o.productid'.$wd;
                break;
            case 'oid':
                $sql_where = 'o.orderid'.$wd;
                break;
            case 'uid':
                $sql_where = 'o.userid'.$wd;
                break;
            default:
                $sql_where = '0';
        }
        $sql = str_replace('ORDER BY', ' AND '.$sql_where.' ORDER BY', $sql);
    }
	private function _lnk_of_seller_order(&$sql, $key, $wd, $map)
    {
        $this->iiSearch($map, $key, $wd);
        switch ($key)
        {
            case 'pid':
                $sql_where = 'o.productid'.$wd;
                break;
            case 'oid':
                $sql_where = 'o.orderid'.$wd;
                break;
            case 'uid':
                $sql_where = 'o.userid'.$wd;
                break;
            default:
                $sql_where = '0';
        }
        $sql = str_replace('ORDER BY', ' AND '.$sql_where.' ORDER BY', $sql);
    }
    private function _lnk_of_coupon_vlist(&$sql, $key, $wd, $map)
    {
        $this->iiSearch($map, $key, $wd);
        switch ($key)
        {
            case 'pid':
                $sql_where = 't.productid'.$wd;
                break;
            case 'oid':
                $sql_where = 't.orderid'.$wd;
                break;
            case 'uid':
                $sql_where = 't.uid'.$wd;
                break;
            case 'coid':
                $sql_where = 't.ticketid'.$wd;
                break;
            default:
                $sql_where = '0';
        }
        $sql = str_replace('ORDER BY', ' AND '.$sql_where.' ORDER BY', $sql);
    }
	private function _lnk_of_seller_ticket(&$sql, $key, $wd, $map)
    {
        $this->iiSearch($map, $key, $wd);
        switch ($key)
        {
            case 'pid':
                $sql_where = 't.productid'.$wd;
                break;
            case 'oid':
                $sql_where = 't.orderid'.$wd;
                break;
            case 'uid':
                $sql_where = 't.uid'.$wd;
                break;
            case 'coid':
                $sql_where = 't.ticketid'.$wd;
                break;
            default:
                $sql_where = '0';
        }
        $sql = str_replace('ORDER BY', ' AND '.$sql_where.' ORDER BY', $sql);
    }
    private function _lnk_of_delivery_vlist(&$sql, $key, $wd, $map)
    {
        $this->iiSearch($map, $key, $wd);
        switch ($key)
        {
            case 'pid':
                $sql_where = 'o.productid'.$wd;
                break;
            case 'oid':
                $sql_where = 'o.orderid'.$wd;
                break;
            case 'uid':
                $sql_where = 'o.userid'.$wd;
                break;
            default:
                $sql_where = '0';
        }
        $sql = str_replace('ORDER BY', ' AND '.$sql_where.' ORDER BY', $sql);
    }
	private function _lnk_of_seller_delivery(&$sql, $key, $wd, $map)
    {
        $this->iiSearch($map, $key, $wd);
        switch ($key)
        {
            case 'pid':
                $sql_where = 'o.productid'.$wd;
                break;
            case 'oid':
                $sql_where = 'o.orderid'.$wd;
                break;
            case 'uid':
                $sql_where = 'o.userid'.$wd;
                break;
            default:
                $sql_where = '0';
        }
        $sql = str_replace('ORDER BY', ' AND '.$sql_where.' ORDER BY', $sql);
    }
    private function _lnk_of_recharge_card(&$sql, $key, $wd, $map)
    {
        $this->iiSearch($map, $key, $wd);
        switch ($key)
        {
            case 'rcid':
                $sql_where = 'id'.$wd;
                break;
            default:
                $sql_where = '0';
        }
        $sql = str_replace('ORDER BY', ' AND '.$sql_where.' ORDER BY', $sql);
    }
    private function _lnk_of_recharge_order(&$sql, $key, $wd, $map)
    {
        $this->iiSearch($map, $key, $wd);
        switch ($key)
        {
            case 'orderid':
                $sql_where = 'orderid'.$wd;
                break;
            case 'userid':
                $sql_where = 'userid'.$wd;
                break;
            default:
                $sql_where = '0';
        }
        $sql = str_replace('oRDEr bY', ' AND '.$sql_where.' ORDER BY', $sql);
    }
	private function _lnk_of_cash_order(&$sql, $key, $wd, $map)
    {
        $this->iiSearch($map, $key, $wd);
        switch ($key)
        {
            case 'orderid':
                $sql_where = 'orderid'.$wd;
                break;
            case 'userid':
                $sql_where = 'userid'.$wd;
                break;
            default:
                $sql_where = '0';
        }
        $sql = str_replace('oRDEr bY', ' AND '.$sql_where.' ORDER BY', $sql);
    }
	private function _lnk_of_fund_order(&$sql, $key, $wd, $map)
    {
        $this->iiSearch($map, $key, $wd);
        switch ($key)
        {
            case 'orderid':
                $sql_where = 'orderid'.$wd;
                break;
            case 'userid':
                $sql_where = 'userid'.$wd;
                break;
			case 'sellerid':
                $sql_where = 'sellerid'.$wd;
                break;
            default:
                $sql_where = '0';
        }
        $sql = str_replace('oRDEr bY', ' AND '.$sql_where.' ORDER BY', $sql);
    }
	private function _lnk_of_salecount_product(&$sql, $key, $wd, $map)
    {
        $this->iiSearch($map, $key, $wd);
        switch ($key)
        {
            case 'pid':
                $sql_where = 'o.productid'.$wd;
                break;
            case 'sid':
                $sql_where = 'p.sellerid'.$wd;
                break;
            case 'uid':
                $sql_where = 'm.uid'.$wd;
                break;
            default:
                $sql_where = '0';
        }
        $sql = str_replace('GROUP BY', ' AND '.$sql_where.' GROUP BY', $sql);
    }
	private function _lnk_of_salecount_payment(&$sql, $key, $wd, $map)
    {
        $this->iiSearch($map, $key, $wd);
        switch ($key)
        {
            case 'pid':
                $sql_where = 'o.productid'.$wd;
                break;
            default:
                $sql_where = '0';
        }
        $sql = str_replace('GROUP BY', ' AND '.$sql_where.' GROUP BY', $sql);
    }
	private function _lnk_of_salecount_user(&$sql, $key, $wd, $map)
    {
        $this->iiSearch($map, $key, $wd);
        switch ($key)
        {
            case 'uid':
                $sql_where = 'm.uid'.$wd;
                break;
            default:
                $sql_where = '0';
        }
        $sql = str_replace('GROUP BY', ' AND '.$sql_where.' GROUP BY', $sql);
    }
	private function _lnk_of_salecount_fund(&$sql, $key, $wd, $map)
    {
        $this->iiSearch($map, $key, $wd);
        switch ($key)
        {
            case 'pid':
                $sql_where = 'o.productid'.$wd;
                break;
            case 'sid':
                $sql_where = 'p.sellerid'.$wd;
                break;
            case 'uid':
                $sql_where = 'm.uid'.$wd;
                break;
            default:
                $sql_where = '0';
        }
        $sql = str_replace('GROUP BY', ' AND '.$sql_where.' GROUP BY', $sql);
    }
    private function _lnk_of_member_moneylog(&$sql, $key, $wd, $map)
    {
        $this->iiSearch($map, $key, $wd);
        switch ($key)
        {
            case 'userid':
                $sql_where = 'u.userid'.$wd;
                break;
            case 'type':
                $sql_where = 'u.type'.$wd;
                break;
            default:
                $sql_where = '0';
        }
        $sql = str_replace('oRDEr bY', ' AND '.$sql_where.' ORDER BY', $sql);
    }
}
?>