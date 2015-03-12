/**
 * ATTRS.MGR
 * $Id$
 */

/**
 * LOADER OPS
 */

function attrs_load_data(pid)
{
	if (pid)
	{
		$('#attrs-form-area').html('正在加载...');
		$.getJSON('admin.php?mod=attrs&code=map&id='+pid, function(data) {
			$('#attrs-form-area').html('');
			$.each(data, function(i, cat) {
				attrs_cache('c', cat.id, {ops : 'remote', catId : cat.id, catName : cat.name, catRequired : cat.required});
				attrs_render_cat(cat.id);
				$.each(cat.attrs, function(ii, attr) {
					attrs_cache('a', attr.id, {ops : 'remote', catId : cat.id, attrId : attr.id, attrName : attr.name, priceMoves : attr.price_moves, attrBinding : attr.binding});
					attrs_render_item(attr.id);
				});
			});
		});
	}
}

/**
 * AJAX OPS
 */

function attrs_rpc(action, data, iffailed, callback)
{
	// get formhash
	data['FORMHASH'] = $('#productIfoForm input[name=FORMHASH]').val();
	// calling
	$.notify.loading('操作正在同步至服务端...');
	$.post('admin.php?mod=attrs&code=ops&op='+action, data, function(result) {
		$.notify.loading();
		var resultNx = parseInt(result);
		if (resultNx > 0)
		{
			callback(resultNx);
		}
		else
		{
			$.notify.failed(iffailed);
		}
	});
}

/**
 * CAT OPS
 */

function attrs_append_cat(ops, pid)
{
	if (ops == '@auto')
	{
		ops = pid == '' ? 'local' : 'remote';
	}
	attrs_call_dialog('创建属性分类', 'attr-tpl-dialog-cat', {catName : ''}, function() {
		var catNameInput = $('#attrs-input-cat-name').val();
		var catRequiredInput = $('#attrs-input-cat-required').val();
		attrs_cat_append(ops, pid, catNameInput, catRequiredInput, function(catId) {
			attrs_render_cat(catId);
			attrs_dialog_close();
		});
	});
}

function attrs_modify_cat(ops, cid)
{
	attrs_call_dialog('编辑属性分类', 'attr-tpl-dialog-cat', attrs_cache('c', cid), function() {
		var catNameInput = $('#attrs-input-cat-name').val();
		var catRequiredInput = $('#attrs-input-cat-required').val();
		attrs_cat_modify(ops, cid, catNameInput, catRequiredInput, function() {
			$('#attrs-cat-name-'+cid).text(catNameInput);
			attrs_dialog_close();
		});
	});
}

function attrs_remove_cat(ops, cid)
{
	if (confirm('确认删除吗？（删除分类时会同步删除其中所有的属性）'))
	{
		attrs_cat_remove(ops, cid, function() {
			$('#attrs-area-'+cid).remove();
		});
	}
}

/**
 * ITEM OPS
 */

function attrs_append_item(ops, cid)
{
	attrs_call_dialog('创建属性标签', 'attr-tpl-dialog-item', {attrName : '', priceMoves : '0'}, function() {
		var nameInput = $('#attrs-input-item-name').val();
		var priceMovesInput = $('#attrs-input-item-pricemoves').val();
		var bindingInput = $('#attrs-input-item-binding').val();
		attrs_item_append(ops, cid, nameInput, priceMovesInput, bindingInput, function(attrId) {
			attrs_render_item(attrId)
			attrs_dialog_close();
		});
	});
}

function attrs_modify_item(ops, aid)
{
	attrs_call_dialog('编辑属性标签', 'attr-tpl-dialog-item', attrs_cache('a', aid), function() {
		var nameInput = $('#attrs-input-item-name').val();
		var priceMovesInput = $('#attrs-input-item-pricemoves').val();
		var bindingInput = $('#attrs-input-item-binding').val();
		attrs_item_modify(ops, aid, nameInput, priceMovesInput, bindingInput, function() {
			$('#attrs-item-'+aid).val(nameInput);
			attrs_dialog_close();
		});
	});
}

function attrs_remove_item(ops, aid)
{
	if (confirm('确认删除吗？'))
	{
		attrs_item_remove(ops, aid, function() {
			$('#attrs-item-'+aid).remove();
			attrs_dialog_close();
		});
	}
}

