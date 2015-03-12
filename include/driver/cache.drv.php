<?php

/**
 * Driver: Cache IO
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package driver
 * @name cache.drv.php
 * @version 1.1
 */

class CacheDriver
{
	private static $path = '';
	public function path($path)
	{
		self::$path = $path;
		return $this;
	}
	public function read($key, $live)
	{
		$path = self::$path;
		$file = $path.$key.'.cache.php';
		if ( !is_file($file) )
		{
			return false;
		}
		else
		{
			if ($live >= 0)
			{
				if ( time() - filemtime($file) > $live)
				{
					$live = 0;
				}
				if ($live == 0)
				{
					unlink($file);
					return false;
				}
			}
			$cache = array();
			include $file;
			return $cache;
		}
	}
	public function write($key, $value)
	{
		$path = self::$path;
		$file = $path.$key.'.cache.php';
		tmkdir(dirname($file));
		file_put_contents($file,
		'<?php'."\n".
		''."\n".
		'$cache =  ' . var_export($value, true) . ';'."\n".
		'?>');
		return $value;
	}
}

?>