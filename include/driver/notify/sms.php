<?php

/**
 * 通知方式：短信
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package notify
 * @name sms.php
 * @version 1.0
 */

class smsNotifyDriver extends NotifyDriver
{
    private $conf = array();
    function __construct($conf)
    {
        $this->conf = $conf;
    }
    
    public function __default($class, $uid, $data)
    {
        if (!is_numeric($uid)) return;
        $phone = user($uid)->get('phone');
        if (!preg_match('/[0-9]{11}/', $phone)) return;
        $msg = ini('notify.event.'.$class.'.msg.sms');
        if (!$msg) return false;

        $this->FlagParser($class.'.sms', $data, $msg);
        if (ini('service.sms.fastsend'))
        {
                        logic('push')->addi('sms', $phone, array(
                'content' => $msg
            ));
        }
        else
        {
            logic('push')->add('sms', $phone, array(
                'content' => $msg
            ), 9);
        }
        return 'QUEUE RECEIVED';
    }
}

?>