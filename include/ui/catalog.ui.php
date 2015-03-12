<?php

/**
 * 界面支持：分类导航
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package UserInterface
 * @name catalog.ui.php
 * @version 1.0
 */

class CatalogUI
{
    
    public function display($meituannav = 0)
    {
        if (!logic('catalog')->Enabled()) return;
                $catalog = logic('catalog')->Navigate($meituannav);
                if (logic('catalog')->FilterEnabled())
        {
            foreach ($catalog as $_i => $_topclass)
            {
                if (isset($_topclass['oslcount']))
                {
                    $_topclass['subclass'] || $_topclass['subclass'] = array();
                    $subprocount = 0;
                    foreach ($_topclass['subclass'] as $_ii => $_subclass)
                    {
                        if (isset($_subclass['oslcount']))
                        {
                            if ($_subclass['oslcount'] == 0)
                            {
                                unset($_topclass['subclass'][$_ii]);
                                continue;
                            }
                            $subprocount += $_subclass['oslcount'];
                        }
                    }
                    if ($subprocount == 0)
                    {
                        unset($catalog[$_i]);
                    }
                    else
                    {
                        $catalog[$_i] = $_topclass;
                    }
                }
            }
        }
        include handler('template')->file('@html/catalog/navigate');
    }
    public function seller_display()
    {
        if (!logic('catalog')->Enabled()) return;
                $catalog = logic('catalog')->seller_navigate();

        include handler('template')->file('@html/catalog/navigate');
    }
    public function hot_display()
    {
    	if (!logic('catalog')->Enabled()) return ;
    	                $catalog = logic('catalog')->hot();
        if(empty($catalog)) return ;

        include handler('template')->file('@html/catalog/hot_navigate');
    }
    public function inputer($category)
    {
        $category || $category = 0;
        $category && $master = logic('catalog')->GetOne($category);
        $catalog = logic('catalog')->Navigate(2);
        include handler('template')->file('@html/catalog/inputer');
    }
    public function tree($category)
	{
		$category || $category = 1;
		$treeList = array();
		while( $category > 0){
			$catalog = logic('catalog')->GetOne($category);
			$arr = array(
				'title' => $catalog['name'],
				'url' => logic('url')->create('catalog', array('code' => $catalog['flag'])),
			);
			$treeList[] = '<a href="'. $arr['url'] .'">'. $arr['title'] .'</a>';
			$category = $catalog['parent'];
		}
		krsort($treeList);
		echo implode( ' &gt;&gt; ', $treeList );
	}
}
?>