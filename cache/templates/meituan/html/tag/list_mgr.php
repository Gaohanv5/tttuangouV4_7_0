<form id="tag_list_mgr_form" method="post"  action="admin.php?mod=tag&code=save">
<input type="hidden" name="FORMHASH" value='<?=FORMHASH?>'/>
<input type="hidden" name="product_id" value="<?=$product_id?>" />
<table>
<tr>
<td>排序<br>（数字越大越靠前）</td>
<td>标签名称<br>（建议4个汉字以内）</td>
<td>备注说明<br>（建议100字以内，<br>可以为空）</td>
<td>显示期限<br>（如2014-08-15，<br>留空则一直显示）</td>
<td>显示状态<br>（最多支持6个）</td>
<td>操作</td>
</tr>
<? if(is_array($list)) { foreach($list as $row) { ?>
<? if($row['id'] > 0) { ?>
<tr id="product_<?=$product_id?>_tag_<?=$row['id']?>">
<td><input type="text" name="tag[<?=$row['id']?>][order]" size=4 value="<?=$row['order']?>" /></td>
<td><input type="text" name="tag[<?=$row['id']?>][name]" value="<?=$row['name']?>" /></td>
<td><input type="text" name="tag[<?=$row['id']?>][desc]" value="<?=$row['desc']?>" /></td>
<td><input type="text" name="tag[<?=$row['id']?>][expire]" size=10 value="<?=$row['expire']?>" /></td>
<td><input type="checkbox" name="tag[<?=$row['id']?>][enable]" value="1"
<? if($row['enable']) { ?>
  checked="true" 
<? } ?>
></td>
<td><a onclick="product_tag_delete('<?=$product_id?>', '<?=$row['id']?>');return false;" href="?#product_tag_delete">删除</a></td>
</tr>
<? } else { ?><? if($product_id > 0 && $row['tag_id'] > 0) logic('product_tag')->delete($product_id, $row['tag_id']); ?>
<? } ?>
<? } } ?>
<tr>
<td><input type="text" name="tag_new[0][order]" size=4 value="0" /></td>
<td><input type="text" name="tag_new[0][name]" value="" /></td>
<td><input type="text" name="tag_new[0][desc]" value="" /></td>
<td><input type="text" name="tag_new[0][expire]" size=10 value="" /></td>
<td><input type="checkbox" name="tag_new[0][enable]" value="1" checked="true" /></td>
<td> &nbsp; </td>
</tr>
<tr>
<td><input type="text" name="tag_new[1][order]" size=4 value="0" /></td>
<td><input type="text" name="tag_new[1][name]" value="" /></td>
<td><input type="text" name="tag_new[1][desc]" value="" /></td>
<td><input type="text" name="tag_new[1][expire]" size=10 value="" /></td>
<td><input type="checkbox" name="tag_new[1][enable]" value="1" checked="true" /></td>
<td> &nbsp; </td>
</tr>
<tr>
<td><input type="text" name="tag_new[2][order]" size=4 value="0" /></td>
<td><input type="text" name="tag_new[2][name]" value="" /></td>
<td><input type="text" name="tag_new[2][desc]" value="" /></td>
<td><input type="text" name="tag_new[2][expire]" size=10 value="" /></td>
<td><input type="checkbox" name="tag_new[2][enable]" value="1" checked="true" /></td>
<td> &nbsp; </td>
</tr>
<tr>
<td><input type="text" name="tag_new[3][order]" size=4 value="0" /></td>
<td><input type="text" name="tag_new[3][name]" value="" /></td>
<td><input type="text" name="tag_new[3][desc]" value="" /></td>
<td><input type="text" name="tag_new[3][expire]" size=10 value="" /></td>
<td><input type="checkbox" name="tag_new[3][enable]" value="1" checked="true" /></td>
<td> &nbsp; </td>
</tr>
</table>
</form>