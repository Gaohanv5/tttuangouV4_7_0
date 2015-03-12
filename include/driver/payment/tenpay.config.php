<!--{template @admin/header}-->

<!-- * 财付通配置项 * -->

{eval
    $pay = logic('pay')->SrcOne('tenpay');
    $cfg = unserialize($pay['config']);
}

<style type="text/css">
.dsp_for_medi {display: none;}
</style>

<form action="?mod=payment&code=save" method="post" enctype="multipart/form-data">
	<table cellspacing="1" cellpadding="4" width="100%" align="center" class="tableborder">
		<tr class="header">
			<td colspan="2">修改财付通设置</td>
		</tr>
		<tr>
			<td class="td_title" width="20%">财付通密钥：</td>
			<td>
				<input name="cfg[key]" type="text" size="50" value="{$cfg['key']}" />
			</td>
		</tr>
		<tr>
			<td class="td_title">财付通商户号：</td>
			<td>
				<input name="cfg[bargainor]" type="text" value="{$cfg['bargainor']}">
			</td>
		</tr>
		<tr>
            <td class="td_title">支付接口类型：</td>
            <td>
                <select id="cfg_service" name="cfg[service]" onchange="dspChange()">
                    <option value="direct"{echo ($cfg['service']=='direct'||$cfg['service']=='')?' selected="selected"':''}>即时到帐接口</option>
                    <option value="medi"{echo $cfg['service']=='medi'?' selected="selected"':''}>担保交易接口</option>
                </select>
                <font class="dsp_for_medi" style="color:red;">
                	&nbsp;&nbsp;&nbsp;因为财付通担保交易接口不支持自动发货，建议不要开启！<b>推荐使用支付宝担保交易接口！</b>
                </font>
            </td>
        </tr>
	</table>
	<br/>
	<center>
		<input type="hidden" name="id" value="{$pay['id']}" />
		<input type="submit" name="submit" value="提 交" class="button" />
	</center>
</form>
<script type="text/javascript">
function dspChange()
{
    var ser = $('#cfg_service').val();
    if (ser == 'direct')
    {
        $('.dsp_for_medi').hide();
        $('.dsp_for_direct').show();
    }
    else
    {
        $('.dsp_for_direct').hide();
        $('.dsp_for_medi').show();
    }
}
$(document).ready(function(){
   dspChange();
});
</script>
<!--{template @admin/footer}-->