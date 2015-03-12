<!--{template @admin/header}-->

<!-- * 网银直连配置项 * -->

{eval

	if (logic('pay')->apiz('bankdirect')->__is_bucket) $this->Messager('正在修复支付接口...', '?mod=payment&code=install&flag=bankdirect');

	$pay = logic('pay')->SrcOne('bankdirect');
	$key = logic('pay')->apiz('bankdirect')->getID();
	$val = meta($key);
	$merkey = logic('pay')->apiz('bankdirect')->getmerkey();

	########授权类型判断########
	$aclData = logic('acl')->Account();
    $licence = $aclData['licence'];
    ########授权类型判断########

    if($licence['type'] == '天天团购企业版授权') $to_url = 'http://cenwor.com/shop/goods.php?id=57';
    else $to_url = 'http://cenwor.com/shop/goods.php?id=57';

    if($licence['type'] == '天天团购企业版授权') $to_user = __('商业用户优惠购买通道');
    else $to_user = __('普通购买通道');

}

<form action="?mod=payment&code=auth" method="post" onsubmit="if(this.sellkey.value == ''){alert('请填写商户密钥');return false;}">
	<table cellspacing="1" cellpadding="4" width="100%" align="center" class="tableborder">
		<tr class="header">
			<td colspan="2">网银直连即时到帐在线设置</td>
		</tr>
		<tr>
			<td class="td_title" width="20%">MD5Key：</td>
			<td><input type='text' name="sellkey" style="width:260px" value="{$merkey}" /></td>
		</tr>
		<tr>
			<td class="td_title" width="20%">授权状态：</td>
			<td>{if $val == ''}<img src="templates/admin/images/btn_disable.gif">{else}<img src="templates/admin/images/btn_enable.png">{/if}</td>
		</tr>
		<tr>
			<td class="td_title" width="20%">对账地址：</td>
			<td>
				{echo rewrite(ini('settings.site_url').'/index.php?mod=callback&pid='.$pay['code'])}
			</td>
		</tr>
		{if $val != ''}
		<tr>
			<td></td>
			<td>
				<font color="red">
					如果您在使用过程中遇到问题，请按如下方式操作：
					<br/>
					1、通过ftp进入程序所在目录/data子目录，并删除“bd.”开头的php文件；
					<br/>
					2、删除上述文件后，<a href="">点此刷新修复接口</a>；
				</font>
			</td>
		</tr>
		{/if}

		{if $val == ''}
		<tr>
			<td class="td_title" width="20%">授权通道：</td>
			<td>
				{echo "<a href=\"$to_url\" target=\"_blank\">".$to_user."</a>"}
			</td>
		</tr>
		<tr>
			<td colspan='2'  style="text-align:center;"><img src="templates/admin/images/paynote.jpg"></td>
		</tr>
		{/if}
	</table>
	<br/>
	<center>
		<input type="hidden" name="id" value="{$pay['id']}" />
		{if $val == ''}<input type="submit" name="submit" value="获 取" class="button" />{else}<input type="submit" name="submit" value="更 新" class="button" />{/if}
	</center>
	</form>
	<br/><br/><br/>
	{if $val != ''}
	<table cellspacing="1" cellpadding="4" width="100%" align="center" class="tableborder">
		<tr>
			<td>
				<form action="?mod=payment&code=bankorder" method="post">
				<table>
					<tr><td colspan="4">当个别银行系统升级或不稳定时可以关闭对应银行启用状态，并且可以通过排序调整前台银行排序等级凸显优先级方便与银行间的合作活动。<br/>
										注：排序序号只支持大于1的连续整数，且不能重复；操作后请点击提交按钮使设置生效。</td></tr>
					<tr><td>启用</td><td>银行标识</td><td>银行名称</td><td>显示顺序</td></tr>
					{eval $i = 0;}
					{loop ini('bankdirect') $bank}
					{eval $lowercode = strtolower($bank['code']);$i++}
					<tr>
						<td>
							<input type="checkbox" name='enable[$i]' value="1" {if $bank[enable] == 1}checked="checked"{/if} title="是否启用"/>
						</td>
						<td><img src="static/images/ibank/{$lowercode}.gif" title="{$bank[name]}" width="121" height="33"></td>
						<td>{echo {$bank[name]}}</td>
						<td><input type="text" name='orders[$i]' value="{$i}"/>
							<input type="hidden" name='codes[$i]' value="{$bank[code]}">
							<input type="hidden" name='names[$i]' value="{$bank[name]}">
						</td>

					</tr>
					{/loop}
					<tr><td colspan='3' style="text-align:center;"><input type="submit" name="submit" value="提交"/></td></tr>
				</table>
				</form>
			</td>
		</tr>
	</table>
	{/if}


<!--{template @admin/footer}-->