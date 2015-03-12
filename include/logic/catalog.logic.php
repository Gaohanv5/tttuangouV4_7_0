<?php

/**
 * 逻辑区：产品分类目录
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package logic
 * @name catalog.logic.php
 * @version 1.0
 */

class CatalogLogic
{
	public $urlTopClass = false;
	private $cacheKEY = 'catalog.logic.procount.update';
	
	private function ProCountSync()
	{
		$lastUpdate = fcache($this->cacheKEY, dfTimer('com.catalog.procount.sync'));		
		if (false === $lastUpdate)
		{
			$topClasses = $this->GetList();
			foreach ($topClasses as $i => $topClass)
			{
				$subClasses = $this->GetList($topClass['id']);
				if (!$subClasses) continue;
				foreach ($subClasses as $ii => $subClass)
				{
															$r = dbc(DBCMax)->select('product')->in('COUNT(1) AS procount')->where('category='.$subClass['id'])->limit(1)->done();
										dbc(DBCMax)->update('catalog')->data('procount='.$r['procount'])->where('id='.$subClass['id'])->done();
										$r = dbc(DBCMax)->select('product')->in('COUNT(1) AS oslcount')->where('category='.$subClass['id'].' AND (status='.PRO_STA_Normal.' OR status='.PRO_STA_Success.')')->limit(1)->done();
										dbc(DBCMax)->update('catalog')->data('oslcount='.$r['oslcount'])->where('id='.$subClass['id'])->done();
				}
			}
			fcache($this->cacheKEY, 'TIME:DNA:' . (string) time());
		}
	}

	public function Enabled()
	{
		return ini('catalog.enabled');
	}
	public function FilterEnabled()
	{
		return ini('catalog.filter.empty.enabled');
	}
	
	public function Navigate($meituannav = 0)
	{
		$mnavall = ($meituannav == 1) ? 2 : ($meituannav == 2 ? 0 : ($meituannav > 2 ? $meituannav : 1));
		$class = $this->GetList(0,$mnavall);
		if (!$class) return array();
				$icon_data = ini('catalog.icon');
				list($topcss, $subcss) = $this->Get_nav_css($_GET['code']);
		foreach ($class as $i => $topclass)
		{
			$class[$i]['icon'] 	= $icon_data[$topclass['id']]['icon'];
			$class[$i]['script'] = stripcslashes($icon_data[$topclass['id']]['script']);
			if ($topclass['id'] > 0)
			{
				$subclass = $this->GetList($topclass['id'],$mnavall);
				if ($subclass) {
					foreach ($subclass as $n => $value) {
						if($value['flag']){
							$subclass[$n]['url'] =logic('url')->create('catalog', array('code' => $value['flag']));
						}else{
							$subclass[$n]['url'] =logic('url')->create('catalog', array('code' => $topclass['flag']));
						}
						$subclass[$n]['selected'] = $value['flag'] == $subcss ? true : false;
					}
				}
				$class[$i]['subclass'] = $subclass;
			}
			$class[$i]['selected'] = $topclass['flag'] == $topcss ? true : false;
			$class[$i]['url'] = logic('url')->create('catalog', array('code' => $topclass['flag']));
		}
		$this->ProCountSync();
		return $class;
	}
	public function seller_navigate()
	{
		$mnavall = ($meituannav == 1) ? 2 : ($meituannav == 2 ? 0 : 1);
		$class = $this->GetList(0,$mnavall);
		if (!$class) return array();
				$icon_data = ini('catalog.icon');
				list($topcss, $subcss) = $this->Get_nav_css($_GET['catalog']);
		foreach ($class as $i => $topclass)
		{
			$class[$i]['icon'] 	= $icon_data[$topclass['id']]['icon'];
			$class[$i]['script'] = stripcslashes($icon_data[$topclass['id']]['script']);
			if ($topclass['id'] > 0)
			{
				$subclass = $this->GetList($topclass['id'],$mnavall);
				if ($subclass) {
					foreach ($subclass as $n => $value) {
						if($value['flag']){
							$subclass[$n]['url'] =logic('url')->create('seller', array('catalog' => $value['flag']));
						}else{
							$subclass[$n]['url'] =logic('url')->create('seller', array('catalog' => $topclass['flag']));
						}
						$subclass[$n]['selected'] = $value['flag'] == $subcss ? true : false;
					}
				}
				$class[$i]['subclass'] = $subclass;
			}
			$class[$i]['selected'] = $topclass['flag'] == $topcss ? true : false;
			$class[$i]['url'] = logic('url')->create('seller', array('catalog' => $topclass['flag']));
		}
				return $class;
	}
	
