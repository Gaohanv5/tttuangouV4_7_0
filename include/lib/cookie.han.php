<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name cookie.han.php
 * @date 2014-12-11 14:44:49
 */
 




class CookieHandler
{

   
	var $_config;

   
	var $_cookie;

   
	var $_prefix;

   
	var $_path;

   
	var $_domain;

	function CookieHandler(& $config=null, & $cookie=null)
	{
	    if (is_null($config))
	    {
	        $config = ini('settings');
	    }
		if (is_null($cookie))
		{
		    $cookie = &$_COOKIE;
		}
		$this->_config =& $config;
		$this->_cookie =& $cookie;

		$this->_prefix = $this->_config['cookie_prefix'];
		$this->_path   = $this->_config['cookie_path']   ? $this->_config['cookie_path']   : '/';
		$this->_domain = $this->_config['cookie_domain'] ? $this->_config['cookie_domain'] : '';
	}

	function SetVar($name, $value, $time = false)
	{
		$expire = 0;

        if($time)
        {
            $expire = time() + $time;
        }
		@setcookie($this->_prefix . $name, $value, $expire, $this->_path, $this->_domain);
		return true;
	}

	
	function SetVarPush($name,$value,$time = FALSE)
	{
	    $expire = 0;
	    
	    if($time)
	    {
	        $expire = time() + $time;
	    }
	    $tmp = $this->GetVar($name);
	    if($tmp !== null)
	    {
	        $aTmp  = explode('|', $tmp);
	        $aTmp[] = $value;
	        $aTmp  = array_unique($aTmp);
	        $value = implode('|', $aTmp);
	    }
	    @setcookie($this->_prefix . $name, $value, $expire, $this->_path, $this->_domain);
	    return true;
	}
	
	function GetVar($key)
	{
	    		if(isset($_POST[$this->_prefix . $key]))
        {
			return base64_decode(post($this->_prefix . $key, 'chars'));
        }
        else {
            if(isset($this->_cookie[$this->_prefix . $key]))
            {
                return rawurldecode($this->_cookie[$this->_prefix . $key]);
            }
			return false;
        }
	}

	function DeleteVar($name)
	{
		$name_list=func_get_args();
		foreach ($name_list as $name)
		{
			$this->SetVar($name,'',-86400000);
		}
	}

	
	function DeleteVarAnyPop($name,$value)
	{
	    $tmp = $this->GetVar($name);
	    
	    $tmpArray = explode('|', $tmp);
	    
	    
	    
	    $array = array();
	    foreach($tmpArray as $k=>$v) {
	    	if($v != $value) {
	    		$array[$k] = $v;
	    	}
	    }
	    $array = array_unique($array);

	    $s = implode("|", $array);
	    return $this->SetVar($name, $s);
	}
	
	function ClearAll()
	{
		$prefix_len=strlen($this->_prefix);
		foreach ((array)$this->_cookie as $name=>$value)
		{
			$name=substr($name,$prefix_len);
			if ($name != '')
			{
			    $this->SetVar($name, '', -86400000);
			}
		}
	}

}

?>