<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name tag.mod.php
 * @date 2014-10-30 10:42:13
 */
 


class ModuleObject extends MasterObject
{
	function ModuleObject( $config )
	{
		$this->MasterObject($config);
		$runCode = Load::moduleCode($this);
		$this->$runCode();
	}

	function Main()
	{
		$this->CheckAdminPrivs('tag');

		$act = get('act');

		if('save' == $act) {
			logic('tag')->save();
			$this->Messager('设置成功', 'admin.php?mod=tag');
		} elseif ('del' == $act) {
			$id = get('id', 'int');
			if($id < 1) {
				$this->Messager('请指定一个要删除的标签ID', 'admin.php?mod=tag');
			}
			logic('tag')->delete($id);
			$this->Messager('删除成功', 'admin.php?mod=tag');
		} else {
			$list = logic('tag')->get_all(false);
		}

		include handler('template')->file('@admin/tag');
	}

	function save()
	{
		if (true === ENC_IS_GBK && ($_REQUEST['in_ajax'] || true === X_IS_AJAX)) {
			$_POST = array_iconv('UTF-8', 'GBK', $_POST);
		}
		logic('tag')->save();
	}

	function view()
	{
		$product_id = get('product_id', 'int');

		$ret = '';
		$count = 0;
		$list = logic('product_tag')->get_list($product_id, ($product_id < 1 && 'must' == get('retry')));
		if(is_array($list) && count($list)) {
			foreach($list as $r) {
				if($count++ < 6) {
					$ret .= " <input type='hidden' name='tag_ids[]' value='{$r['id']}' /> <span title='{$r['desc']}'>{$r['name']}</span> ";
				}
			}
		}

		exit($ret ? $ret : '暂时还没有设置标签');
	}

	function delete()
	{
		$product_id = get('product_id', 'int');
		$tag_id = get('tag_id', 'int');

		logic('product_tag')->delete($product_id, $tag_id);
	}

}
?>