	private function front_formatted($list, $top_code = false, &$topsd = false)
	{
		if (is_array($list))
		{
			array_unshift($list, array('id' => 0, 'name' => '全部', 'flag' => ''));
			foreach ($list as $i => $one)
			{
				$code = $top_code ? ($top_code.(($one['flag'] ? '_' : '').$one['flag'])) : $one['flag'];
				if ($_GET['code'] == $code)
				{
					$list[$i]['selected'] = true;
					$topsd = true;
				}
				$list[$i]['url'] = logic('url')->create('catalog', array('code' => $code));
			}
		}
		return $list;
	}
	
	public function GetOne($id)
	{
		return dbc(DBCMax)->select('catalog')->where('id='.$id)->limit(1)->done();
	}
	public function Flag2Name($flag){
		$infos = dbc(DBCMax)->select('catalog')->in('name')->where(array('flag'=>$flag))->limit(1)->done();
		return $infos['name'] ? $infos['name'] : '';
	}
	public function ID2Name($id){		$infos = dbc(DBCMax)->select('catalog')->in('name')->where(array('id'=>$id))->limit(1)->done();
		return $infos['name'] ? $infos['name'] : '';
	}
	
	public function GetList($parent = 0,$sortstr = 0)
	{
		$catalogOBJ = dbc(DBCMax)->select('catalog')->where('parent='.$parent)->order('`order`.asc');
		if ($sortstr == 2 && $parent == 0) {
			$return = $catalogOBJ->limit(8)->done();
		}elseif ($sortstr > 2 && $parent > 0) {
			$return = $catalogOBJ->limit($sortstr)->done();
		}else{
			$return = $catalogOBJ->done();
		}
		if($sortstr == 1 && $return){
			$catalog = array('all'=>array('name'=>'全部','flag'=>NULL));
			foreach($return as $val){
				$catalog[$val['flag']] = $val;
			}
			return $catalog;
		}else{
			return $return;
		}
	}
	
	public function Search($where, $limit = 1)
	{
		$dbo = dbc(DBCMax)->select('catalog')->where($where);
		$limit && $dbo->limit($limit);
		return $dbo->done();
	}
	private function Get_nav_css($catalog){
		$topclass = NULL;
		$subclass = NULL;
		$top_cata = $this->Search(array('flag'=>$catalog));
		if($top_cata){
			if($top_cata['parent'] > 0){
				$down_cata = $this->Search(array('id'=>$top_cata['parent']));
				$topclass = $down_cata['flag'];
				$subclass = $catalog;
			}else{
				$topclass = $catalog;
			}
		}
		return array($topclass,$subclass);
	}
	
	public function Filter($catalog, $flag = 'product')
	{
		if($catalog == 'main') return '1';
		list($topclass, $subclass) = $this->Get_nav_css($catalog);
		$prefix = ('product' == $flag ? 'p.' : ('seller' == $flag ? 's.' : ''));
				if ($topclass && $subclass)
		{
			return $this->Filter_subClass($subclass, $prefix);
		}
		elseif ($topclass)
		{
			return $this->Filter_topClass($topclass, $prefix);
		}
		else
		{
			return '1';
		}
	}
	
	private function Filter_subClass($classFlag, $prefix = '')
	{
		$subClass = $this->Search(array('flag'=>$classFlag));
		if ($subClass['id'] > 0)
		{
			$this->urlTopClass = $subClass['parent'];
			return $prefix . 'category = '.$subClass['id'];
		}
		else
		{
			return '0';
		}
	}
	
