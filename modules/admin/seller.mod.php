<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name seller.mod.php
 * @date 2014-09-01 17:24:22
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
		exit('ok');
	}
	
	public function add_image() {
		$this->CheckAdminPrivs('seller');
		$seller_id = get('seller_id', 'int');
		$id = get('id', 'int');
		if($seller_id > 0 && $id > 0) {
			$s = logic('seller')->GetOne($seller_id);
			if($s) {
				$imgs = explode(',', $s['imgs']);
				foreach ($imgs as $i => $iid)
				{
					if ($iid == '' || $iid == 0)
					{
						unset($imgs[$i]);
					}
				}
				$imgs[] = $id;
				$new = implode(',', $imgs);
				dbc(DBCMax)->update('seller')->data(array('imgs'=>$new))->where(array('id'=>$seller_id))->done();
			}
		}
		exit('ok');
	}
	
	public function del_image() {
		$this->CheckAdminPrivs('seller');
		$seller_id = get('seller_id', 'int');
		$id = get('id', 'int');
		if($seller_id > 0 && $id > 0) {
			$s = logic('seller')->GetOne($seller_id);
			if($s) {
				if ($s['imgs'] == '')
				{
										logic('upload')->Delete($id);
				}
				else
				{
					$imgs = explode(',', $s['imgs']);
					foreach ($imgs as $i => $iid)
					{
						if ($iid == $id)
						{
							logic('upload')->Delete($id);
							unset($imgs[$i]);
						}
					}
					$new = implode(',', $imgs);
					dbc(DBCMax)->update('seller')->data(array('imgs'=>$new))->where(array('id'=>$seller_id))->done();
				}
			}
		}
		exit('ok');
	}

}
?>