/**
 * DATA OPS
 */

/**
 * DATA CAT OPS
 */

function attrs_cat_append(ops, pid, name, required, callback)
{
	var cat = {ops : ops, pid : pid, catName : name, catRequired : required};
	if (ops == 'local')
	{
		cat['catId'] = attrs_rnd_string();
		// write
		attrs_cache('c', cat['catId'], cat, 'cat', ops);
		// callback
		callback(cat['catId']);
	}
	if (ops == 'remote')
	{
		attrs_rpc('cat_append', cat, '属性分类添加失败！', function(id) {
			cat['catId'] = id;
			// write
			attrs_cache('c', cat['catId'], cat, 'cat', ops);
			// callback
			callback(cat['catId']);
		});
	}
}

function attrs_cat_modify(ops, cid, name, required, callback)
{
	var cat = attrs_cache('c', cid);
	if (ops == 'local')
	{
		cat['catName'] = name;
		cat['catRequired'] = required;
		// write
		attrs_cache('c', cid, cat, 'cat', ops);
		// callback
		callback();
	}
	if (ops == 'remote')
	{
		cat['catName'] = name;
		cat['catRequired'] = required;
		attrs_rpc('cat_modify', cat, '属性分类修改失败！', function(eff) {
			// write
			attrs_cache('c', cid, cat, 'cat', ops);
			// callback
			callback();
		});
	}
}

function attrs_cat_remove(ops, cid, callback)
{
	if (ops == 'local')
	{
		// write
		attrs_cache('c', cid, null, 'cat', ops);
		// callback
		callback();
	}
	if (ops == 'remote')
	{
		attrs_rpc('cat_remove', {catId : cid}, '属性分类删除失败！', function(eff) {
			// write
			attrs_cache('c', cid, null, 'cat', ops);
			// callback
			callback();
		});
	}
}

/**
 * DATA ITEM OPS
 */

function attrs_item_append(ops, cid, name, priceMoves, binding, callback)
{
	var attr = {ops : ops, catId : cid, attrName : name, priceMoves : priceMoves, attrBinding : binding};
	if (ops == 'local')
	{
		attr['attrId'] = attrs_rnd_string();
		// write
		attrs_cache('a', attr['attrId'], attr, 'attr', ops);
		// callback
		callback(attr['attrId']);
	}
	if (ops == 'remote')
	{
		attrs_rpc('item_append', attr, '属性标签添加失败！', function(id) {
			attr['attrId'] = id;
			// write
			attrs_cache('a', attr['attrId'], attr, 'attr', ops);
			// callback
			callback(attr['attrId']);
		});
	}
}

function attrs_item_modify(ops, aid, name, priceMoves, binding, callback)
{
	var attr = attrs_cache('a', aid);
	if (ops == 'local')
	{
		attr['attrName'] = name;
		attr['priceMoves'] = priceMoves;
		attr['attrBinding'] = binding;
		// write
		attrs_cache('a', attr['attrId'], attr, 'attr', ops);
		// callback
		callback();
	}
	if (ops == 'remote')
	{
		attr['attrName'] = name;
		attr['priceMoves'] = priceMoves;
		attr['attrBinding'] = binding;
		attrs_rpc('item_modify', attr, '属性标签修改失败！', function(eff) {
			// write
			attrs_cache('a', attr['attrId'], attr, 'attr', ops);
			// callback
			callback();
		});
	}
}

function attrs_item_remove(ops, aid, callback)
{
	if (ops == 'local')
	{
		// write
		attrs_cache('a', aid, null, 'attr', ops);
		// callback
		callback();
	}
	if (ops == 'remote')
	{
		attrs_rpc('item_remove', {attrId : aid}, '属性标签删除失败！', function(eff) {
			// write
			attrs_cache('a', aid, null, 'attr', ops);
			// callback
			callback();
		});
	}
}

/**
 * RENDER OPS
 */

function attrs_render_cat(cid)
{
	var cat = attrs_cache('c', cid);
	$('#attrs-append-pox').before(attrs_template_render('attr-tpl-cat-fw', {ops : cat['ops'], catId : cat['catId'], catName : cat['catName']}));
}

function attrs_render_item(aid)
{
	var attr = attrs_cache('a', aid);
	$('#attrs-ops-pox-'+attr['catId']).before(attrs_template_render('attr-tpl-item-value', {ops : attr['ops'], catId : attr['catId'], attrId : attr['attrId'], attrName : attr['attrName'], priceMoves : attr['priceMoves']}));
}

