<?php

/**
 * 逻辑区：安全过滤
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package logic
 * @name safe.logic.php
 * @version 1.0
 */

class SafeLogic
{
    private $__vf_driver = null;
    
    function Vars($method, $key, $limit)
    {
        switch ($method)
        {
            case 'POST':
                $var = &$_POST;
                break;
            default:
                $var = &$_GET;
        }
        $method = strtolower($method);
        if ($key == '')
        {
            return $var;
        }
        if ($limit == '')
        {
            $igc = isset($var[$key]) ? $var[$key] : false;
        }
        else
        {
            $igc = $var[$key];
            $loops = explode(';', $limit);
            foreach ($loops as $i => $rule)
            {
                $igc = ($igc !== false) ? $this->__vars_filter($rule, $igc, $method) : false;
            }
        }
        if (ENC_IS_GBK)
        {
            $IS_GET = ($_SERVER['REQUEST_METHOD'] == 'GET');
            if (is_string($igc) && ($IS_GET || X_IS_AJAX))
            {
                $igc = ENC_U2G($igc);
            }
        }
        return $igc;
    }
    private function __vars_filter($rule, $val, $method)
    {
        if ($this->__vf_driver == null)
        {
            $this->__vf_driver = new SafeLogicVarsFilter();
        }
        if (method_exists($this->__vf_driver, $rule))
        {
            if (WEB_BASE_ENV_DFS::$APPNAME != 'admin' && $method == 'get' && in_array($rule, array('string', 'txt')))
            {
                $valc = $val;
                if (strlen($valc) > 32)
                {
                    $safe = dbc(DBCMax)->do_sql_safe_query($valc);
                    if ($safe === true)
                    {
                        if (strstr($valc, '%'))
                        {
                            $safe = dbc(DBCMax)->do_sql_safe_query(urldecode($valc));
                            if ($safe === true)
                            {
                                                            }
                            else
                            {
                                dbc(DBCMax)->sql_hack_found($safe);
                            }
                        }
                    }
                    else
                    {
                        dbc(DBCMax)->sql_hack_found($safe);
                    }
                }
            }
            return $this->__vf_driver->$rule($val);
        }
        return $val;
    }
}


class SafeLogicVarsFilter
{
    function int($val)
    {
        return (int)$val;
    }
    function number($val)
    {
        return is_numeric($val) ? $val : false;
    }
	function string($val)
	{
		return $val;
	}
    function txt($val)
    {
        if ($val != '')
        {
            $charset_loops = array();
            if (ENC_IS_GBK)
            {
                $charset_loops[] = 'GB2312';
                $charset_loops[] = 'ISO-8859-1';
            }
            else
            {
                $charset_loops[] = 'UTF-8';
            }
            foreach ($charset_loops as $charset)
            {
                if ('' != $parsed = @thtmlspecialchars($val, ENT_COMPAT, $charset))
                {
                    return $parsed;
                }
            }
            return $val;
        }
        else
        {
            return $val;
        }
    }
	function chars($val)
	{
		if (preg_match('/^[\w\-\+\/\=]+$/i', $val))
		{
			return $val;
		}
		else
		{
			return false;
		}
	}
    function float($val)
    {
        return (float)$val;
    }
    function trim($val)
    {
    	if (is_array($val))
    	{
    		foreach ($val as $key => $one)
    		{
    			$val[$key] = trim($one);
    		}
    		return $val;
    	}
    	else
    	{
    		return trim($val);
    	}
    }
}

?>