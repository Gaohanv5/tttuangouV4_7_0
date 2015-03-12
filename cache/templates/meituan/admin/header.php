<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> <html xmlns="http://www.w3.org/1999/xhtml"> <head> <meta http-equiv="Content-Type" content="text/html; charset=<?=ini("settings.charset")?>"> <script type="text/javascript">
var thisSiteURL = '<?=ini("settings.site_url")?>/';
var tuangou_str = '<?=ini("settings.tuangou_str")?>';
</script> <?=ui('loader')->css('#admin/css/admincp')?> <?=ui('loader')->js('@jquery')?> <?=ui('loader')->js('@jquery.notify')?> <?=ui('loader')->addon('dialog.art')?> <?=ui('loader')->addon('dialog.art.iframe')?> <?=app('ucard')->init()?> <script language="JavaScript">
function checkalloption(form, value) {
for(var i = 0; i < form.elements.length; i++) {
var e = form.elements[i];
if(e.value == value && e.type == 'radio' && e.disabled != true) {
e.checked = true;
}
}
}
function checkallvalue(form, value, checkall) {
var checkall = checkall ? checkall : 'chkall';
for(var i = 0; i < form.elements.length; i++) {
var e = form.elements[i];
if(e.type == 'checkbox' && e.value == value) {
e.checked = form.elements[checkall].checked;
}
}
}
function zoomtextarea(objname, zoom) {
zoomsize = zoom ? 10 : -10;
obj = $(objname);
if(obj.rows + zoomsize > 0 && obj.cols + zoomsize * 3 > 0) {
obj.rows += zoomsize;
obj.cols += zoomsize * 3;
}
}
function redirect(url) {
window.location.replace(url);
}
function checkall(form, prefix, checkall) {
var checkall = checkall ? checkall : 'chkall';
for(var i = 0; i < form.elements.length; i++) {
var e = form.elements[i];
if(e.name != checkall && (!prefix || (prefix && e.name.match(prefix)))) {
e.checked = form.elements[checkall].checked;
}
}
}
</script> 
<? echo ui('loader')->css('#admin/css/'.$this->Module.'.'.$this->Code) ?>
 </head> <body> <table width="100%" border="0" cellpadding="2" cellspacing="6" style="_margin-left:-10px; "> <tr class="__none__"> <td><table width="100%" border="0" cellpadding="2" cellspacing="6"> <tr class="__none__"> <td>
<? if($__is_messager!=true) { ?>
 
<? } ?>