<? include handler('template')->file('header'); ?>
<form method="POST"  action="<?=$action?>">
<input type="hidden" name="FORMHASH" value='<?=FORMHASH?>'/>
<div class="site-ms__left">
<div class="t_area_out">
<div class="t_area_in">
<p class="cur_title" >用户登陆</p>
<div class="sect"  >
<div class="cont" style="width:400px;">			
<div class="field">
<label>帐  户</label>
<input name="username" type="text" class="f-l input_h" placeholder="输入用户名、邮箱或手机号码" />
</div>
<div class="field">
<label>密　码</label>
<input name="password" type="password" class="f-l input_h"/>
<span class="lostpassword"><a href="?mod=get_password">忘记密码？</a></span>
</div>
<div class="field">
<input name="keeplogin" type="checkbox" checked="checked" id="check_remember" />
<label id="remember" for="check_remember">记住登录状态</label>
</div>
<div id="l_act">
<input type="submit" class="btn btn-primary"  value="登 录">
</div>
</div>
<?=account('ulogin')->wlist()?>
</div>
</div>
</div>
</div>
<div class="site-ms__right">
<div class="t_area_out">
<h1>还没有本站帐户？</h1>
<div class="t_area_in">
<p><a class="R12" href="?mod=account&code=register">立即注册</a>，仅需30秒！</p>
</div>
</div>
<?=ui('widget')->load()?>
</div>
</form>
</div>
<? include handler('template')->file('footer'); ?>