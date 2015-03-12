<?php

/**
 * 模块：支付方式管理
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package module
 * @name payment.mod.php
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
		$this->CheckAdminPrivs('payment');
		header('Location: ?mod=payment&code=vlist');
	}
	public function vList()
	{
		$this->CheckAdminPrivs('payment');
		$list = logic('pay')->SrcList();
		$list_local = $this->local_list();

		$funcs = array('fsockopen'=>'支付完成后的通知校验时使用', 'pfsockopen'=>0, 'file_get_contents'=>0, 'curl_exec'=>0, 'openssl_open'=>'某些支付方式需要使用到HTTPS/SSL连接', );
		foreach($funcs as $k=>$v) {
			$row = array('name'=>$v, 'value'=>$k, 'enabled'=>function_exists($k));
			$funcs[$k] = $row;
		}

		include handler('template')->file('@admin/payment_list');
	}
	public function install()
	{
		$this->CheckAdminPrivs('payment');
		$flag = get('flag', 'txt');
		$list_local = $this->local_list();
		if (!isset($list_local[$flag]))
		{
			$this->Messager('不可识别的支付标记，系统无法进行安装！', '?mod=payment&code=vlist');
		}
		$db_pay = logic('pay')->SrcOne($flag);
		if ($db_pay['code'] != 'bankdirect')
		{
			if ($db_pay['id'])
			{
				$this->Messager('支付方式已经安装过了！', '?mod=payment&code=vlist');
			}
		}
		$file = DRIVER_PATH.'payment/'.$flag.'.install.php';
		if (is_file($file))
		{
			include $file;
		}
		$payment = $list_local[$flag];
		if ($db_pay['id'] > 0)
		{
			$r = true;
		}
		else
		{
			$datax = array('code' => $flag, 'name' => $payment['name'], 'detail' => $payment['detail'], 'order' => 888, 'config' => 'N;', 'enabled' => 'false');
			$r = dbc(DBCMax)->insert('payment')->data($datax)->done();
		}
		if ($r)
		{
			$this->Messager('安装成功！', '?mod=payment&code=vlist');
		}
		else
		{
			$this->Messager('安装失败！', '?mod=payment&code=vlist');
		}
	}
	function Config()
	{
		$this->CheckAdminPrivs('payment');
		$flag = get('flag', 'txt');
		$file = DRIVER_PATH.'payment/'.$flag.'.config.php';
		if (!is_file($file))
		{
			$this->Messager('此支付方式没有配置项！');
		}
		else
		{
			include handler('template')->absfile($file);
		}
	}
	private function Config_link($flag)
	{
		$file = DRIVER_PATH.'payment/'.$flag.'.config.php';
		if (!is_file($file))
		{
			return '<font title="此接口不需要设置">设置</font>';
		}
		else
		{
			return '<a href="?mod=payment&code=config&flag='.$flag.'">设置</a>';
		}
	}
	function Save()
	{
		$this->CheckAdminPrivs('payment');
		if ($_POST['cfg']['content'] && post('replacer') == 'true')
		{
			$_POST['cfg']['content'] = str_replace(array('"','\\',"'"), '', $_POST['cfg']['content']);
		}
		$data = array(
			'config' => serialize(post('cfg', 'trim'))
		);
		logic('pay')->Update($data, 'id='.post('id', 'number'));
		$this->Messager('修改完成！', '?mod=payment');
	}
	private $payment_local_list = null;
	private function local_list()
	{
		if (is_null($this->payment_local_list))
		{
			$list_local = array();
			$local_file = DRIVER_PATH.'payment/payment.list.php';
			if (is_file($local_file))
			{
				$list_local = include $local_file;
			}
			$this->payment_local_list = $list_local;
		}
		return $this->payment_local_list;
	}

	function auth(){
		$this->CheckAdminPrivs('payment');
		$key = logic('pay')->apiz('bankdirect')->getID();
		$rs  = logic('pay')->apiz('bankdirect')->init();

		if(meta($key) == ''){
			$str = __('获取授权信息成功！');
		}else{
			$str = __('更新授权信息成功！');
		}

		if ($rs === true) {
			return $this->Messager($str, 'admin.php?mod=payment');
		}else{
			return $this->Messager(__('获取授权信息失败！').'原因：'.$rs['error'],null,null );
		}
	}

	function itemcount($a){
		if($a>1)
		$x=$a+$this->itemcount($a-1);
		else
		$x=$a;

		return $x;
	}

	function bankorder(){
		$this->CheckAdminPrivs('payment');
		$names = (array)post('names');
		$codes = (array)post('codes');
		$orders= (array)post('orders');
		$enable= (array)post('enable');

		if (!$orders || !$codes || !$names) 	$this->Messager(__('排序失败!'));

		$new_list = array();
		foreach ($orders as $k => $v) {

			$sum += $v;
			if ($k != $v) {
				$new_list[$k]['name'] = $names[$v];
				$new_list[$k]['code'] = $codes[$v];
				$new_list[$k]['enable'] = 1 === (int)$enable[$v] ? 1 : 0;
			}else{
				$new_list[$v]['name'] = $names[$k];
				$new_list[$v]['code'] = $codes[$k];
				$new_list[$v]['enable'] = 1 === (int)$enable[$k] ? 1 : 0;
			}

		}

		if ($sum !== $this->itemcount(count($orders))) {
			return $this->Messager(__('排序值不能重复!'));
		}

		if (ini('bankdirect',$new_list)) 	$this->Messager(__('排序成功!'));
	}
}

?>