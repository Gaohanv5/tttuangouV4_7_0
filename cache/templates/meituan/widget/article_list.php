<div class="t_area_out">
<h1>最新资讯</h1>
<div class="t_area_in">
<ul>
<? $articles = logic('article')->get_list(10) ?>
<? if(is_array($articles)) { foreach($articles as $article) { ?>
<li class="information">
<a class="info_title" href="?mod=article&code=view&id=<?=$article['id']?>" target="_blank"><?=$article['title']?></a>
<div style="clear:both;"></div>
</li>
<? } } ?>
</div>
</div>