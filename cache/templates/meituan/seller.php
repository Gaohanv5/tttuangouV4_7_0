<? include $this->TemplateHandler->template("header"); ?>
<div class="site-ms__left"> 
<div class="site-mast__search-result" 
<? if($kw) { ?>
 style="display:block;"
<? } else { ?>style="display:none;"
<? } ?>
>找到“<?=$kw?>”相关的商家信息如下</div>
<? echo ui('loader')->css($this->Module.'.'.$this->Code) ?>
<?=ui('loader')->css('seller')?>
<style>.site-fs__cell-fix_re .site-fs__cell-fixson {width: 938px;}</style>
<div class="site-fs">
<?=logic('city')->place_navigate('seller')?>
<?=ui('catalog')->seller_display()?>
<?=logic('seller')->price_navigate()?>
<?=logic('sort')->seller_navigate()?>
<script type="text/javascript">
var __Timer_lesser_auto_accuracy = <? echo ini('ui.igos.litetimer') ? 'true' : 'false'; ?>;
var __Timer_lesser_worker_max = <? echo (int)ini('ui.igos.litetimer_wm'); ?>;
</script>
</div>
<?=ui('loader')->js('@time.lesser')?>
<? include $this->TemplateHandler->template("seller_list_inc"); ?>
<?=ui('iimager')->single_lazy()?>
<? include $this->TemplateHandler->template("footer"); ?>