<div class="t_area_out ">
<? $cpid = isset($_GET['view']) ? $_GET['view'] : -1; ?>
<h1>其他精彩<?=TUANGOU_STR?></h1>
<div class="t_area_in">
<ul class="product_list">
<? $one_product =  logic('product')->GetOne($cpid); ?>
<? if($one_product) { ?>
<? $product_other_list = logic('product')->GetOtherList($one_product['city'], $one_product['category'], $one_product['id']); ?>
<? if(is_array($product_other_list)) { foreach($product_other_list as $i => $product) { ?>
<li>
<p class="pl_img"><a href="?view=<?=$product['id']?>"><img src="<? echo imager($product['imgs']['0'], IMG_Small);; ?>" width="175"/></a></p>
<p class="name"><a href="?view=<?=$product['id']?>"><?=$product['name']?></a></p>
<div class="shop">
<div class="pr">
<font class="price">&yen;<?=$product['nowprice']?></font>
<font class="markprice">&nbsp;市场价：&yen;<?=$product['price']?></font>
</div>
</div>
</li>
<? } } ?>
<? } ?>
</ul>
</div>
</div>