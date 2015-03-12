<?php

/**
 * 驱动：通知方式
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package driver
 * @name nofity.drv.php
 * @version 1.0
 */

class NotifyDriver
{
    private $name = '';
    
    public function load($name, $conf)
    {
        $this->name = $name;
        $file = dirname(__FILE__).'/notify/'.$name.'.php';
	    include_once $file;
	    $className = $name.'NotifyDriver';
	    return new $className($conf);
    }
    
    public function FlagParser($flag, $data, &$msg)
    {
        if (!is_array($data))
        {
                        $msg = preg_replace('/{\*}/i', $data, $msg);
            return;
        }
                $flags = $this->__GetMsgFlags($flag, $msg, $data);
        foreach ($flags as $name)
        {
        	$val = $data[$name];
        	if (strstr($name, '.'))
        	{
        		        		$keys = explode('.', $name);
        		$final = $data;
        		foreach ($keys as $i => $key)
        		{
        			if (isset($final[$key]))
        			{
        				$final = $final[$key];
        			}
        		}
        		$val = $final;
        	}
        	$msg = preg_replace('/{'.$name.'}/i', $val, $msg);
        }
    }
    
    private function __GetMsgFlags($flag, $msg, $data)
    {
        $fid = 'notify.msg.'.$flag;
        $flags = fcache($fid, dfTimer('com.notify.mf.cache'));
        if ($flags)
        {
            return $flags;
        }
        preg_match_all('/{([a-z0-9\._]+)}/i', $msg, $matchs);
        $flags = array();
        foreach ($matchs[1] as $key)
        {
            $flags[] = $key;
        }
        fcache($fid, $flags);
        return $flags;
    }
}

?>