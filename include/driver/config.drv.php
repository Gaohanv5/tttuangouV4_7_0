<?php

/**
 * Driver: Config IO
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package driver
 * @name config.drv.php
 * @version 1.0
 */

class ConfigDriver
{

	private $__conf_loaded = array();

	private $__real_write = array();

	private $__array_key = '@';

	private $__special_file = '@';

	private $__flag_delete = '';

	public function __construct()
	{
		$this->__flag_delete = microtime();
	}

	public function read( $locate )
	{
		if ( $locate == '' )
		return null;
		$locs = explode('.', $locate);
		$file = $locs[0];
		if ( ! array_key_exists($file, $this->__conf_loaded) )
		{
			$this->__load_conf($file);
		}
		$return = $this->__conf_loaded[$file];
		$loops = count($locs);
		for ( $_i = 1; $_i < $loops; $_i ++ )
		{
			if ( array_key_exists($locs[$_i], $return) )
			{
				$return = $return[$locs[$_i]];
			}
			else
			{
				return false;
			}
		}
		return $return;
	}

	public function write( $locate, $value )
	{
		if ( $locate == '' )
		return null;
		$locs = explode('.', $locate);
		$file = $locs[0];
		if ( ! array_key_exists($file, $this->__conf_loaded) )
		{
			$this->__load_conf($file);
		}
		$current = &$this->__conf_loaded;
		$loops = count($locs);
		for ( $_i = 0; $_i < $loops; $_i ++ )
		{
			if ( ! array_key_exists($locs[$_i], $current) )
			{
				if ( $locs[$_i] != $this->__array_key )
				{
					$current[$locs[$_i]] = array();
					$this->__real_write[$file] = true;
				}
			}
			if ( $_i == ($loops - 1) )
			{
				if ( $locs[$_i] == $this->__array_key )
				{
					$current[] = $value;
				}
				else
				{
					if ($value === $this->__flag_delete)
					{
						unset($current[$locs[$_i]]);
					}
					else
					{
						$current[$locs[$_i]] = $value;
					}
				}
				$this->__real_write[$file] = true;
			}
			if ( $locs[$_i] != $this->__array_key )
			{
				if (isset($current[$locs[$_i]]))
				{
					$current = &$current[$locs[$_i]];
				}
			}
		}
	}

	public function delete($locate)
	{
		$this->write($locate, $this->__flag_delete);
	}

	public function close()
	{
		$writes = array_keys($this->__real_write);
		$loops = count($writes);
		for ( $_i = 0; $_i < $loops; $_i ++ )
		{
			$this->__write_file($writes[$_i], $this->__write_parse_fast($this->read($writes[$_i])));
		}
		$this->__real_write = array();
	}
	private function __write_parse_fast( $input )
	{
		return var_export($input, true);
	}
	private function __write_parse( $input )
	{
		if ( is_string($input) )
		{
			if ( is_bool(strpos($input, "\n")) )
			{
				return "'{$input}'";
			}
			else
			{
				$input = str_replace("\n", '\\n', $input);
				return '"' . $input . '"';
			}
		}
		elseif ( is_numeric($input) )
		{
			return "{$input}";
		}
		elseif ( is_bool($input) )
		{
			if ( true == $input )
			{
				return "True";
			}
			else
			{
				return "False";
			}
		}
		elseif ( is_array($input) )
		{
			$array_keys = array_keys($input);
			$loops = count($array_keys);
			if ( is_string($array_keys[0]) )
			{
				$return = "Array("."\n";
				for ( $_i = 0; $_i < $loops; $_i ++ )
				{
					if ( isset($input[$array_keys[$_i]]) )
					{
						$return .= "'{$array_keys[$_i]}'=>" .
						self::__write_parse($input[$array_keys[$_i]]) . ","."\n";
					}
				}
				$return .= ")"."\n";
			}
			else
			{
				$loops = count($input);
				$return = "Array("."\n";
				for ( $_i = 0; $_i < $loops; $_i ++ )
				{
					if ( isset($input[$_i]) )
					{
						$return .= self::__write_parse($input[$_i]) . ","."\n";
					}
				}
				$return .= ")"."\n";
			}
			return $return;
		}
	}
	private function __write_file( $file, $content )
	{
		if ( substr($file, 0, 1) == $this->__special_file )
		{
			$write = CONFIG_PATH . substr($file, 1) . '.php';
		}
		else
		{
			$write = CONFIG_PATH . $file . '.php';
		}
		file_put_contents($write,
        '<?php'."\n".
        ''."\n".
        '$config["' . $file . '"] =  ' . $content . ';'."\n".
        '?>');
	}
	private function __load_conf( $file )
	{
		if ( substr($file, 0, 1) == $this->__special_file )
		{
			$include = CONFIG_PATH . substr($file, 1) . '.php';
		}
		else
		{
			$include = CONFIG_PATH . $file . '.php';
		}
		$config[$file] = array();
		if ( file_exists($include) )
		{
			include $include;
		}

		$this->__conf_loaded[$file] = $config[$file];
	}
}
?>