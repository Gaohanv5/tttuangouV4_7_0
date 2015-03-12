</td> </tr> </table> <div class="site_cop">
<? $this->ob_gzhand1er(); ?>
</div> 
<? echo ui('loader')->js('#admin/js/'.$this->Module.'.'.$this->Code) ?>
 <?=ui('loader')->js('#admin/js/table.hover')?> <?=ui('loader')->js('#admin/js/footer')?> <?=ui('pingfore')->html()?>
<?=$GLOBALS['iframe']?>
</body> </html>
<? $this->MemberHandler->UpdateSessions(); ?>