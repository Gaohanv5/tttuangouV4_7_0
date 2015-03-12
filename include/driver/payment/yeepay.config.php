<!--{template @admin/header}-->

<!-- * 易宝一键支付配置项 * -->

{eval
$pay = logic('pay')->SrcOne('yeepay');
$cfg = unserialize($pay['config']);
}
<form action="?mod=payment&code=save" method="post" enctype="multipart/form-data">
    <table cellspacing="1" cellpadding="4" width="100%" align="center" class="tableborder">
        <tr class="header">
            <td colspan="2">修改易宝一键支付设置</td>
        </tr>
        <tr>
            <td width="23%" class="td_title">商户编号：</td>
            <td width="77%">
                <input name="cfg[merchantaccount]" type="text" size="38" value="{$cfg['merchantaccount']}">
				<span><font color="red">开通易宝支付，直接联系易宝-天天负责人QQ：1060977716，或者联系天天客服QQ：800058566</font></span>
			</td>
        </tr>
		<tr>
            <td width="23%" class="td_title">业务开通范围：</td>
            <td width="77%">
                <input name="cfg[yeepayType]" id="yeepayType_1" type="radio" value="1" {if $cfg['yeepayType'] == '1'}checked{/if} class="radio"><label for="yeepayType_1">仅移动业务</label>
				<input name="cfg[yeepayType]" id="yeepayType_2" type="radio" value="2" {if $cfg['yeepayType'] == '2'}checked{/if} class="radio"><label for="yeepayType_2">仅PC业务</label>
				<input name="cfg[yeepayType]" id="yeepayType_3" type="radio" value="3" {if $cfg['yeepayType'] == '3'}checked{/if} class="radio"><label for="yeepayType_3">两者都开通</label>
				<span style="margin-left:42px;">不清楚，请咨询易宝QQ：1060977716</span>
			</td>
        </tr>
		<tr>
            <td width="23%" class="td_title">交易类别码：</td>
            <td width="77%">
				<select name="cfg[productcatalog]">
				<option value="1" {if $cfg['productcatalog'] == '1'}selected{/if}>1-虚拟产品</option>
				<option value="3" {if $cfg['productcatalog'] == '3'}selected{/if}>3-公共事业缴费</option>
				<option value="4" {if $cfg['productcatalog'] == '4'}selected{/if}>4-手机充值</option>
				<option value="6" {if $cfg['productcatalog'] == '6'}selected{/if}>6-公益事业</option>
				<option value="7" {if $cfg['productcatalog'] == '7'}selected{/if}>7-实物电商</option>
				<option value="8" {if $cfg['productcatalog'] == '8'}selected{/if}>8-彩票业务</option>
				<option value="10" {if $cfg['productcatalog'] == '10'}selected{/if}>10-行政教育</option>
				<option value="11" {if $cfg['productcatalog'] == '11'}selected{/if}>11-线下服务业</option>
				<option value="13" {if $cfg['productcatalog'] == '13'}selected{/if}>13-微信实物电商</option>
				<option value="14" {if $cfg['productcatalog'] == '14'}selected{/if}>14-微信虚拟电商</option>
				<option value="15" {if $cfg['productcatalog'] == '15'}selected{/if}>15-保险行业</option>
				<option value="16" {if $cfg['productcatalog'] == '16'}selected{/if}>16-基金行业</option>
				<option value="17" {if $cfg['productcatalog'] == '17'}selected{/if}>17-电子票务</option>
				<option value="18" {if $cfg['productcatalog'] == '18'}selected{/if}>18-金融投资</option>
				</select>
				<span style="margin-left:132px;">不清楚，请咨询易宝QQ：1060977716</span>
			</td>
        </tr>
		<tr>
            <td class="td_title">商户公钥：</td>
            <td>
                <textarea name="cfg[merchantPublicKey]" rows="5" cols="100">{$cfg['merchantPublicKey']}</textarea>
				&nbsp;&nbsp;<a href="http://mobiletest.yeepay.com/file/caculate/to_rsa" target="_blank">生成RSA密钥对</a>
            </td>
        </tr>
		<tr>
            <td class="td_title">商户私钥：</td>
            <td>
                <textarea name="cfg[merchantPrivateKey]" rows="15" cols="100">{$cfg['merchantPrivateKey']}</textarea>		
            </td>
        </tr>
        <tr>
            <td class="td_title">易宝公钥：</td>
            <td>
                <textarea name="cfg[yeepayPublicKey]" rows="5" cols="100">{$cfg['yeepayPublicKey']}</textarea>
				&nbsp;&nbsp;<a href="http://www.yeepay.com/" target="_blank">登录易宝商户账号</a>
            </td>
        </tr>
		
        <tr>
            <td class="td_title">操作步骤：</td>
            <td>
                第一步：点击“<a href="http://mobiletest.yeepay.com/file/caculate/to_rsa" target="_blank">生成RSA密钥对</a>”生成商户公钥和私钥。并填到上方对应的栏目中。<br>
				第二步：<a href="http://www.yeepay.com/" target="_blank">登录</a>自己的易宝账户http://www.yeepay.com/（没有的话请先注册商户账号）->产品管理->RSA公钥管理->新增RSA公钥，填入之前生产的商户公钥，用来生成易宝RSA公钥，将易宝RSA公钥（易宝公钥）并填到上方对应的栏目中。<br>
				第三步：检查商户编码、交易类别码、易宝公钥、商户公钥、商户私钥等信息填写无误后，点击"提交"。<br>
				第四步：接口测试，最低支持2分钱测试，每笔交易会扣除1.5%的费率，不足1分的费率按1分扣除。在易宝商家后台可以正常查询到交易记录则表明接口配置成功。
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