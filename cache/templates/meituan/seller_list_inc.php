<style>
.exp .site-fs{
width: 938px;
margin: 0 0 15px;
float: left;
border: 1px solid #ededed;
border-bottom: 1px solid #ccc;
}
.site-fs__sort_w{ display:none;}
.exp .site-fs__sort-t,.exp .site-fs__sort-s a{ border-right:none;}
.exp .site-fs__sort-t{ padding-right:16px;}
</style>
<div class="product_item">
<? if(is_array($seller)) { foreach($seller as $item) { ?>
<div class="t_area_out template4">
<div class="shopList">
<div class="deal-tile__cover">
<? ui('iimager')->seller_single($item['id'], (int) $item['imgs']) ?>
</div>
<div class="deal-tile__title">
<h2><a href="?mod=seller&code=view&id=<?=$item['id']?>" target="_blank" title="<?=$item['sellername']?>"><?=$item['sellername']?></a></h2>
<dl class="shopList_info">
<? if($item['sellerphone']) { ?>
<p><em>联系电话：</em><?=$item['sellerphone']?></p>
<? } ?>

<? if($item['selleraddress']) { ?>
<p><em>详细地址：</em><?=$item['selleraddress']?></p>
<? } ?>

<? if($item['price_avg']) { ?>
<p><em>人均消费：</em>￥<?=$item['price_avg']?>元</p>
<? } ?>
</dl>
</div>
</div>
</div>
<? } } ?>
<div class="product_list_pager"><?=page_moyo()?></div>
</div>
</div>
<div class="site-ms__right">
<?=ui('widget')->load('index_home')?>
</div>
</div>