/**
 * DIALOG OPS
 */

var attrs_dialog_global = null;

function attrs_call_dialog(title, tplid, data, callback)
{
	attrs_dialog_global = art.dialog({
		title: title,
		content: attrs_template_render(tplid, data),
		yesFn: function() {
			callback();
		}
	});
}

function attrs_dialog_close()
{
	if (attrs_dialog_global)
	{
		attrs_dialog_global.close();
	}
}

/**
 * UTIL TOOLS
 */

function attrs_template_render(tplid, data)
{
	var tplHtml = $('#'+tplid).html();
	$.each(data, function(k, v){
		if (k == 'catRequired' || k == 'attrBinding')
		{
			tplHtml = tplHtml.replace(new RegExp("!!selected-"+v+"!!", "ig"), 'selected="selected"');
		}
		else
		{
			tplHtml = tplHtml.replace(new RegExp("!!"+k+"!!", "ig"), v);
		}
	});
	return tplHtml;
}

function attrs_rnd_string()
{
	var salt = '0123456789qwertyuioplkjhgfdsazxcvbnm';
	var str = '';
	for(var i=0; i<6; i++)
	{
		str += salt.charAt(Math.ceil(Math.random()*100000000)%salt.length);
	}
	return str;
}

/**
 * CACHE OPS
 */

var attrs_cache_global = {'a' : {}, 'c' : {}};

function attrs_cache(domain, ckey, cval, type, ops)
{
	if (cval === undefined)
	{
		// read
		return attrs_cache_global[domain][ckey];
	}
	else
	{
		// write
		if (ops == 'local')
		{
			// element ops
			if (cval === null)
			{
				if (type == 'cat')
				{
					attrs_form_delete(ckey, ['-nm', '-rq']);
				}
				if (type == 'attr')
				{
					attrs_form_delete(ckey, ['-nm', '-pm', '-bd']);
				}
			}
			else
			{
				if (type == 'cat')
				{
					attrs_form_update(ckey+'-nm', 'oocix-cat['+ckey+'][name]', cval.catName);
					attrs_form_update(ckey+'-rq', 'oocix-cat['+ckey+'][required]', cval.catRequired);
				}
				if (type == 'attr')
				{
					attrs_form_update(ckey+'-nm', 'oocix-attr['+cval.catId+']['+ckey+'][name]', cval.attrName);
					attrs_form_update(ckey+'-pm', 'oocix-attr['+cval.catId+']['+ckey+'][pricemoves]', cval.priceMoves);
					attrs_form_update(ckey+'-bd', 'oocix-attr['+cval.catId+']['+ckey+'][binding]', cval.attrBinding);
				}
			}
		}
		if (cval === null)
		{
			// delete
			delete attrs_cache_global[domain][ckey];
		}
		else
		{
			attrs_cache_global[domain][ckey] = cval;
		}
		return cval;
	}
}

/**
 * DOM OPS
 */

function attrs_form_update(cid, name, value)
{
	var eid = 'attrs-field-'+cid;
	var ele = $('#'+eid);
	if (ele.length > 0)
	{
		ele.val(value);
	}
	else
	{
		$('#attrs-form-area').append('<input id="'+eid+'" type="hidden" name="'+name+'" value="'+value+'" />');
	}
}

function attrs_form_delete(cid, exts)
{
	var dops = [];
	if (exts)
	{
		for (en in exts)
		{
			dops.push(cid + exts[en]);
		}
	}
	else
	{
		dops.push(cid);
	}
	for (ee in dops)
	{
		var eid = 'attrs-field-' + dops[ee];
		var ele = $('#'+eid);
		if (ele.length > 0)
		{
			ele.remove();
		}
	}
}

/**
 * HELP OPS
 */

function attrs_help()
{
	art.dialog({
		title: '帮助手册',
		icon: 'question',
		lock: true,
		content: $('#attr-tpl-help').html(),
		yesText: '知道了',
		yesFn: true
	});
}

function attrs_cat_help()
{
	art.dialog({
		title: '帮助手册',
		icon: 'question',
		lock: true,
		content: $('#attr-cat-tpl-help').html(),
		yesText: '知道了',
		yesFn: true
	});
}