<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"><head> <meta http-equiv="Content-Type" content="text/html; charset=<?=ini("settings.charset")?>" /> <meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" /> <title>天天团购后台管理系统</title> <?=ui('loader')->css('#admin/admin_m')?> <style type="text/css">
html,body{ height:100%;}
</style> </head> <body > <script type="text/javascript" src="static/js/jquery.js"></script> <script type="text/javascript">
function setTab(name,cursel,n){
for(i=1;i<=n;i++){
var menu=document.getElementById(name+i);
var con=document.getElementById("con_"+name+"_"+i);
if(menu && con){
menu.className=i==cursel?"navon":"";
con.style.display=i==cursel?"block":"none";
}
}
return false;
}
function adminnavhtml(str1,str2){
var ahtml = '<p class="ifamenav">'+str1+' - '+str2+'</p>';
$("iframe").load(function(){
if($(this).contents().find(".ifamenav").length > 0){
$(this).contents().find(".ifamenav").html(str1+' - '+str2);
}else{
$(this).contents().find("body").prepend(ahtml);
}
})
}
</script> <table width="100%" height="100%"  border="0" cellpadding="0" cellspacing="0"> <tr> <td colspan="2" height="77" valign="top"><div id="header"> <div class="logo fl"> <div class="png"> <a href="<?=$this->Config['site_url']?>/admin.php"><img src="templates/default/./images/logo.png" alt=" 天天团购系统 " /></a> </div> <div class="lun"><span style="color:#ff0000">V<?=SYS_VERSION?><?=SYS_RELEASE?></span></div> </div> <ul class="nav">
<? if(is_array($menuList)) { foreach($menuList as $i => $menuOne) { ?>
<li id="nav<?=$i?>" onClick="return setTab('nav',<?=$i?>,9)"
<? if($i==1) { ?>
 class="navon"
<? } ?>
><em><a href="#"><?=$menuOne['title']?></a></em></li>
<? } } ?>
</ul> <div class="wei fl">用户名：<?=MEMBER_NAME?>（<a href="admin.php?mod=login&code=logout">退出</a>） &nbsp;|&nbsp; <a href="admin.php?mod=index&code=home" target="main">控制面板首页</a> &nbsp;|&nbsp; <a href="admin.php?mod=salecount" target="main">报表统计</a>&nbsp;|&nbsp; <a href="admin.php?mod=cache" target="main">清空缓存</a> &nbsp;|&nbsp; <a title="在新窗口中打开访问<?=SYS_NAME?>" href="index.php" style="cursor: pointer;" class="s0" target="_blank">访问前台</a> &nbsp;|&nbsp;  <a title="查看天天团购程序更新说明" href="http://www.tttuangou.net/download.html" style="cursor: pointer;" class="s0" target="_blank">更新说明</a> &nbsp;|&nbsp; <a title="后退到前一页" onClick="history.go(-1);" style="cursor: pointer;" >后退一页</a> &nbsp;</div> <div class="wei2 fr"> <TABLE> <TR> <TD valign="top"><div style="_padding-top:6px"><img title="商业用户可QQ在线咨询天天团购客服" style="cursor: pointer" onClick="javascript:window.open('http://bizapp.qq.com/webc.htm?new=0&sid=800058566&o=tttuangou.net&q=7', '_blank', 'height=544, width=644,toolbar=no,scrollbars=no,menubar=no,status=no');"  border="0" src="./templates/default/images/admincp/qq.gif"></div></TD> </TR> </TABLE> </div> </div></td> </tr> <tr> <td valign="top" id="main-fl" style="height:94%; overflow:hidden; "><div id="left" style="height:100%; overflow:auto;">
<? if(is_array($menuList)) { foreach($menuList as $i => $menuOne) { ?>
<div id="con_nav_<?=$i?>"
<? if($i>1) { ?>
 style="display:none;"
<? } ?>
> <h1>
<? if($i>1) { ?>
<?=$menuOne['title']?>
<? } else { ?>常用操作 [<a style="background:none;padding:0;margin:0;display:inline;" href="admin.php?mod=setting&code=modify_shortcut" target="main" title="设置快捷功能菜单">设置</a>]
<? } ?>
</h1> <div class="cc"/> </div> <ul>
<? if(is_array($menuOne['sub_menu_list'])) { foreach($menuOne['sub_menu_list'] as $j => $menu) { ?>
<? if($menu['link']!='hr') { ?>
<li><a href="<?=$menu['link']?>" target="main" onClick="adminnavhtml('<?=$menuOne['title']?>','<?=$menu['title']?>');"><?=$menu['title']?></a></li>
<? } else { ?></ul> <h1><?=$menu['title']?></h1> <div class="cc"/> </div> <ul>
<? } ?>
<? } } ?>
</ul> </div>
<? } } ?>
</td> <td valign="top" id="mainright" style="height:94%; "> <iframe id="ifm" name="main" frameborder="0" width="100%" height="100%" frameborder="0" scrolling="yes" style="overflow: visible;" src="admin.php?mod=index&code=home"> </iframe> </td> </tr> </table> </body> </html>