	private function Filter_topClass($classFlag, $prefix = '')
	{
		$topClass = $this->Search(array('flag'=>$classFlag));
		$this->urlTopClass = $topClassID = $topClass['id'];
				$subClasses = (array)$this->Search(array('parent'=>$topClassID), 0);
		$sIDS = $topClassID > 0 ? $topClassID.',' : '';
		foreach ($subClasses as $i => $subClass)
		{
			$sIDS .= $subClass['id'].',';
		}
		$sIDS = substr($sIDS, 0, -1);
		if ($sIDS)
		{
			return $prefix . 'category IN('.$sIDS.')';
		}
		else
		{
			return '0';
		}
	}
	
	public function Add($parent, $flag, $name)
	{
				$checked = $this->Search(array('flag'=>$flag));
		if ($checked)
		{
			return -1;
		}
		return dbc(DBCMax)->insert('catalog')->data(array(
			'parent' => $parent,
			'name' => $name,
			'flag' => $flag,
			'procount' => 0,
			'upstime' => time()
		))->done();
	}
	
	private function Delete_where($where)
	{
		return dbc(DBCMax)->delete('catalog')->where($where)->done();
	}
	
	public function Delete($id)
	{
				$catalog = $this->Search('id='.$id);
		if (!$catalog) return false;
		$master = false;
		if ($catalog['parent'] == 0)
		{
			$master = true;
		}
		$pro_where = 'category = '.$id;
				$this->Delete_where('id='.$id);
				if ($master)
		{
			$sublist = $this->GetList($id);
			$this->Delete_where('parent='.$id);
			if ($sublist)
			{
				$pro_where = 'category IN(';
				foreach ($sublist as $i => $one)
				{
					$pro_where .= $one['id'].',';
				}
				$pro_where = substr($pro_where, 0, -1).')';
			}
		}
				dbc()->Query('UPDATE '.table('product').' SET category=0 WHERE '.$pro_where);
		return true;
	}
	
	public function ProUpdate(&$data = false)
	{
		if ($data)
		{
			$cid_old = post('__catalog_subclass_old', 'int');
			$cid_new = post('__catalog_subclass', 'int') > 0 ? post('__catalog_subclass', 'int') : post('__catalog_topclass', 'int');
			if ($cid_old == $cid_new) return;
			$data['category'] = $cid_new;
		}
				fcache($this->cacheKEY, 0);
	}

	public function hot_save($id, $data) {
		$ret = false;
		$id = (int) $id;
		if($id > 0) {
			$data['hot'] = $data['hot'] ? 1 : 0;
			$data['hotorder'] = (int) $data['hotorder'];
			$ret = dbc(DBCMax)->update('catalog')->data($data)->where(array('id'=>$id))->done();

			fcache('catalog/hot', 0);
		}
		return $ret;
	}
	public function hot($limit = 0)
	{
		$ckey = 'catalog/hot';
		if(false === ($class = fcache($ckey, 900))) {
			$class = dbc(DBCMax)->query("select * from " . table('catalog') . " where `hot`='1' order by `hotorder` ASC, `order` ASC")->done();
			fcache($ckey, $class);
		}
		if (!$class) return array();
				$icon_data = ini('catalog.icon');
		$count = 0;
		$nclass = array();
		foreach ($class as $i => $topclass)
		{
			$class[$i]['icon'] 	= $icon_data[$topclass['id']]['icon'];
			$class[$i]['script'] = stripcslashes($icon_data[$topclass['id']]['script']);
			$class[$i]['selected'] = $topclass['flag'] == $topcss ? true : false;
			$class[$i]['url'] = logic('url')->create('catalog', array('code' => $topclass['flag']));

			$nclass[$i] = $class[$i];
			if($limit > 0 && ++$count >= $limit) {
				break;
			}
		}
		return $nclass;
	}

}

?>
