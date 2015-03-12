<!--{template @admin/header}-->
<!-- * 中国银联配置项 * -->
{eval
$pay = logic('pay')->SrcOne('unionpaymobile');
$cfg = unserialize($pay['config']);
}
<form action="?mod=payment&code=save" method="post" enctype="multipart/form-data">
    <table cellspacing="1" cellpadding="4" width="100%" align="center" class="tableborder">
        <tr class="header">
            <td colspan="2">修改中国银联移动支付设置</td>
        </tr>
        <tr>
            <td width="23%" class="td_title">商户号：</td>
            <td width="77%">
                <input name="cfg[merchantid]" type="text" size="40" value="{$cfg['merchantid']}"> <a target="_blank" href="http://mobile.unionpay.com/merchant">申请银联支付</a> -- 申请类别：银联无卡快捷支付</td>
        </tr>
        <tr>
            <td class="td_title">商户密钥：</td>
            <td>
                <input name="cfg[password]" type="text" size="60" value="{$cfg['password']}" />
            </td>
        </tr>
		<tr>
            <td class="td_title">对帐地址：</td>
            <td><span style='font-family:Georgia,Times,"Times New Roman",serif;font-size:17px;color:green;line-height:40px;'>{echo rewrite(ini('settings.site_url').'/index.php?mod=callback&pid=unionpaymobile')}</span>
            </td>
        </tr>
		<tr class="header">
            <td colspan="2">说明：要求服务器必须有curl模块；否则无法支付。经检测：您当前服务器<b>
			{if function_exists("curl_init") && (function_exists("mbstring") || function_exists("iconv"))}
			<font color="green">支持</font>{else}<font color="red">不支持</font>{/if}
			</b>使用该支付方式。</td>
        </tr>
    </table>
    <br/>
    <center>
        <input type="hidden" name="id" value="{$pay['id']}" />
        <input type="submit" name="submit" value="提 交" class="button" />
    </center>
</form>
<!--{template @admin/footer}-->