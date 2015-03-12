<div class="site-mast_recentlist">
<a class="recent" href="#">最近浏览<b></b></a>
<ul class="recentlist">
<? if(is_array($products)) { foreach($products as $row) { ?>
<li>
<a target="_blank" href="index.php?view=<?=$row['id']?>">
<div class="image"><img src="<?=$row['img_src']?>"></div>
<div class="right"><h4><?=$row['name']?></h4><em class="price">&yen;<?=$row['nowprice']?></em> <em class="old-price">&yen;<?=$row['price']?></em></div> 
</a>                            
</li>
<? } } ?>
<? if($products) { ?>
<li class="recent_view_clean"><a href='javascript:;' onclick="recent_view_clean();return false;">清空浏览记录</a></li>
<? } else { ?><li>暂无浏览记录</li>
<? } ?>
</ul>
</div>
<script>
$(".site-mast_recentlist").mouseover(function() { $(".recentlist").show();$(".recent").addClass("recent_active");$(".recentlist").css("right","-2px");}).mouseout(function() { $(".recentlist").hide(); $(".recent").removeClass("recent_active");$(".recentlist").css("right","-1px");});
function recent_view_clean() {
$(".recentlist").html('');
$.get('index.php?mod=index&code=recent_view&op=clean');
}
</script>