<?php
/**
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package php
 * @name bankdirect.install.php
 * @date 2013-10-30 16:44:22
 */
 


$url_list = '?mod=payment&code=vlist';

$url_base = '?mod=payment&code=install&flag=bankdirect&step=';

$step = get('step');

if ($step)
{
	switch ($step)
	{
		case 'check':
		$rps = logic('pay')->apiz('bankdirect')->tool->service('/merchant/status');
		if (is_array($rps))
		{
			$this->Messager('正在加载支付接口核心文件，请稍候...', $url_base.'download', 1);
		}
		else
		{
			$this->Messager('无法检查授权状态：'.$rps.'<br/><br/><b><a href="http://cenwor.com/shop/goods.php?id=57" target="_blank">如果您未购买支付接口授权，请点击此处进行购买</a></b>', $url_list, null);
		}
		break;
		case 'download':
		$rps = logic('pay')->apiz('bankdirect')->tool->service('/merchant/api-kernel-file');
		if (is_array($rps))
		{
			$md5 = logic('pay')->apiz('bankdirect')->tool->api_kernel_file($rps['file.b64'], $rps['file.md5']);
			$this->Messager('正在进行核心文件安全性校验，请稍候...', $url_base.'hash&hash-s='.$rps['file.md5'].'&hash-l='.$md5, 1);
		}
		else
		{
			$this->Messager('无法加载支付接口核心文件：'.$rps, $url_list);
		}
		break;
		case 'hash':
		$md5_sv = get('hash-s');
		$md5_lc = get('hash-l');
		if ($md5_lc == $md5_sv)
		{
			$md5_3rd = logic('pay')->apiz('bankdirect')->tool->rd3service('/md5');
		}
		else
		{
			$md5_3rd = 'error';
		}
		if ($md5_lc == $md5_3rd)
		{
			$this->Messager('核心文件哈希值校验正确，正在获取接口配置信息，请稍候...', $url_base.'init', 1);
		}
		else
		{
			$this->Messager('核心文件哈希值校验失败，请稍候重试安装！', $url_list);
		}
		break;
		case 'init':
		$result = logic('pay')->apiz('bankdirect')->init();
		if (is_array($result))
		{
			$this->Messager('支付接口配置失败：'.$result['error'], $url_list);
		}
		else
		{
			$this->Messager('支付接口配置成功，正在进行最后安装...', $url_base.'final', 1);
		}
		break;
		case 'final':

		break;
	}
}
else
{
	$this->Messager('正在检查授权状态，请稍候...', $url_base.'check', 1);
}

?>