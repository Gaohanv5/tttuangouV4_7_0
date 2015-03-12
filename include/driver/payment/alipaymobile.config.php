<!--{template @admin/header}-->

<!-- * 支付宝配置项 * -->

{eval
$pay = logic('pay')->SrcOne('alipaymobile');
$cfg = unserialize($pay['config']);
}
<form action="?mod=payment&code=save" method="post" enctype="multipart/form-data">
    <table cellspacing="1" cellpadding="4" width="100%" align="center" class="tableborder">
        <tr class="header">
            <td colspan="2">修改支付宝移动快捷支付设置</td>
        </tr>
        <tr>
            <td width="23%" class="td_title">支付宝账户：</td>
            <td width="77%">
                <input name="cfg[account]" type="text" size="38" value="{$cfg['account']}">
            </td>
        </tr>
        <tr>
            <td class="td_title">合作者身份(PID)：</td>
            <td>
                <input name="cfg[partner]" type="text" size="38" value="{$cfg['partner']}" />
            </td>
        </tr>
        <tr>
            <td class="td_title">RSA私钥(网站方)：</td>
            <td>
                <textarea name="cfg[web_pri_key]" style="width:480px;height:240px;">{$cfg['web_pri_key']}</textarea>
            </td>
        </tr>
        <tr>
            <td class="td_title">RSA公钥(支付宝)<br>（这一项请不要修改，保持默认的）</td>
            <td>
                <textarea name="cfg[ali_pub_key]" title="这里的RSA公钥(支付宝)值请不要修改，保持默认的就可以了。" readonly="true" style="width:480px;height:100px;">{echo $cfg['ali_pub_key'] ? $cfg['ali_pub_key'] : "-----BEGIN PUBLIC KEY-----\nMIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCnxj/9qwVfgoUh/y2W89L6BkRA\nFljhNhgPdyPuBV64bfQNN1PjbCzkIM6qRdKBoLPXmKKMiFYnkd6rAoprih3/PrQE\nB/VsW8OoM8fxn67UDYuyBTqA23MML9q1+ilIZwBC2AQ2UBVOrFXfFl75p6/B5Ksi\nNG9zpgmLCUYuLkxpLQIDAQAB\n-----END PUBLIC KEY-----"}</textarea>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="http://t.jishigou.net/topic/261910" target="_blank" style="color:red">集成说明</a>
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