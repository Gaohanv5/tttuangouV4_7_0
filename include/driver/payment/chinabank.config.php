<!--{template @admin/header}-->

<!-- * 网银在线配置项 * -->

{eval
	$pay = logic('pay')->SrcOne('chinabank');
	$cfg = unserialize($pay['config']);
}

<form action="?mod=payment&code=save" method="post" enctype="multipart/form-data">
	<table cellspacing="1" cellpadding="4" width="100%" align="center" class="tableborder">
		<tr class="header">
			<td colspan="2">修改网银在线设置</td>
		</tr>
		<tr>
			<td class="td_title" width="20%">网银在线商户号：</td>
			<td>
				<input name="cfg[account]" type="text" size="35" value="{$cfg['account']}" />
			</td>
		</tr>
		<tr>
			<td class="td_title" width="20%">网银在线密钥：</td>
			<td>
				<input name="cfg[key]" type="text" size="35" value="{$cfg['key']}" />
			</td>
		</tr>
		<tr>
			<td class="td_title" width="20%">对账地址：</td>
			<td>
				{echo rewrite(ini('settings.site_url').'/index.php?mod=callback&pid='.$pay['id'])}
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