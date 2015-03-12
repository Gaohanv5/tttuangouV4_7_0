/** * @copyright (C)2014 Cenwor Inc. * @author Cenwor <www.cenwor.com> * @package js * @name catalog.mgr.ajax.js * @date 2014-10-30 10:42:14 */ var __CATA_MGR_DIALOG = null;

function __catalog_add(parentID)
{
	if (!parentID) parentID = 0;
	var opURL = 'admin.php?mod=catalog&code=add&op=ajax&parent='+parentID.toString()+'&~iiframe=yes';
	__CATA_MGR_DIALOG = $.browser.msie ? window.open(opURL, 'cataMgr', 'width=500,height=220,toolbar=no,menubar=no,location=no,scrollbars=yes,status=no,resizable=no,left='+(screen.width-500)/2+',top='+(screen.height-260)/2+'') : art.dialog.open(opURL, {title:'分类添加'});
}

function __catalog_add_finish(id)
{
	if (id < 0)
	{
		$.notify.failed('添加失败，请确认您填写了名称和有效的短标记（字母a到z，数字0到9），并且短标记没有被其他分类使用！');
	}
	else
	{
		$.hook.call('catalog.add.finish', id);
	}
	__CATA_MGR_DIALOG.close();
}

function __catalog_del(id, callback)
{
	if (!confirm('确认删除吗？\n\n1 删除主分类时会删除相关的子分类！\n\n2 子分类被删除时，其下的产品会变成无分类状态！')) return;
	$.get('admin.php?mod=catalog&code=del&op=ajax&id='+id.toString()+$.rnd.stamp(), function(data){
		if (data == 'ok')
		{
			callback(id);
		}
		else
		{
			$.notify.failed('删除失败！');
		}
	});
}

function __catalog_hot(id) {
	var opURL = 'admin.php?mod=catalog&code=hot&op=ajax&id='+id+'&~iiframe=yes';
	__CATA_MGR_DIALOG = $.browser.msie ? window.open(opURL, 'cataMgr', 'width=500,height=220,toolbar=no,menubar=no,location=no,scrollbars=yes,status=no,resizable=no,left='+(screen.width-500)/2+',top='+(screen.height-260)/2+'') : art.dialog.open(opURL, {title:'热门设置'});
}

function __catalog_hot_finish(idv, hotv) {
	$.hook.call('catalog.add.finish', idv);
	
	__CATA_MGR_DIALOG.close();
}
