<!--{template @admin/header}-->

<!-- * 快钱配置项 * -->

{eval
$pay = logic('pay')->SrcOne('kuaibillmobile');
$cfg = unserialize($pay['config']);
}
<form action="?mod=payment&code=save" method="post" enctype="multipart/form-data">
    <table cellspacing="1" cellpadding="4" width="100%" align="center" class="tableborder">
        <tr class="header">
            <td colspan="2">修改快钱移动快捷支付设置</td>
        </tr>
        <tr>
            <td width="23%" class="td_title">快钱商户编号(15位)：</td>
            <td width="77%">
                <input name="cfg[merchantid]" type="text" size="38" value="{$cfg['merchantid']}"></td>
        </tr>
        <tr>
            <td class="td_title">快钱会员编号(11位)：</td>
            <td>
                <input name="cfg[mebcode]" type="text" size="38" value="{$cfg['mebcode']}" />
            </td>
        </tr>
		<tr>
            <td class="td_title">私钥文件名：</td>
            <td>
                <input name="cfg[pemfile]" type="text" size="38" value="{$cfg['pemfile']}" />（含扩展名，例如：bill.pem）
				<br>此私钥文件（*.pem）必须要放在网站根目录下，否则支付失败。
            </td>
        </tr>
        <tr>
            <td class="td_title">证书文件名：</td>
            <td>
                <input name="cfg[cerfile]" type="text" size="38" value="kuaibill.cer" readonly/>（系统默认，不得修改）
				<br>此证书文件（kuaibill.cer）默认放在网站根目录下，不得删除，否则支付失败。
            </td>
        </tr>
		<tr>
            <td class="td_title">MNP通知接口：</td>
            <td><span style='font-family:Georgia,Times,"Times New Roman",serif;font-size:17px;color:green;line-height:40px;'>{echo rewrite(ini('settings.site_url').'/index.php?mod=callback&pid=kuaibillmobile')}</span>
				<br>请将此地址告知快钱客服备案登记，否则服务器无法进行数据对帐校验，造成支付失败。
            </td>
        </tr>
    </table>
    <br/>
    <center>
        <input type="hidden" name="id" value="{$pay['id']}" />
        <input type="submit" name="submit" value="提 交" class="button" />
    </center>
</form>
<!--{template @admin/footer}-->