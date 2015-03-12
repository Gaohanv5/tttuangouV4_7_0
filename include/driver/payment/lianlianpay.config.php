<!--{template @admin/header}-->

<!-- * 连连支付配置项 * -->

{eval
$pay = logic('pay')->SrcOne('lianlianpay');
$cfg = unserialize($pay['config']);
}
<form action="?mod=payment&code=save" method="post" enctype="multipart/form-data">
    <table cellspacing="1" cellpadding="4" width="100%" align="center" class="tableborder">
        <tr class="header">
            <td colspan="2">银行卡快捷支付设置<br>
			1、此快捷支付（含手机移动支付）对外费率1%（注意不是传统网银支持），通过天天团购官方签约只需0.9%；<br>
			2、此快捷支付接口一次设置同时支持web网站、手机客户端、wap支付使用；<br>
			3、快捷支付与网银支付区别：<br>
			网银支付：首先需要用户开通网银功能，并且每次支付都需要跳到相关银行页面，重复填写卡号、密码等；<br>
			快捷支付：不论用户是否开通网银都可以用，用户只需第一次支付的时候填写银行卡，以后只需填写手机短信验证码即可完成支付，简单便捷，减少用户付款障碍。
			</td>
        </tr>
        <tr>
            <td width="23%" class="td_title">商户编号：</td>
            <td width="77%">
                <input name="cfg[oid_partner]" type="text" size="38" value="{$cfg['oid_partner']}">
                <span><font color="red">如需开通请直接联系天天客服QQ：800058566</font></span>
            </td>
        </tr>
        <tr>
            <td width="23%" class="td_title">安全校验码：</td>
            <td width="77%">
                <input name="cfg[key]" type="text" size="38" value="{$cfg['key']}">
                <span><font color="red"></font></span>
            </td>
        </tr>
        <tr style="display: none;">
            <td width="23%" class="td_title">商户业务类型：</td>
            <td width="77%">
                <select name="cfg[busi_partner]">
                    <option value="101001"{if '101001'==$cfg['busi_partner']} selected="true" {/if}>虚拟商品销售</option>
                    <option value="109001"{if '109001'==$cfg['busi_partner']} selected="true" {/if}>实物商品销售</option>
                    <option value="108001"{if '108001'==$cfg['busi_partner']} selected="true" {/if}>外部帐户充值</option>
                </select>
                <span><font color="red"></font></span>
            </td>
        </tr>
    </table>
    <br/>
    <input type="hidden" name="cfg[version]" value="1.0" />
    <input type="hidden" name="cfg[id_type]" value="0" />
    <input type="hidden" name="cfg[sign_type]" value="MD5" />
    <input type="hidden" name="cfg[valid_order]" value="1440" />
    <input type="hidden" name="cfg[input_charset]" value="{~strtolower(ini('settings.charset'))}" />
    <input type="hidden" name="cfg[transport]" value="http" />
    <center>
        <input type="hidden" name="id" value="{$pay['id']}" />
        <input type="submit" name="submit" value="提 交" class="button" />
    </center>
</form>
<!--{template @admin/footer}-->