<!--{template @admin/header}-->

<!-- * 银行汇款 * -->

{eval
	$pay = logic('pay')->SrcOne('bank');
	$cfg = unserialize($pay['config']);
}
{~ui('loader')->addon('editor.kind')}
<form action="?mod=payment&code=save" method="post" enctype="multipart/form-data">
	<table cellspacing="1" cellpadding="4" width="100%" align="center" class="tableborder">
		<tr class="header">
			<td colspan="2">银行汇款详情设置</td>
		</tr>
		<tr>
			<td width="3%" bgcolor="#F8F8F8">详情:</td>
			<td width="97%" bgcolor="#FFFFFF">
				<textarea id="editor" name="cfg[content]" style="width:100%;">{$cfg['content']}</textarea>
			</td>
		</tr>
	</table>
	<br/>
	<center>
		<input type="hidden" name="replacer" value="true" />
		<input type="hidden" name="id" value="{$pay['id']}" />
		<input type="submit" name="addsubmit" value="提 交" class="button" />
	</center>
</form>
<script type="text/javascript">
$(document).ready(function(){
	KindEditor.ready(function(K) {K.create('#editor');});
});
</script>
<!--{template @admin/footer}-->