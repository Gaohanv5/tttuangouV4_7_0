<?php

/**
 * 界面支持：广告展示
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package UserInterface
 * @name ad.ui.php
 * @version 1.0
 */

class CartDisplayerUI
{
    
    public function load($block)
    {
        $masterFile = handler('template')->TemplateRootPath.'html/cart/'.$block.'.html';
        if (!is_file($masterFile)) return;
        $cartItems = cartItems();
        
        include handler('template')->file('@html/cart/'.$block);
    }
}

?>