if (document.all && window.external) {
	// IE
	document.getElementsByClassName = function() {
		var tTagName = "*";
		if (arguments.length > 1) {
			tTagName = arguments[1];
		}
		if (arguments.length > 2) {
			var pObj = arguments[2]
		} else {
			var pObj = document;
		}
		var objArr = pObj.getElementsByTagName(tTagName);
		var tRObj = new Array();
		for ( var i = 0; i < objArr.length; i++) {
			if (objArr[i].className == arguments[0]) {
				tRObj.push(objArr[i]);
			}
		}
		return tRObj;
	}
}

function sub2next()
{
	var cc = document.getElementsByClassName('nw').length;
	if (cc > 0)
	{
		alert('环境检查不通过，无法进行下一步操作！');
		return;
	}
	window.location = '?mod=install&code=dbs';
}
