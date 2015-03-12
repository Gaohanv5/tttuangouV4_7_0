<?php

/**
 * 逻辑区：事件通知
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package logic
 * @name notify.logic.php
 * @version 1.0
 */

class NotifyLogic
{
    private $notifys = array();
    private $listener = false;
    
    public function __construct()
    {
        if (ini('notify.listener'))
        {
            $this->listener = true;
        }
        foreach ( ini('notify.api') as $type => $conf )
        {
            if ( $conf['enabled'] )
            {
                $this->notifys[$type] = $conf;
            }
        }
    }
    
    public function html( $product )
    {
        
        $inputers = array(
            'sms' => true
        );
        foreach ($this->notifys as $type => $conf)
        {
            if (isset($inputers[$type]) && $inputers[$type])
            {
                include handler('template')->file('@html/'.$type.'_inputer');
            }
        }
    }
    
    public function Accessed($class, &$data)
    {
        if ($class == 'order.save')
        {
            
        }
    }
    
    private function __GetStruct($mixed, $parent = '')
    {
        if (!is_array($mixed))
        {
            return '*';
        }
        $return = '';
        foreach ($mixed as $key => $val)
        {
            if (is_array($val))
            {
                $return .= $this->__GetStruct($val, $parent.$key.'.');
            }
            else
            {
                $return .= $parent.$key.',';
            }
        }
        return $return;
    }
    
    private function __GetAPI($type)
    {
        $SID = 'notify.driver.api.'.$type;
        $api = moSpace($SID);
        if (!$api)
        {
            $api = driver('notify')->load($type, $this->notifys[$type]);
            moSpace($SID, $api);
        }
        return $api;
    }
    
    public function Clears()
    {
        foreach (ini('notify.event') as $name => $val)
        {
            foreach ($this->notifys as $type => $conf)
            {
                fcache('notify.msg.'.$name.'.'.$type, 0);
            }
        }
    }
    
    public function Call($uid, $class, $data = array())
    {
        $method = str_replace('.', '_', $class);
        if ($this->listener)
        {
            $pox = 'notify.event.'.$method;
            if (!ini($pox))
            {
                ini($pox, INI_DELETE);
            }
            if (!ini($pox.'.struct'))
            {
                ini($pox.'.struct', $this->__GetStruct($data));
            }
        }
		$notifyType = false;
				if ($method == 'logic_coupon_Create')
		{
			$notifyType = meta('p_nt_'.$data['productid']);
		}
        foreach ($this->notifys as $type => $conf)
        {
            if (!ini('notify.event.'.$method.'.hook.'.$type.'.enabled'))
            {
                continue;
            }
						if ($notifyType && $type != $notifyType)
			{
				continue;
			}
            $al2user = ini('notify.event.'.$method.'.cfg.'.$type.'.al2user');
            $cc2admin = ini('notify.event.'.$method.'.cfg.'.$type.'.cc2admin');
            $api = $this->__GetAPI($type);
            $this->__API_Call($api, $method, $uid, $data, $al2user, $cc2admin);
        }
    }
    
    private function __API_Call($api, $method, $uid, $data, $al2user = true, $cc2admin = false)
    {
        $adminid = ini('notify.adminid');
        if (method_exists($api, $method))
        {
            if ($al2user)
            {
                $api->$method($uid, $data);
            }
            if ($cc2admin)
            {
                                DEBUG && $data['content'] .= __(' (抄送)');
                $api->$method($adminid, $data);
            }
        }
        elseif (method_exists($api, '__default'))
        {
            if ($al2user)
            {
                $api->__default($method, $uid, $data);
            }
            if ($cc2admin)
            {
                                DEBUG && $data['content'] .= __(' (抄送)');
                $api->__default($method, $adminid, $data);
            }
        }
    }
}
?>