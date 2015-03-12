<?php

/**
 * 模块：Widget管理
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package module
 * @name widget.mod.php
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
        $this->CheckAdminPrivs('widget');
		$list = ini('widget');
        unset($list['~@blocks']);
        include handler('template')->file('@admin/widget_area_list');
    }
    function Config()
    {
        $this->CheckAdminPrivs('widget');
		$area_name = get('flag', 'txt');
        $list = ini('widget.'.$area_name.'.blocks');
        $blocks = ini('widget.~@blocks');
        include handler('template')->file('@admin/widget_area_blocks');
    }
    function Config_sort()
    {
        $this->CheckAdminPrivs('widget');
		$area_name = get('flag', 'txt');
        $list = ini('widget.'.$area_name.'.blocks');
        $blocks = ini('widget.~@blocks');
        include handler('template')->file('@admin/widget_area_blocks_sort');
    }
    function Config_sort_save()
    {
        $this->CheckAdminPrivs('widget','ajax');
		$area_name = get('flag', 'txt');
        $oldList = ini('widget.'.$area_name.'.blocks');
        $newListString = get('list', 'txt');
        $newListArray = explode(',', $newListString);
        $list2W = array();
        foreach ($newListArray as $i => $flag)
        {
            $list2W[$flag] = $oldList[$flag];
            unset($oldList[$flag]);
        }
                $list2W = array_merge($list2W, $oldList);
                ini('widget.'.$area_name.'.blocks', $list2W);
        exit('ok');
    }
    function Config_block_add()
    {
        $this->CheckAdminPrivs('widget','ajax');
		$add_area = get('area', 'txt');
        $add_block = get('block', 'txt');
        $list = ini('widget.'.$add_area.'.blocks');
        if (isset($list[$add_block]))
        {
            exit(__('已经加载了此模块！'));
        }
        $blocks = ini('widget.~@blocks');
        if (!isset($blocks[$add_block]))
        {
            exit(__('不存在此模块，无法添加！'));
        }
        ini('widget.'.$add_area.'.blocks.'.$add_block, array('enabled'=>true));
        exit('ok');
    }
    function Config_block_remove()
    {
        $this->CheckAdminPrivs('widget','ajax');
		$rm_area = get('area', 'txt');
        $rm_block = get('block', 'txt');
        $list = ini('widget.'.$rm_area.'.blocks');
        if (!isset($list[$rm_block]))
        {
            exit(__('不存在此模块，无法删除！'));
        }
        ini('widget.'.$rm_area.'.blocks.'.$rm_block, INI_DELETE);
        exit('ok');
    }
    function Block()
    {
        $this->CheckAdminPrivs('widget');
		$list = ini('widget.~@blocks');
        include handler('template')->file('@admin/widget_block_list');
    }
    function Block_add()
    {
        $this->CheckAdminPrivs('widget');
		$class = get('class', 'txt');
        include handler('template')->file('@admin/widget_add_'.$class);
    }
    function Block_add_save_diy()
    {
        $this->CheckAdminPrivs('widget');
		$flag = post('flag', 'txt');
        $name = post('name', 'txt');
        $title = post('title');
        $content = post('content');
                $content = stripcslashes($content);
        $dir = ROOT_PATH.'templates/widget/';
        $tpl = file_get_contents($dir.'!diy.template.html');
        $write = str_replace(
        	array('{$title}', '{$content}'),
        	array($title, $content),
    	$tpl);
    	ini('widget.~@blocks.'.$flag, array('name' => $name));

        $flag =str_replace('/', '', $flag);
        $flag =str_replace('\\', '', $flag);

        $file = $dir.$flag.'.html';
        file_exists($file) and exit('文件已存在');

    	file_put_contents($dir.$flag.'.html', $write);
    	$this->Messager('模块创建成功！', '?mod=widget&code=block');
    }
    function Block_config()
    {
        $this->CheckAdminPrivs('cserset');
		$flag = get('flag', 'txt');
        $flag =str_replace('/', '', $flag);
        $flag =str_replace('\\', '', $flag);
        $file = ROOT_PATH.'templates/widget/'.$flag.'.config.html';
        if (!is_file($file))
        {
            $this->Messager('此模块不需要配置！');
        }
        include handler('template')->file('@widget/'.$flag.'.config');
    }
    private function Block_config_link($flag)
    {
		$flag =str_replace('/', '', $flag);
        $flag =str_replace('\\', '', $flag);
    	$file = ROOT_PATH.'templates/widget/'.$flag.'.config.html';
        if (!is_file($file))
        {
        	return '<font title="此模块不需要配置">配置</font>';
        }
        else
        {
        	return '<a href="?mod=widget&code=block&op=config&flag='.$flag.'">配置</a>';
        }
    }
    function Block_config_save()
    {
        $this->CheckAdminPrivs('cserset');
		$flag = post('flag', 'txt');
        $flag =str_replace('/', '', $flag);
        $flag =str_replace('\\', '', $flag);
        $data = post('data');
        ini('data.'.$flag, $data);
        $this->Messager('配置已经更新！', '?mod=widget&code=block');
    }
    function Block_editor()
    {
        $this->CheckAdminPrivs('widget');
		$flag = get('flag', 'txt');
        $flag =str_replace('/', '', $flag);
        $flag =str_replace('\\', '', $flag);

        $file = ROOT_PATH.'templates/widget/'.$flag.'.html';
        !file_exists($file) && exit('文件名不正确');

        $content = file_get_contents($file);
        include handler('template')->file('@admin/widget_editor');
    }
    function Block_editor_save()
    {
        $this->CheckAdminPrivs('widget');
		$flag = post('flag', 'txt');
        $flag =str_replace('/', '', $flag);
        $flag =str_replace('\\', '', $flag);

        $file = ROOT_PATH.'templates/widget/'.$flag.'.html';
        !file_exists($file) && exit('文件名不正确');

        $content = post('content');
                $content = stripcslashes($content);
        file_put_contents($file, $content);
                $cfile = handler('template')->file('@widget/'.$flag);

        is_file($cfile) && unlink($cfile);
                $this->Messager('文件更新完成！', '?mod=widget&code=block');
    }
    function Block_delete()
    {
        $this->CheckAdminPrivs('widget');
		$flag = get('flag', 'txt');
                if (false !== ini('widget.~@blocks.'.$flag))
        {
            ini('widget.~@blocks.'.$flag, INI_DELETE);
        }
                $areas = ini('widget');
        foreach ($areas as $name => $val)
        {
        	if ($name == '~@blocks') continue;
        	foreach ($val['blocks'] as $key => $enabled)
        	{
        		if ($key == $flag)
        		{
        			        			ini('widget.'.$name.'.blocks.'.$key, INI_DELETE);
        		}
        	}
        }
        $dir = ROOT_PATH.'templates/widget/';
                $file = $dir.$flag.'.config.html';
        if (is_file($file))
        {
            unlink($file);
        }
                $file = $dir.$flag.'.html';
        if (is_file($file))
        {
            unlink($file);
        }
        $this->Messager('模块已经删除！', '?mod=widget&code=block');
    }
    private function Block_delete_link($flag)
    {
    	$sysm = array('admin_widget_guide','asker','broadcast','cservice','faq_invite','faq_my_bill','faq_my_coupon','faq_my_order','faq_ticket','follow_us','invite','my_account','product_list', 'article_list', 'order_buys','user_nav', 'seller_nav', 'user_cash_nav');
    	if (in_array($flag, $sysm))
    	{
    		return '<font title="系统模块不可以删除">删除</font>';
    	}
    	else
    	{
    		return '<a href="?mod=widget&code=block&op=delete&flag='.$flag.'" onclick="javascript:return confirm(\'确定要删除吗？操作不可恢复，请慎重\');">删除</a>';
    	}
    }
}


?>