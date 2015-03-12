<?php

/**
 * 多语言管理引擎
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package base
 * @name i18n.php
 * @version 1.0
 */

$__i18n = array();
$__lang = '';
require_once dirname(__FILE__) . '/i18n/mo.php';

function i18n_init( $lang )
{
    global $__lang;
    $__lang = $lang;
}
function i18n_unload( $lang )
{
    global $__i18n;
    if ( isset($__i18n[$lang]) ) unset($__i18n[$lang]);
}


function __( $text )
{
    global $__lang;
    return __i18n_translate($text, $__lang);
}

function __i18n_translate( $text, $lang )
{
    $translations = &__i18n_get_translations_interface($lang);
    return $translations->translate($text);
}

function &__i18n_get_translations_interface( $lang )
{
    global $__i18n;
    if ( ! isset($__i18n[$lang]) )
    {
        $__i18n[$lang] = new NOOP_Translations();
                $mofile = ROOT_PATH . 'languages/' . $lang . '.mo';
        __i18n_load_db_mo($lang, $mofile);
    }
    return $__i18n[$lang];
}

function __i18n_load_db_mo( $lang, $mofile )
{
    global $__i18n;
    if ( ! is_readable($mofile) ) return false;
    $mo = new MO();
    if ( ! $mo->import_from_file($mofile) ) return false;
    if ( isset($__i18n[$lang]) )
    {
        $mo->merge_with($__i18n[$lang]);
    }
    $__i18n[$lang] = &$mo;
    return true;
}