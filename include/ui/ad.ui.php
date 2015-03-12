<?php

/**
 * 界面支持：广告展示
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package UserInterface
 * @name ad.ui.php
 * @version 1.0
 */

class AdDisplayerUI
{
    
    public function load($block)
    {
        $masterFile = handler('template')->TemplateRootPath.'html/ad/'.$block.'.html';
        if (!is_file($masterFile)) return;
        if (!ini('ad.'.$block.'.enabled')) return;
        $cfg = ini('ad.'.$block.'.config');
        include handler('template')->file('@html/ad/'.$block);
    }
}

?>