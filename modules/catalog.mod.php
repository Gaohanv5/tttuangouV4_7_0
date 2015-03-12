<?php

/**
 * 模块：产品分类展示
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package module
 * @name catalog.mod.php
 * @version 1.0
 */

class ModuleObject extends MasterObject
{
    public $Title = '';
    function ModuleObject( $config )
    {
        $this->MasterObject($config);
                $runCode = Load::moduleCode($this, false, false);
        $this->Sort($runCode);
    }
    private function Sort($catalog)
    {
		$data = logic('product')->display(logic('catalog')->Filter($catalog, 'product'));
                if (!$data)
        {
            $data = array('product'=>array(),'mutiView'=>true);
        }
        else
        {
                        $data['mutiView'] = true;
            $product = (isset($data['product']['id']) && $data['product']['id']>0) ? array($data['product']) : $data['product'];
        }
		$cataname = logic('catalog')->Flag2Name($catalog);
        $this->Title = $data['mutiView'] ? $cataname : $product['name'];
                include handler('template')->file('home');
    }
}

?>
