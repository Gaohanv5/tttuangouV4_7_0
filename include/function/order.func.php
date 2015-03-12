<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name order.func.php
 * @date 2014-09-01 17:24:22
 */
 


function __order($order_by_list='',$query_link='',$config=array())
{
	if(empty($order_by_list))
	{
		return false;
	}

	extract($config,EXTR_SKIP);

		$asc_mark = '▲';
	$desc_mark = '▼';

	$result = array();

	if('' == $order_by)
	{
		$order_by_default = "default";
		if(isset($order_by_list['order_by_default']) and $order_by_list['order_by_default'])
		{
			$order_by_default = $order_by_list['order_by_default'];

			unset($order_by_list['order_by_default']);
		}

		$order_by = empty($order_by_list[$_GET['order_by']]) ? $order_by_default : $_GET['order_by'];
		$result['is_default'] = ($order_by==$order_by_default);

		$result['order_by'] = $result['is_default'] ? '' : $order_by;
	}

	if('' != $order_by)
	{
		$order_type = $order_type ? $order_type : (('ASC' == strtoupper($_GET['order_type'])) ? "ASC" : "DESC");

		$result['order_type'] = ('DESC' == $order_type) ? "" : 'asc';

		$result['name'] = $result['order_name'] = $order_by_list[$order_by]['name'];

		$result['order_string'] = ($order_by_list[$order_by]['order'] ? $order_by_list[$order_by]['order'] : $order_by_list[$order_by]['order_by'])." {$order_type}";

		if($result['order_string']) $result['order'] = "ORDER BY ".$result['order_string'];
	}
	else
	{
		$order_type = '';
	}

	if(isset($result[$return]))
	{
		return $result[$return];
	}


	global $rewriteHandler;

	if('' == $query_link)
	{
		$query_link = '?' . ((is_array($_POST) and count($_POST)) ? http_build_query(array_merge($_GET,$_POST)) : $_SERVER['QUERY_STRING']);
	}

	$un_order_type = ('DESC' == $order_type) ? 'ASC' : 'DESC';

	if(false!==strpos($query_link,'&order_by='))
	{
		$query_link = preg_replace("/\&order_by\=[^\&]+/i",'',$query_link);
	}

	if(false!==strpos($query_link,'&order_type='))
	{
		$query_link = preg_replace("/\&order_type\=[^\&]+/i",'',$query_link);
	}

	$result['query_link'] = $query_link;
	if($result['order_by'])
	{
		$result['query_link'] .= "&order_by={$result['order_by']}";

		if($result['order_type'])
		{
			$result['query_link'] .= "&order_type={$result['order_type']}";
		}
	}

	$html = NULL;
	foreach ((array) $order_by_list as $o_by=>$o)
	{
		if((false == isset($o['display']) or $o['display']) and $o_by and isset($o['name']) and ''!=$o['name'])
		{
			$href = (($o_by == $order_by_default) ? "{$query_link}" : "{$query_link}&order_by={$o_by}");
			if($display_un_href) $un_href = $href . (('DESC' == $un_order_type) ? '' : '&order_type=asc');

			if($rewriteHandler)
			{
				$href = $rewriteHandler->formatURL($href,null);
				if($un_href) $un_href = $rewriteHandler->formatURL($un_href,null);
			}

			$html .= ($order_by == $o_by) ? ($un_href ? " <a href='{$un_href}'><b>{$o['name']}</b></a>" : "<b>{$o['name']}</b>") . ('DESC' == $order_type ? $desc_mark : $asc_mark) . " " : " <a href='{$href}'>{$o['name']}</a> ";
		}
	}
	$result['html'] = $html;

	if(isset($result[$return]))
	{
		return $result[$return];
	}

	return $result;
}

?>