<?php

/**
 * 模块：订阅管理
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
        $this->CheckAdminPrivs('subscribe');
		$class = get('class', 'txt');
        $class = $class ? $class : 'mail';
        $typeDfs = logic('subscribe')->TypeList();
        $type = $typeDfs[$class];
        $list = logic('subscribe')->GetList($class);
        foreach ($list as $i => $one)
        {
            $city = logic('misc')->CityList($one['city']);
            $list[$i]['cityName'] = $city[0]['cityname'];
        }
        include handler('template')->file('@admin/subscribe');
    }
    function Del()
    {
        $this->CheckAdminPrivs('subscribe');
		$id = get('id', 'int');
        if (!$id)
        {
            $this->Messager('非法编号！');
        }
        logic('subscribe')->Del($id);
        $this->Messager('删除成功！');
    }
    function Broadcast()
    {
        $this->CheckAdminPrivs('subscribemail');
		$class = get('class', 'txt');
        $class = $class ? $class : 'mail';
        $typeDfs = logic('subscribe')->TypeList();
        $type = $typeDfs[$class];
        $list = logic('push')->template()->GetList($class);
        include handler('template')->file('@admin/subscribe_broadcast');
    }
    function Broadcast_add()
    {
        $this->CheckAdminPrivs('subscribemail');
		$class = get('class', 'txt');
        $actionName = '新建';
        $typeDfs = logic('subscribe')->TypeList();
        include handler('template')->file('@admin/push_template_mgr');
    }
    function Broadcast_edit()
    {
        $this->CheckAdminPrivs('subscribemail');
		$id = get('id', 'int');
        if (!$id)
        {
            $this->Messager('非法编号！');
        }
        $actionName = '编辑';
        $typeDfs = logic('subscribe')->TypeList();
        $tpl = logic('push')->template()->GetOne($id);
        $class = $tpl['type'];
        include handler('template')->file('@admin/push_template_mgr');
    }
    function Broadcast_save()
    {
        $this->CheckAdminPrivs('subscribemail');
		$id = post('id', 'int');
        $data = array();
        $data['type'] = post('type', 'txt');
        $data['name'] = post('name', 'txt');
        $data['intro'] = post('intro', 'txt');
        $data['title'] = post('title', 'txt');
        $data['content'] = post('content');
        logic('push')->template()->Update($id, $data);
        $this->Messager('更新成功！', '?mod=subscribe&code=broadcast&class='.$data['type']);
    }
    function Broadcast_del()
    {
        $this->CheckAdminPrivs('subscribemail');
		$id = get('id', 'int');
        if (!$id)
        {
            $this->Messager('非法编号！');
        }
        logic('push')->template()->Del($id);
        $this->Messager('删除成功！');
    }
    function Push()
    {
        $this->CheckAdminPrivs('subscribe','ajax');
		$class = get('class', 'txt');
        $tid = get('tid', 'int');
        $tpl = logic('push')->template()->GetOne($tid);
        $city = get('city', 'int');
        logic('subscribe')->Push($class, $city, array('title'=>addslashes($tpl['title']),'content'=>addslashes($tpl['content'])));
        exit('ok');
    }
	public function Push_direct()
	{
		$this->CheckAdminPrivs('subscribe','ajax');
		$class = get('class', 'txt');
        $tid = get('tid', 'int');
        $tpl = logic('push')->template()->GetOne($tid);
        $targets = post('targets');
        logic('subscribe')->Push_direct($class, $targets, array('title'=>addslashes($tpl['title']),'content'=>addslashes($tpl['content'])));
        exit('ok');
	}
    function Push_preview()
    {
        $this->CheckAdminPrivs('subscribe','ajax');
		$class = get('class', 'txt');
        $tid = get('tid', 'int');
        $tpl = logic('push')->template()->GetOne($tid);
        $target = get('target', 'txt');
        logic('push')->addi($class, $target, array('subject'=>addslashes($tpl['title']),'content'=>addslashes($tpl['content'])));
        exit('ok');
    }
    public function Generate()
    {
        $this->CheckAdminPrivs('subscribe');
		$from = get('from', 'txt');
        $idx = get('idx', 'int');
        if ($from == 'product')
        {
            $product = logic('product')->GetOne($idx);
            $source = $product['flag'];
            $cityID = ($product['display'] == PRO_DSP_Global) ? 0 : $product['city'];
        }
        $flag = get('type', 'txt');
        $template = logic('push')->template()->Search('name', $flag.':'.substr(md5($source), 12, 6));
        include handler('template')->file('@admin/subscribe_generate');
    }
    public function Generate_template()
    {
        $this->CheckAdminPrivs('subscribe','ajax');
		$flag = get('flag', 'txt');
        $from = get('from', 'txt');
        $idx = get('idx', 'int');
        if ($from == 'product')
        {
            $data = logic('product')->GetOne($idx);
            $source = $data['flag'];
        }
        $content = handler('template')->content('@html/push/'.$flag.'/default', $data);
        $data = array();
        $data['type'] = $flag;
        $data['name'] = $flag.':'.substr(md5($source), 12, 6);
        $data['intro'] = addslashes('[ '.date('Y-m-d').' ] '.$source);
        $data['title'] = addslashes(ini('settings.site_name').'：'.$source);
        $data['content'] = addslashes($content);
        $id = logic('push')->template()->Update(0, $data);
        exit((string)$id);
    }
    public function Template_preview()
    {
        $this->CheckAdminPrivs('subscribe','ajax');
		$id = get('id', 'int');
        $template = logic('push')->template()->GetOne($id);
        exit($template['content']);
    }
    public function Config()
    {
        $this->CheckAdminPrivs('subscribe');
		$typeDfs = logic('subscribe')->TypeList();
        $type = 'config';
        include handler('template')->file('@admin/subscribe_config');
    }
}

?>