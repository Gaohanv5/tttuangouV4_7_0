<? if($list) { ?>
<div class="login_union">
<p>合作网站账号登录！</p>
<p>
<? if(is_array($list)) { foreach($list as $flag => $name) { ?>
<a href="?mod=account&code=login&op=union&flag=<?=$flag?>" title="<?=$name?>"><img src="templates/account/login/images/union_<?=$flag?>.gif" width="120px" height="24px"/></a>
<? } } ?>
</p>
</div>
<? } ?>