<? include handler('template')->file('header'); ?>
<script language="javascript">
function checkEmail(email){
var emailRegExp = new RegExp("[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?");
if (!emailRegExp.test(email)||email.indexOf('.')==-1){
alert('email地址格式错误了哦~~');
$('#email').val('');
}else{
return true;
}
}
function check(){
if(!checkEmail($('#email').val())){
return false;
}
return true;
}
</script>
<div class="site-ms__left">
<div class="t_area_out">
<div class="t_area_in">
<p class="cur_title">邮件订阅</p>
<div class="sect">
<p class="B14">邮件预定每日最新<?=TUANGOU_STR?>信息，了解<?=TUANGOU_STR?>第一手资讯。</p>
<div class="enter_address">
<p class="B14"><?=$this->Config['site_name']?>运行中，欢迎通过邮件订阅每日<?=TUANGOU_STR?>信息！</p>
<div class="enter_address_c">
<form action="?mod=subscribe&code=save" enctype="multipart/form-data" method="post"  onsubmit="return check()">
<input type="hidden" name="FORMHASH" value='<?=FORMHASH?>'/>
<input name="type" type="hidden" value="mail" />
<div class="mail">
<label>邮件地址：</label>
<input name="target" type="text" class="f_input " id="email" size="20" value="<?=$target?>" />
<span>邮件地址不会被公开或发垃圾邮件。</span> 
</div>
<div class="product">
<label>选择您关注的城市：</label>
<select name="city" style="text">
<? if(is_array(logic('misc')->CityList())) { foreach(logic('misc')->CityList() as $i => $value) { ?>
<option value="<?=$value['cityid']?>" 
<? if(logic('misc')->City('id')==$value['cityid']) { ?>
selected
<? } ?>
><?=$value['cityname']?></option>
<? } } ?>
</select>
&nbsp;&nbsp;
<input type="submit" value="订 阅" class="btn btn-primary btn-small">
</div>
</form>
</div>
</div>
</div>
<p style="margin-left:14px; clear:both;">
<a href="?mod=subscribe&code=undo">如果您不想继续订阅，可以点击此处来取消</a>
</p>
</div>
</div>
</div>
<div class="site-ms__right">
<?=ui('widget')->load()?>
</div>
</div>
<? include handler('template')->file('footer'); ?>