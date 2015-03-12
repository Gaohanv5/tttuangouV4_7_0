<?php

/**
 * 界面支持：前端界面风格
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package UserInterface
 * @name style.ui.php
 * @version 1.0
 */

class StyleControllerUI
{
    
    public function allowMulti()
    {
        $disable = ini('styles.multi_support');
        return $disable ? false : true;
    }
    
    public function loadSwUI()
    {
        $shtmls = array();
        $styles = ini('styles.local');
        foreach ($styles as $id => $data)
        {
            if ($data['enabled'])
            {
                $shtmls[] = '<a href="'.rewrite('?mod=style&code=load&id='.$id).'"><img src="'.rewrite('templates/themes/'.$id.'/icon.png').'" class="themes" title="'.$data['name'].'" /></a>';
            }
        }
        return implode('', $shtmls);
    }
    
    public function setCSS($id)
    {
        handler('cookie')->SetVar('stylecssid', $id);
    }
    
    public function loadCSS()
    {
        $styleid = '';
        if ($this->allowMulti())
        {
            $styleid = handler('cookie')->GetVar('stylecssid');
        }
        $styleid || $styleid = ini('styles.default');
                return '<link href="'.rewrite('templates/themes/'.$styleid.'/css/style.css').'" rel="stylesheet" type="text/css" />';
    }
    
    public function get_all()
    {
        $tpl_root = handler('template')->TemplateRootPath.'themes/';
        $styles_io = handler('io')->ReadDir($tpl_root);
        $styles_lc = ini('styles.local');
        $styles_lc || $styles_lc = array();
        $styles_ms = $styles_lc;
        foreach ($styles_io as $i => $style_name)
        {
            $style_name = str_replace($tpl_root, '', $style_name);
            if (isset($styles_lc[$style_name]))
            {
                unset($styles_ms[$style_name]);
            }
            else
            {
                                $styles_lc[$style_name] = array(
                    'name' => '新增皮肤',
                    'enabled' => false
                );
            }
        }
        foreach ($styles_ms as $style_name => $style_data)
        {
            $styles_lc[$style_name] = array(
                'name' => $style_data['name'].'[已失效]',
                'enabled' => false
            );
        }
        return $styles_lc;
    }
}

?>