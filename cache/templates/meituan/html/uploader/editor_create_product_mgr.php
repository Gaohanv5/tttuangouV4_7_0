<?=ui('loader')->addon('editor.kind')?>
<? @session_start() ?>

<? $CCPRE = ini('settings.cookie_prefix') ?>
<script type="text/javascript">
$(document).ready(function(){
document.title = 'Product Editor';
$.each($('.editor'), function(i, n){
var iid = 'editor_'+__rand_key();
$(this).attr('id', iid);
KindEditor.ready(function(K) {
K.create('#' + iid, {
width : '700px',
height : '120px',
resizeType : 1,
cssPath : '<?=ini("settings.site_url")?>/static/addon/KindEditor/kindeditor.css',
items : [
'source', 'fullscreen', '|', 'fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold', 'italic', 'underline',
'removeformat', '|', 'justifyleft', 'justifycenter', 'justifyright', 'insertorderedlist',
'insertunorderedlist', '|', 'emoticons', 'image', 'multiimage', 'link', 'table'
],
uploadJson : '<?=ini("settings.site_url")?>/?mod=upload&code=editor&field=imgFile',
imageSizeLimit : '<?=$uploadSize?>',
imageFileTypes : '<?=$allowExts?>',
imageUploadLimit : 10,
extraFileUploadParams : {
'PHPSESSID': '<? echo session_id(); ?>',
'<?=$CCPRE?>sid': '<? echo base64_encode($_COOKIE[$CCPRE."sid"]); ?>',
'<?=$CCPRE?>auth': '<? echo base64_encode($_COOKIE[$CCPRE."auth"]); ?>',
'<?=$CCPRE?>ajhAuth': '<? echo base64_encode($_COOKIE[$CCPRE."ajhAuth"]); ?>',
'HTTP_USER_AGENT': '<? echo base64_encode($_SERVER["HTTP_USER_AGENT"]); ?>',
'HTTP_X_REQUESTED_WITH': 'xmlhttprequest'
}
});
});
});
});
</script>