<?php

/**
 * 模块：订阅区
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package module
 * @name subscribe.mod.php
 * @version 1.0
 */

class ModuleObject extends MasterObject
{

    function ModuleObject( $config )
    {
        $this->MasterObject($config);
        $runCode = Load::moduleCode($this);
        $this->$runCode();
    }
    function Main()
    {
        $this->Mail();
    }
    function Mail()
    {
        $target = get('target', 'txt');
        $this->Title = __('邮件订阅');
        include handler('template')->file('subscribe_mail');
    }
    function SMS()
    {
        $target = get('target', 'txt');
        $this->Title = __('短信订阅');
        include handler('template')->file('subscribe_sms');
    }
    function Save()
    {
        $type = post('type', 'txt');
        $target = post('target', 'txt');
        if ($type == 'mail')
        {
            if (!preg_match('/[a-z0-9\._]+@[a-z0-9\.-]+/', $target))
            {
                $this->Messager(__('无效的Email地址！'));
            }
        }
        elseif ($type == 'sms')
        {
            if (!preg_match('/[0-9]{11}/', $target))
            {
                $this->Messager(__('无效的手机号码！'));
            }
        }
        $city = post('city', 'int');
        $result = logic('subscribe')->Search('target', $target);
        if ($result)
        {
            if ($result['validated'] == 'true')
            {
                $this->Messager(__('您已经订阅过了，请不要重复提交哦！'));
            }
            $sid = $result['id'];
        }
        else
        {
            $sid = logic('subscribe')->Add($city, $type, $target);
        }
        if (ini('subscribe.validate.do.'.$type))
        {
            $this->Validate_resend($sid);
            header('Location: '.rewrite('?mod=subscribe&code=validate&sid='.$sid));
        }
        else
        {
            $this->Validate_verify('do', $sid);
        }
    }
    public function Undo()
    {
        $target = get('target', 'txt');
        $this->Title = __('取消订阅');
        include handler('template')->file('subscribe_undo');
    }
    public function Undo_confirm()
    {
        $target = post('target', 'txt');
        $sid = logic('subscribe')->Subsd($target);
        if (!$sid)
        {
            $this->Messager(__('您还没有进行订阅，无法取消！'));
        }
                $sub = logic('subscribe')->GetOne($sid);
        $type = $sub['type'];
        if (ini('subscribe.validate.undo.'.$type))
        {
            $this->Validate_resend($sid, 'undo');
            header('Location: '.rewrite('?mod=subscribe&code=validate&sid='.$sid.'&action=undo'));
        }
        else
        {
            $this->Validate_verify('undo', $sid);
        }
    }
    public function Validate()
    {
        $sid = get('sid', 'int');
        $sub = logic('subscribe')->GetOne($sid);
        $this->Title = __('订阅验证');
        $action = get('action', 'txt');
        if ($action && $action == 'undo')
        {
            $this->Title = __('取消订阅');
        }
        else
        {
            $action = 'dosub';
        }
        include handler('template')->file('subscribe_'.$sub['type'].'_validate');
    }
    public function Validate_resend($csid = null, $action = 'dosub')
    {
        $sid = $csid ? $csid : get('sid', 'int');
        if (get('action')) $action = get('action', 'txt');
                $lastSend = meta('sub_last_send_of_'.$sid);
        if ($lastSend)
        {
            if (time() - $lastSend < 120)
            {
                if (is_null($csid))
                {
                    $this->Messager(__('系统已经发送过验证信息，如需重新发送，请间隔两分钟再试！'));
                }
                return;
            }
        }
        $sub = logic('subscribe')->GetOne($sid);
        $vcode = $this->__vcode_generate();
        $send = handler('template')->content('@html/subscribe/'.$action.'.validate.'.$sub['type'], array('vcode'=>$vcode));
        if ($sub['type'] == 'sms')
        {
            logic('push')->addi('sms', $sub['target'], array('content'=>$send));
        }
        else
        {
            $subject = __('您在申请订阅，请验证！');
            if ($action == 'undo')
            {
                $subject = __('您在申请取消订阅，请确认！');
            }
            logic('push')->add($sub['type'], $sub['target'], array('subject'=>$subject,'content'=>$send));
        }
        meta('sub_vcode_'.$vcode, $sid, 'd:1');
        meta('sub_last_send_of_'.$sid, time(), 'm:2');
        if (is_null($csid))
        {
            $this->Messager(__('发送成功！'), '?mod=subscribe&code=validate&sid='.$sid.'&action='.$action);
        }
        return;
    }
    public function Validate_verify($action = null, $sid = null)
    {
        if (is_null($action) && is_null($sid))
        {
            $vcode = get('vcode', 'txt');
            $vcode = $vcode ? $vcode : post('vcode', 'txt');
            $vcode = trim($vcode);
            $action = get('action', 'txt');
            $action = $action ? $action : post('action', 'txt');
            $sid = meta('sub_vcode_'.$vcode);
            if (!$sid)
            {
                $this->Messager(__('无效的验证码！'));
            }
            meta('sub_vcode_'.$vcode, null);
            
                    }
                if ($action == 'undo')
        {
            logic('subscribe')->Validate($sid, 'false');
            $this->Messager(__('已经取消订阅！'), '?mod=me&code=setting');
        }
        else
        {
            logic('subscribe')->Validate($sid);
            $this->Messager(__('已经成功订阅！'), '?mod=me&code=setting');
        }
    }
    private function __vcode_generate()
    {
        $string = md5(time());
        return substr($string, 12, 6);
    }
}

?>