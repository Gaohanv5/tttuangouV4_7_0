<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name tag.logic.php
 * @date 2014-10-30 10:42:13
 */
 




class TagLogic
{
	public function html($product_id = 0, $flag = 'list_mgr') {

		$list = self::get_list($product_id, true);

		$flag = (in_array($flag, array('list_mgr', )) ? $flag : 'list_mgr');
		include handler('template')->file('@html/tag/'.$flag);
	}

	public function navigate($limit = 0) {
		$tags = self::get_all(true, $limit);
		if($tags) {
			$tags = self::make_navigate($tags);
			include handler('template')->file('@html/tag/navigate');
		}
	}

	public function save() {
		$count = 0;
		$product_id = max(0, post('product_id', 'int'));
				$tag_new = post('tag_new');
		if(is_array($tag_new) && count($tag_new)) {
			foreach($tag_new as $row) {
				$n = cut_str(trim(strip_tags($row['name'])), 8, '');
				$d = array();
				$tag_id = 0;
				if($n) {
					$d = array(
						'name' => $n,
						'desc' => cut_str(trim(strip_tags($row['desc'])), 200, ''),
						'order' => (int) $row['order'],
						'enable' => ($row['enable'] ? 1 : 0),
					);
					$d['expire'] = $row['expire'];
					if($d['expire']) {
						$d['expire_time'] = strtotime($d['expire']);
					}
					$nr = self::get_one($n, true);
					if(false == $nr) {						
						$tag_id = dbc(DBCMax)->insert('tag')->data(array_merge($d, array('display_order' => $d['order'], 'display_enable' => $d['enable'])))->done();
					} else {
						$tag_id = $nr['id'];
					}
					if($tag_id > 0 && $product_id > 0 && false == logic('product_tag')->get_one($product_id, $tag_id)) {
						$d['product_id'] = $product_id;
						$d['tag_id'] = $tag_id;
						if($d['enable']) {
							if(++$count > 6) {
								$d['enable'] = 0;
							}
						}
						unset($d['name'], $d['desc']);
						dbc(DBCMax)->insert('product_tag')->data($d)->done();
					}
				}
			}
		}
				$tag = post('tag');
		if(is_array($tag) && count($tag)) {
			foreach($tag as $id=>$row) {
				$id = (int) $id;
				if(false == self::get_one($id)) {
					continue ;
				}
				$n = cut_str(trim(strip_tags($row['name'])), 8, '');
				$d = array();
				if($n) {
					$d = array(
						'name' => $n,
						'desc' => cut_str(trim(strip_tags($row['desc'])), 200, ''),				
					);
					if(isset($row['order'])) {
						$d['enable'] = ($row['enable'] ? 1 : 0);
						$d['order'] = (int) $row['order'];
					}
					if(isset($row['expire'])) {
						$d['expire'] = $row['expire'];
						if($d['expire']) {
							$d['expire_time'] = strtotime($d['expire']);
						}
					}
					$nr = self::get_one($n, true);
					if(false == $nr || $nr['id'] == $id) {
						$dd = array();
						if(isset($row['display_order'])) {
							$dd['display_enable'] = ($row['display_enable'] ? 1 : 0);
							$dd['order'] = $dd['display_order'] = (int) $row['display_order'];
						}
						dbc(DBCMax)->update('tag')->data(array_merge($d, $dd))->where(array('id'=>$id))->done();
					}
					if($product_id > 0) {
						if($d['enable']) {
							if(++$count > 6) {
								$d['enable'] = 0;
							}
						}
						unset($d['name'], $d['desc']);
						if(false != logic('product_tag')->get_one($product_id, $id)) {
							dbc(DBCMax)->update('product_tag')->data($d)->where(array('product_id'=>$product_id, 'tag_id'=>$id))->done();
						} else {
							$d['product_id'] = $product_id;
							$d['tag_id'] = $id;
							dbc(DBCMax)->insert('product_tag')->data($d)->done();
						}
					}
								} else {
					logic('product_tag')->delete($product_id, $id);
				}
			}
		}
		self::_list_cache_clean($product_id);
	}

	public function get_one($id, $is_name = false) {
		$key = '';
		$val = '';
		if($is_name) {
			$key = 'name';
			$val = strip_tags($id);
		} else {
			$key = 'id';
			$val = max(0, (int) $id);
		}
		if($key && $val) {
			return dbc(DBCMax)->select('tag')->where(array($key=>$val))->limit(1)->done();
		} else {
			return false;
		}
	}

	public function get_list($product_id = 0, $empty_retry = true) {
		$rets = array();
		$product_id = max(0, (int) $product_id);
		$empty_retry = ($empty_retry ? 1 : 0);
		$key = "tag/list_{$product_id}_{$empty_retry}";
		if(false === ($rets = fcache($key, 3600))) {
			if($product_id > 0) {
				$sql = " SELECT PT.*, T.id, T.name, T.desc FROM " . table('product_tag') . " PT
					LEFT JOIN " . table('tag') . " T ON T.id=PT.tag_id
					WHERE PT.product_id='{$product_id}' ORDER BY PT.order DESC ";
				$rets = dbc()->FetchAll($sql);
			}
			if($empty_retry && empty($rets)) {
				$rets = dbc(DBCMax)->select('tag')->order(' `order` DESC ')->done();
			}
			fcache($key, $rets);
		}
		return $rets;
	}

	public function delete($id) {
		$id = (int) $id;
		if($id < 1) {
			return ;
		}
		$one = self::get_one($id);
		if(false == $one) {
			return ;
		}
		dbc(DBCMax)->delete('product_tag')->where(array('tag_id' => $id))->done();
		dbc(DBCMax)->delete('tag')->where(array('id' => $id))->done();
		$this->_list_cache_clean();
	}

	public function get_all($filter = true, $limit = 0) {
		$rets = array();
		$key = "tag/list_all";
		if(false === ($rets = fcache($key, 3600))) {
			$rets = dbc(DBCMax)->select('tag')->order(' `display_order` DESC ')->done();
			fcache($key, $rets);
		}
		if(true == $filter) {
			foreach($rets as $rk=>$r) {
				if(false == $r['display_enable']) {
					unset($rets[$rk]);
				}
			}
		}
		if($limit > 0) {
			$_rets = array();
			foreach($rets as $rk=>$r) {
				if(count($_rets) < $limit) {
					$_rets[] = $r;
				}
			}
			$rets = $_rets;
			unset($_rets);
		}
		return $rets;
	}

	private function _list_cache_clean($product_id = 0) {
		fcache("tag/list_all", 0);
		fcache("tag/list_{$product_id}_0", 0);
		fcache("tag/list_{$product_id}_1", 0);
		handler('io')->ClearDir(CACHE_PATH . 'fcache/tag/');
	}

	private function make_navigate($tags, $flag = 'product') {
		$current_tag = get('tag');
		foreach ($tags as $i => $tag)
		{
			$tags[$i]['url'] = logic('url')->create($flag, array('tag' => $tag['id']));
			$tags[$i]['selected'] = ($tag['id'] == $current_tag);
		}
		return $tags;
	}

}

?>