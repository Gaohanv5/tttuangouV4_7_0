//###########################
//   Name:天天团购 
//   Link:tttuangou.net
//   Date:2011.01.07
//   Intro:主JS
//#############################
var userAgent = navigator.userAgent.toLowerCase();
var is_opera = userAgent.indexOf('opera') != -1 && opera.version();
var is_moz = (navigator.product == 'Gecko') && userAgent.substr(userAgent.indexOf('firefox') + 8, 3);
var is_ie = (userAgent.indexOf('msie') != -1 && !is_opera) && userAgent.substr(userAgent.indexOf('msie') + 5, 3);
var is_safari = (userAgent.indexOf('webkit') != -1 || userAgent.indexOf('safari') != -1);

//复制URL地址
function copyText(_sTxt){
	_sTxt=document.title+" "+_sTxt;
	if(is_ie) {
		clipboardData.setData('Text',_sTxt);
		alert ("网址“"+_sTxt+"”\n\n已经复制到您的剪贴板中\n您可以使用Ctrl+V快捷键粘贴到需要的地方");
	} else {
		prompt("请使用Ctrl+C快捷键复制下面的内容:",_sTxt); 
	}
	return false;
}
//TAB切换按钮
function setTab(name,cursel,n){
	for(i=1;i<=n;i++){
	var menu=document.getElementById(name+i);
	var con=document.getElementById("con_"+name+"_"+i);
	if(menu && con){
	menu.className=i==cursel?"hover":"";
	con.style.display=i==cursel?"block":"none";
	}
}
}
//TAB切换按钮2
function hideAllClips(){
	for (i=1; i<5; i++){
		var allClips="topNews_"+i;
		var clipNum="clipNum"+i;
		document.getElementById(allClips).style.display="none";
		document.getElementById(clipNum).className="ts3_mbtn2";
		}
} 
function clip_Switch(n){
	var curClip="topNews_"+n;
	var curClipNum="clipNum"+n;
	hideAllClips();
	document.getElementById(curClip).style.display="block";
	document.getElementById(curClipNum).className="ts3_mbtn1";
	scrollNewsCt=n; 
} 

//表格订单部分
(function($){
    $.fn.jExpand = function(){
        var element = this;

        $(element).find("tr:odd").addClass("odd");
        $(element).find("tr:not(.odd)").hide();
        $(element).find("tr:first-child").show();

        $(element).find("tr.odd").click(function() {
            $(this).next("tr").toggle();
        });
    }    
})(jQuery); 


//个性模版设置 2010.06.23
var arrCSS=[
    ["<img src='templates/default/images/zhanweifu.gif' width='30' height='20' class='themes themes1' title='默认风格'>","templates/default/styles/t1.css"],
    ["<img src='templates/default/images/zhanweifu.gif' width='30' height='20' class='themes themes2' title='蓝色风格'>","templates/tpl_2/styles/t1.css"],
    ["<img src='templates/default/images/zhanweifu.gif' width='30' height='20' class='themes themes3' title='喜庆风格'>","templates/tpl_3/styles/t1.css"],
	["<img src='templates/default/images/zhanweifu.gif' width='30' height='20' class='themes themes4' title='水晶风格'>","templates/tpl_4/styles/t1.css"],
	["<img src='templates/default/images/zhanweifu.gif' width='30' height='20' class='themes themes5' title='绿色风格'>","templates/tpl_5/styles/t1.css"],
    ""
];

// 获取样式表连接
function v(){
	return;
}

// 设置 Cookies 记录 
function writeCookie(name, value, expiredays){
	if (!expiredays) expiredays = 365;
	exp = new Date();
	exp.setTime(exp.getTime() + (86400 *1000 *expiredays));
	document.cookie = name + "=" + escape(value) + "; expires=" + exp.toGMTString() + "; path=/";
}

function readCookie(name){ 
	var search; 
	search = name + "="; 
	offset = document.cookie.indexOf(search); 
	if (offset != -1) { 
		offset += search.length; 
		end = document.cookie.indexOf(";", offset);  
		if (end == -1){
			end = document.cookie.length;
		}
		return unescape(document.cookie.substring(offset, end)); 
	}else{
		return "";
	}
}

function writeCSSLinks(){
  for(var i=0;i<arrCSS.length-1;i++){
    if(i>0) document.write('  ');
    document.write('<a href="javascript:v()" onclick="setStyleSheet(\'styles'+i+'\');location.reload();">'+arrCSS[i][0]+'</a>');
  }
}
function setStyleSheet(strCSS){
  writeCookie("stylesheet",strCSS, 365);
}

// 隐藏显示换肤框
function ShowHideDiv(init) {
	if(document.getElementById("skin-chose").style.display == "block"){
	    document.getElementById("skin-chose").style.display = "none";
  }
  else{
  	document.getElementById("skin-chose").style.display = "block";
  }
}

//分享产品获得积分
function sharescore(pid,uid,share){
	$.get('?mod=me&code=ajaxscore&uid='+uid+'&pid='+pid+'&share='+share,function(){});
}