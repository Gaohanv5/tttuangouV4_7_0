<div class="t_area_out ">
<h1>在线问答</h1>
<div class="t_area_in">
<a target="_blank" href="?mod=list&code=ask#q_form">我要提问</a>
|
<a target="_blank" href="?mod=list&code=ask">查看全部</a>
<ul>
<? if(is_array(logic('misc')->AskList(6))) { foreach(logic('misc')->AskList(6) as $i => $value) { ?>
<li class="ask"><a target="_blank" class="txt13" href="?mod=list&code=ask#id<?=$value['id']?>"><?=$value['content']?></a></li>
<? } } ?>
</ul>
</div>
</div>