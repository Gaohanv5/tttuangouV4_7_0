<?php

/**
 * 通知方式：QQ机器人
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package notify
 * @name qqrobot.php
 * @version 1.0
 */

class qqrobotNotifyDriver extends NotifyDriver
{
    private $api = null;
    function __construct($conf)
    {
        require_once dirname(__FILE__).'/qqrobot.api.php';
        $this->api = qqrobot_api_driver::getInstance();
        $this->api->config($conf);
    }
    
    public function __default($class, $uid, $data)
    {
        return '';
        if (!is_numeric($uid)) return;
        $qq = user($uid)->get('qq');
        if (!preg_match('/[0-9]{5,11}/', $qq)) return;
        $msg = ini('notify.event.'.$class.'.msg.qqrobot');
        if (!$msg) return false;

        $this->FlagParser($class.'.qqrobot', $data, $msg);
        return $this->SendMsg($qq, $msg);
    }
    
    private function SendMsg($qq, $msg)
    {
        $result = $this->api->command('buddy.send', array(
            'uid' => $qq,
            'message' => $msg
        ));
        logic('push')->log('qqrobot', 'xiaoc', $qq, array('content'=>$msg), $result);
        return $result;
    }
}

?>