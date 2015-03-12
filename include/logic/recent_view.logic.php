<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name recent_view.logic.php
 * @date 2014-10-30 10:42:13
 */
 




class Recent_viewLogic
{
	private $num = 10;
	
	private $cookie_name = '__recent_view_id';
	
	private $id = '';
	
	public $ids = array();
	
	public function __construct() {
		$this->reset(handler('cookie')->GetVar($this->cookie_name));
	}
	
	public function html($flag = '') {
		$products = array();
		if($this->ids) {
			$arr = array();
			$_arrs = dbc(DBCMax)->query("select `id`, `name`, `nowprice`, `price`, `img` from ".table('product')." where `id` IN ('".implode("','", $this->ids)."')")->done();
			foreach($_arrs as $r) {
				$r['img_src'] = imager((int) $r['img'], IMG_Tiny);
				$arr[$r['id']] = $r;
			}
			foreach($this->ids as $id) {
				$products[$id] = $arr[$id];
			}
		}
		
		$flag = (in_array($flag, array('top_nav_right')) ? $flag : 'top_nav_right');
		include handler('template')->file('@html/recent_view/' . $flag);
	}
	
	public function add($id) {
		$this->reset($id . '|' . $this->id);
	}
	
	public function del($id) {
		$this->reset(str_replace("|{$id}|", '', "|{$this->id}|"));
	}
	
	public function clean() {
		$this->reset();
	}

	private function reset($id = '') {
		$ids = array();
		if($id) {
			$_ids = explode('|', $id);
			$ids_count = 0;
			foreach($_ids as $_id) {
				$_id = (is_numeric($_id) ? $_id : 0);
				if($_id > 0 && $ids_count++ < $this->num) {
					$ids[$_id] = $_id;
				}
			}
		}
		$this->ids = $ids;
		$this->id = implode('|', $this->ids);
		handler('cookie')->SetVar($this->cookie_name, $this->id, ($this->id ? 864000 : 0));
	}
}

?>