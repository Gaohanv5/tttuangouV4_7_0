<?php
/**
 *
 * 通知方式：邮件
 *
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package notify
 * @name mail.php
 * @version 1.0
 */

class mailNotifyDriver extends NotifyDriver
{
    private $conf = array();
    function __construct($conf)
    {
        $this->conf = $conf;
    }
    
    public function __default($class, $uid, $data)
    {
        if (!is_numeric($uid)) return;
        $email = user($uid)->get('email');
        if (!preg_match('/[a-z0-9\._]+@[a-z0-9\.-]+/', $email)) return;
        $msg = ini('notify.event.'.$class.'.msg.mail');
        if (!$msg) return false;

        $this->FlagParser($class.'.mail', $data, $msg);
        logic('push')->add('mail', $email, array(
            'subject' => ini('settings.site_name').' 提示您',
            'content' => $msg
        ), 6);
        return 'QUEUE RECEIVED';
    }
}

?>