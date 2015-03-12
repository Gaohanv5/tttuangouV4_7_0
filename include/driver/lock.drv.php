<?php

/**
 * Driver: Static Locker
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package driver
 * @name lock.drv.php
 * @version 1.0
 */

class LockDriver
{
	
	private $dir = null;
	
	private $path = array();
	
	public function __construct()
	{
		$this->config(CACHE_PATH.'locks/');
	}
	
	public function config( $dir )
	{
		$this->dir = $dir;
	}
	
	public function file( $name )
	{
		return $this->pathed($name);
	}
	
	public function islocked( $name )
	{
		$file = $this->pathed($name);
		$c = (is_file($file)) ? file_get_contents($file) : '';
		return ($c == 'locked') ? true : false;
	}

	
	public function locks( $name, $lock )
	{
		$file = $this->pathed($name);
		if ($lock === true)
		{
			$result = file_put_contents($file, 'locked');
		}
		elseif ($lock === false)
		{
			$result = (is_file($file)) ? unlink($file) : false;
		}
		return $result;
	}

	
	private function pathed($name)
	{
		if (!isset($this->path[$name]))
		{
			if ( !is_dir($this->dir) )
			{
				tmkdir($this->dir);
			}
			$this->path[$name] = $this->dir . $this->mixd($name) . '.lock';
		}
		return $this->path[$name];
	}

	
	private function mixd( $string )
	{
		return $string;
	}
}
?>
