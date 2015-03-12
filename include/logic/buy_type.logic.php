<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name buy_type.logic.php
 * @date 2014-12-11 14:44:49
 */
 

interface template
{
    public function address($data);
}

class BuyCheckoutM implements template
{
     
    public function address($data)
    {
        if(is_array(array_pop($data)) === FALSE)
            return false;
        $isAddress = 0;
                foreach ($data as $k => $v)
        {
            if($v['type'] == 'stuff')
            {    
                $isAddress = 1;
                break;
            }
            
        }
        return $isAddress == 1 ? true : false;            
    }
    
    public function GetStuff($data)
    {
        if(is_array(current($data)) === FALSE)
            return false;
        $stuff = array();
                foreach ($data as $k => $v)
        {
            if($v['type'] == 'stuff')
            {
                $stuff[$v['id']] = $v['id'];
            }
        
        }
        return $stuff;
    }
}

class BuyCheckS implements template
{
    public function address($data)
    {
        if(is_array(current($data)) === TRUE)
            return false;
        if($data['type'] == 'stuff')
            return true;
        return false;
    }
    
    public function GetStuff($data)
    {
        if(is_array(array_pop($data)) === TRUE)
            return false;
        if($data['type'] == 'stuff')
            return array($data['id']=>$data['id']);
        return false;
    }
}

class Buy_typeLogic
{
    private $_obj = array();
    private $_result = false;
    public function Add($obj)
    {
        $this->_obj[] = $obj;
    }
    
     
    public function CheckStuff($data)
    {
       foreach ($this->_obj as $k => $v)
       {
           if($v->address($data))
           {
               return true;
               break;
           }
       }
       return false; 
    }
    
    
    public function GetStuff($data)
    {
        $Stuff = array();
        foreach ($this->_obj as $k => $v)
        {
            $tmp = $v->GetStuff($data);
            if($tmp)
            {
                $Stuff = $Stuff+$tmp;;
            }
        }
        return $Stuff;
    }
}