<!--{template @admin/header}-->

<!-- * 支付宝配置项 * -->

{eval
	$pay = logic('pay')->SrcOne('alipay');
	$cfg = unserialize($pay['config']);
}

{eval logic('misc')->alipay_check_xml_file();}

{eval $ssl = function_exists('openssl_open')}

<script type="text/javascript">
var global_cs_type = '';
$(document).ready(function(){
	var pk_url_prefix = 'https://b.alipay.com/order/pidKey.htm?pid=2088501217834340&product=';
	var cs_map = new Array();
	cs_map['create_direct_pay_by_user'] = 'fastpay';
	cs_map['create_partner_trade_by_buyer'] = 'escrow';
	cs_map['trade_create_by_buyer'] = 'dualpay';
	$('#cfg_service_select').bind('change', function(){
		var cs_type = cs_map[$(this).val()];
		global_cs_type = cs_type;
		var url = pk_url_prefix + cs_type;
		$('#link2pk').attr('href', url);
		{if !$ssl}
			if (cs_type != 'fastpay')
			{
				$.notify.alert('您的空间不支持OpenSSL，不能使用“担保交易接口”和“支付宝双接口”！ * <a style="color:red;font-weight:bold;" href="http://cenwor.com/thread-13271-1-1.html" target="_blank">帮助</a>');
				$(this).val('create_direct_pay_by_user');
				return;
			}
		{/if}
		$('#cfg_ssl_select').trigger('change');
	});
	$('#cfg_ssl_select').bind('change', function(){
		var ssl = $(this).val();
		{if !$ssl}
			if (ssl == 'true')
			{
				$.notify.alert('您的空间不支持OpenSSL，无法使用SSL连接！');
				$(this).val('false');
				return;
			}
		{/if}
		if (ssl == 'false' && global_cs_type != 'fastpay')
		{
			$.notify.alert('“SSL连接验证”只有即时到帐接口可以选择“否”，其他接口必须选择“是”！');
			$(this).val('true');
			return;
		}
	});
	$('#cfg_service_select').trigger('change');
});
</script>

<form action="?mod=payment&code=save" method="post" enctype="multipart/form-data">
	<table cellspacing="1" cellpadding="4" width="100%" align="center" class="tableborder">
		<tr class="header">
			<td colspan="2">修改支付宝设置</td>
		</tr>
		<tr>
			<td></td>
			<td>
				<a href="http://bizpartner.alipay.com/tiantian/index.htm" target="_blank"><font color="red">天天团购专属通道：点此免费开通支付宝</font></a>
			</td>
		<td>
		<tr>
			<td class="td_title">支付接口类型：</td>
			<td>
				<select id="cfg_service_select" name="cfg[service]">
					<option value="create_direct_pay_by_user"{if $cfg['service']=='create_direct_pay_by_user'} selected="selected"{/if}>即时到帐接口</option>
					<option value="create_partner_trade_by_buyer"{if $cfg['service']=='create_partner_trade_by_buyer'} selected="selected"{/if}>担保交易接口</option>
					<option value="trade_create_by_buyer"{if $cfg['service']=='trade_create_by_buyer'||$cfg['service']==''} selected="selected"{/if}>支付宝双接口</option>
				</select>
				&nbsp;&nbsp;&nbsp;<a id="link2pk" href="#921+513" target="_blank">请先选择支付接口类型，然后<font style="color:red;font-weight:bold;">点此获取PID，KEY</font></a>
			</td>
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
			<td class="td_title">安全校验码(KEY)：</td>
			<td>
				<input name="cfg[key]" type="text" size="38" value="{$cfg['key']}" />
			</td>
		</tr>
		<tr>
			<td class="td_title">是否使用SSL连接验证：</td>
			<td>
				<select id="cfg_ssl_select" name="cfg[ssl]">
					<option value="true"{if $cfg['ssl']=='true'} selected="selected"{/if}>是</option>
					<option value="false"{if $cfg['ssl']=='false'} selected="selected"{/if}>否</option>
				</select>
				 <br/>*注1：SSL是一种安全的HTTP连接，选择“是”的话，则您的空间必须支持OpenSSL才可以{if $ssl}（您的空间支持OpenSSL）{else}<b>（您的空间不支持OpenSSL，只能选择“否”）</b>{/if}
				 <br/>*注2：<font color="red">只有即时到帐接口可以选择“否”，其他接口必须选择“是”，且空间必须支持OpenSSL，否则交易会失败！</font>
				 <br/>*注3：<b>国外空间使用“担保交易接口/支付宝双接口”时有可能会造成无法发货的问题，请尽量选择使用国内空间！</b>
			</td>
		</tr>
		<tr>
			<td class="tr_nav tr_center" colspan="2">
				支付宝大快捷 - <a href="{~ihelper('tg.payment.alipay.fpm')}" target="_blank"><font color="blue">点此申请</font></a>
			</td>
		</tr>
		<tr class="tips">
			<td class="tr_center" colspan="2">
				<font color="red">提醒用户：使用大快捷应用之前请先确认申请开通了支付宝相应权限</font>
			</td>
		</tr>
		<tr>
			<td class="td_title">是否开启快捷登录？</td>
			<td><font class="ini" src="alipay.account.login.enabled"></font></td>
		</tr>
		<tr>
			<td class="td_title">是否开启快速获取收货地址？</td>
			<td><font class="ini" src="alipay.address.import.enabled"></font></td>
		</tr>
	</table>
	<br/>
	<center>
		<input type="hidden" name="id" value="{$pay['id']}" />
		<input type="submit" name="submit" value="提 交" class="button" />
	</center>
</form>
{~ui('loader')->js('#admin/js/sdb.parser')}
<!--{template @admin/footer}-->
