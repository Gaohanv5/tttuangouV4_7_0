<style>
/*	#divselect{width:186px; margin:80px auto; position:relative; z-index:10000;}
#divselect cite{width:150px; height:24px;line-height:24px; display:block; color:#807a62; cursor:pointer;font-style:normal;
padding-left:4px; padding-right:30px; border:3px solid #333333; 
background:url(xjt.png) no-repeat right center;}
#divselect ul{width:184px;border:1px solid #333333; background-color:#ffffff; position:absolute; z-index:20000; margin-top:-1px; display:none;}
#divselect ul li{height:24px; line-height:24px;}
#divselect ul li a{display:block; height:24px; color:#333333; text-decoration:none; padding-left:10px; padding-right:10px;}
#divselect ul li a:hover{background-color:#ccc;}*/
</style>
<script>
/*	jQuery.divselect = function(divselectid,inputselectid) { 
var inputselect = $(inputselectid); 
$(divselectid+" cite").click(function(){ 
var ul = $(divselectid+" ul"); 
if(ul.css("display")=="none"){ 
ul.slideDown("fast"); 
}else{ 
ul.slideUp("fast"); 
} 
}); 
$(divselectid+" ul li a").click(function(){ 
var txt = $(this).text(); 
$(divselectid+" cite").html(txt); 
var value = $(this).attr("selectid"); 
inputselect.val(value); 
$(divselectid+" ul").hide(); 
}); 
}; 
$(function(){ 
$.divselect("#divselect","#inputselect"); 
}); */
</script>
<div class="search-box">
<select id="search-type" class="select_sel" onchange="front_search_select(this.value);"><option value="">商品</option><option value="seller" 
<? if($_GET['mod']=='seller') { ?>
selected
<? } ?>
>商家</option></select>
<input type="text" id="search-txt" value="<?=$kw?>"  class="input_h search-box__input"  onkeyup="if (event.keyCode == 13) front_search_request();" 
<? if($_GET['mod']=='seller') { ?>
placeholder="请输入商家名称等"
<? } else { ?>placeholder="请输入商品名称等"
<? } ?>
/>
<input class="search-box__button" type="button" value="搜索" onclick="front_search_request()" />
</div>
<script type="text/javascript">
var front_seller_base = "<? echo logic('url')->create('seller', array('mod' => 'seller', 'kw' => 'KW000WK')); ?>";
var front_product_base = "<? echo logic('url')->create('product', array('kw' => 'KW000WK')); ?>";
var front_search_base = "";//index.php?kw=dd
function front_search_request()
{
	front_search_select($('#search-type').val());
	window.location = front_search_base.replace('KW000WK', encodeURIComponent($('#search-txt').val()));

}
function front_search_select(val)
{
	front_search_base = front_product_base;
	$('#search-txt').attr('placeholder','请输入商品名称等');
	if(val=='seller'){
		front_search_base = front_seller_base;
		$('#search-txt').attr('placeholder','请输入商家名称等');
	}
}
</script>