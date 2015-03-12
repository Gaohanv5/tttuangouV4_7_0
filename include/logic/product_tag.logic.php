<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name product_tag.logic.php
 * @date 2014-10-30 10:42:13
 */
 




class Product_tagLogic
{
	public function html($product_id) {
		echo self::get_view($product_id, ' ', "<i><span title='%s'>%s</span></i>");
	}

	public function get_one($product_id, $tag_id) {
		$product_id = (int) $product_id;
		$tag_id = (int) $tag_id;
		if($product_id > 0 && $tag_id > 0) {
			return dbc(DBCMax)->select('product_tag')->where(array('product_id'=>$product_id, 'tag_id'=>$tag_id))->limit(1)->done();
		} else {
			return false;
		}
	}

	public function get_list($product_id, $empty_retry = false) {
		$rets = array();
		$list = logic('tag')->get_list($product_id, $empty_retry);
		if(is_array($list) && count($list)) {
			foreach($list as $r) {
				if($r['name'] && (empty($r['expire']) || $r['expire_time'] >= time())) {
					$rr = array(
						'id' => $r['id'],
						'name' => $r['name'],
						'desc' => $r['desc'],
					);
					if($r['enable']) {
						$rets[] = $rr;
					}
				}
			}
		}
		return $rets;
	}

	public function get_view($product_id, $implode_glue = ' &nbsp; ', $tpl_format = '') {
		$rets = array();
		$list = self::get_list($product_id);
		$tpl_format = ($tpl_format ? $tpl_format : "<span title='%s'>%s</span>");
		if(is_array($list) && count($list)) {
			foreach($list as $row) {
				$rets[] = sprintf($tpl_format, $row['desc'], $row['name']);
			}
		}
		return implode($implode_glue, $rets);
	}

	public function delete($product_id, $tag_id) {
		$ptr = self::get_one($product_id, $tag_id);
		if($ptr) {
			dbc(DBCMax)->delete('product_tag')->where(array('product_id'=>$ptr['product_id'], 'tag_id'=>$ptr['tag_id']))->limit(1)->done();
		}
	}
	
	public function save($product_id, $tag_ids) {
		$count = 0;
		$product_id = (int) $product_id;
		if($product_id > 0) {
			$tag_ids = (array) $tag_ids;
			foreach($tag_ids as $tag_id) {
				$tag_id = (int) $tag_id;
				if($tag_id > 0 && false != ($tr = logic('tag')->get_one($tag_id)) && false == self::get_one($product_id, $tag_id)) {
					if($tr['enable']) {
						$count = $count + 1;
					}
					if($count > 6) {
						$tr['enable'] = 0;
					}
					dbc(DBCMax)->insert('product_tag')->data(array(
						'product_id' => $product_id,
						'tag_id' => $tag_id,
						'order' => $tr['order'],
						'enable' => $tr['enable'],
						'expire' => $tr['expire'],
						'expire_time' => $tr['expire_time'],
					))->done();
				}
			}
		}
	}

	public function get_product_ids($tag_id, $filter = true) {
		$pids = array();
		$rets = dbc(DBCMax)->select('product_tag')->where(array('tag_id' => (int) $tag_id))->done();
		if($rets) {
			foreach($rets as $r) {
				if(false == $filter || $r['enable']) {
					$pids[] = $r['product_id'];
				}
			}
		}
		return $pids;
	}

	public function product_sql_filter($prefix = 'p.') {
		$tag_id = get('tag', 'int');
		if($tag_id < 1) {
			return 1;
		}
		return " {$prefix}`id` IN ('" . implode("','", $this->get_product_ids($tag_id)) . "') ";		
	}

}

?>