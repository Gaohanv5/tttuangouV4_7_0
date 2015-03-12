<?php

/**
 * 模块：文件上传
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package module
 * @name upload.mod.php
 * @version 1.0
 */

class ModuleObject extends MasterObject
{

    function ModuleObject( $config )
    {
        $this->MasterObject($config);
                $rtype = user()->get('role_type');
        $rtype || $rtype = 'normal';
        $artypes = explode(',', ini('upload.role'));
        if (false === array_search($rtype, $artypes))
        {
            $msg = 'Access Deined';
			if ($this->Code == 'image')
			{
				$ops = array(
					'status' => 'fails',
					'msg' => $msg
				);
			}
			elseif ($this->Code == 'editor')
			{
				$ops = array(
					'error' => 1,
					'message' => $msg
				);
			}
			else
			{
				exit($msg);
			}
			exit(jsonEncode($ops));
        }
        $runCode = Load::moduleCode($this);
        $this->$runCode();
    }
    function Main()
    {
        exit('IO.Uploads.Index');
    }
    function Image()
    {
                        $result = logic('upload')->Save('Filedata', false, false);
        if (isset($result['error']) && $result['error'])
        {
            $ops = array(
                'status' => 'fails',
                'msg' => $result['msg']
            );
        }
        else
        {
            $ops = array(
                'status' => 'ok',
                'file' => $result
            );
        }
        exit(jsonEncode($ops));
    }
    function Editor()
    {
        $field = get('field', 'txt');
        $result = logic('upload')->Save($field, false, true);
        if (isset($result['error']) && $result['error'])
        {
            $ops = array(
                'error' => 1,
                'message' => $result['msg']
            );
        }
        else
        {
            $ops = array(
                'error' => 0,
                'url' => $result['url']
            );
        }
        exit(jsonEncode($ops));
    }
    function Iframe()
    {
    	$field = get('field', 'txt');
        $result = logic('upload')->Save($field, false, false);
        if (isset($result['error']) && $result['error'])
        {
            $ops = array(
                'status' => 'fails',
                'msg' => $result['msg']
            );
        }
        else
        {
            $ops = array(
                'status' => 'ok',
                'file' => $result
            );
        }
        exit('<script type="text/javascript">window.parent.ups_Result('.jsonEncode($ops).');</script>');
    }
    function UI()
    {
    	$driver = get('driver', 'txt');
    	include handler('template')->file('@html/uploader/image_ui_'.$driver);
    }
}

?>