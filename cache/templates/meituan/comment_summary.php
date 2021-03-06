<?=ui('loader')->css('comment')?>
<?=ui('loader')->js('@comment.ops')?>
<div class="user-reviews">
<div class="overview">
<div class="overview-head">
<div class="overview-title">消费评价</div>
<div class="overview-feedback">
<? if($i_buyed) { ?>
<a href="?mod=me&code=order&pid=<?=$product_id?>">我要评价</a>
<? } ?>
</div>
</div>
<div class="rating-area total-detail">
<div class="total-score">本产品用户评价：<span><?=$summary['average']?></span>分</div>
</div>
<div class="rating-area">
<ul class="comment-rating">
<li style="z-index:0; display: list-item; width:<? echo $summary['average'] * 20; ?>%;"></li>
</ul>
</div>
<div class="rating-area">
<div>已有<strong><?=$summary['count']?></strong>条评价</div>
</div>
<div style="clear:both"></div>
</div>
<div class="comment-list" id="deal_comment">
<div id="sp_other">
<div class="comment-sort"><h4>【最新评价内容】</h4></div>
<dl class="comment-txt">
<? if(is_array($comments)) { foreach($comments as $comment) { ?>
<dd>
<p><?=$comment['content']?></p>
<? if($comment['img']) { ?>
<script>
$(function(){ 
$('.deal_comment_img li').hover(function() {
$(this).addClass('on');
var wl = $(this).find('img').attr('width');
if (wl < 190) {
$(this).find('.in').css('left', '0')
} else {
$(this).find('.in').css('left', -wl / 4)
}
},
function() {
$(this).animate({
height: "100px"
},
100).removeClass('on');
$(this).find('.in').css('left', '0')
});
})
</script>
<ul class="deal_comment_img">
<li>
<div class="in">
<img src="<? echo imager($comment['img'], IMG_Original); ?>">
</div>
</li>
</ul>
<? } ?>

<? if($comment['reply']) { ?>
<div class="replybg"><p class="reply">商家回复：<?=$comment['reply']?></p></div>
<? } ?>
<div class="comment-info">
<ul class="comment-info__rating">
<li style="z-index:0; display: list-item; width:<? echo $comment['score'] * 20; ?>%"></li>
</ul>
<span class="comment-info__user-time">
<span class="name">
<? if($comment['anonymous']) { ?>
<? echo substr_replace($comment['user_name'],'**',2,2); ?>
<? } else { ?><?=$comment['user_name']?>
<? } ?>
</span>
<span class="date"><? echo date('Y-m-d H:i:s', $comment['timestamp_update']); ?></span>
<? if($comment['status'] != 'approved') { ?>
<span class="status">
<? if($comment['status']=='auditing') { ?>
审核中
<? } ?>

<? if($comment['status']=='denied') { ?>
未通过
<? } ?>
</span>
<? } ?>
</span>
</div>
</dd>
<? } } ?>
</dl>
<div style=" padding:15px 15px 0 15px;" class="page product_list_pager">
<?=page_moyo()?>
</div>
<input type="hidden" id="nowpage" value="1" />
<div class="c"></div>
</div>
</div>
</div>
<script type="text/javascript">
var commentGoodsId = '7454706';
// if document.ready
//getGoodsComment(commentGoodsId, 1);
// end if
$('#_comment_page a').live("click", function(){
var classn  = $(this).attr('class');
var nowpage = parseInt($("#nowpage").val());
var nowtext = parseInt($(this).text());
if((classn.indexOf('pageup-dis') != '-1') || (classn.indexOf('pagedown-dis') != '-1')) return false;
if(classn.indexOf('pageup') != '-1' && isNaN(nowtext))
{
var page = nowpage - 1;
}
else if(classn.indexOf('pagedown') != '-1' && isNaN(nowtext))
{
var page = nowpage + 1;
}
else
{
var page = nowtext;
}
$("#nowpage").val(page);
$("html,body").animate({scrollTop: $("#deal_comment").offset().top}, 500);
getGoodsComment(commentGoodsId, page);
});
//get comment
function getGoodsComment(goodsId, page) {        
var _url = '/ajax/getComment.php?act=comment';        
$(function(){
$.ajax({
type: "POST",
dataType:'json',
data: "goodsId="+goodsId + "&page=" + page,
url:_url,
beforeSend:function(){$('#_comment_info').html('加载中...');},
success: function(data){
if(data.code == 1) {
$('#_comment_info').html(data.html);
$('#_comment_page').html(data.page);
}
}
});
})
}
</script>