<!--{template @admin/header}-->

<!-- * 机器人配置项 * -->

{eval
	$flag = 'qqrobot';
	$cfg = ini('notify.api.'.$flag);
}
<form action="?mod=notify&code=save" method="post" enctype="multipart/form-data">
	<table cellspacing="1" cellpadding="4" width="100%" align="center" class="tableborder">
		<tr class="header">
			<td colspan="2">QQ机器人配置</td>
		</tr>
		<tr>
			<td width="23%" bgcolor="#F8F8F8">名称:</td>
			<td width="77%" bgcolor="#FFFFFF">
			    <input name="cfg[name]" type="text" value="{$cfg['name']}" />
			    <input name="cfg[enabled]" type="text" size="1" value="{echo $cfg['enabled']?'on':'off'}" readonly="readonly" />
			</td>
		</tr>
		<tr>
			<td width="23%" bgcolor="#F8F8F8">API Server IP:</td>
			<td width="77%" bgcolor="#FFFFFF">
				<input name="cfg[server]" type="text" value="{$cfg['server']}" />
			</td>
		</tr>
		<tr>
			<td bgcolor="#F8F8F8">API Server Port:</td>
			<td bgcolor="#FFFFFF">
				<input name="cfg[port]" type="text" value="{$cfg['port']}" />
			</td>
		</tr>
		<tr>
			<td bgcolor="#F8F8F8">API Server Seckey:</td>
		    <td bgcolor="#FFFFFF">
		    	<input name="cfg[seckey]" type="text" size="35" value="{$cfg['seckey']}" />
	    	</td>
		</tr>
	</table>
	<br/>
	<center>
		<input type="hidden" name="flag" value="{$flag}" />
		<input type="submit" name="addsubmit" value="提 交" />
	</center>
</form>

<!--{template @admin/footer}-->