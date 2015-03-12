var Cookies={
// utility function to retrieve an expiration date in proper
// format; pass three integer parameters for the number of days, hours,
// and minutes from now you want the cookie to expire (or negative
// values for a past date); all three parameters are required,
// so use zeros where appropriate
getExpDate:function(days, hours, minutes) {
    var expDate = new Date( );
    if (typeof days == "number" && typeof hours == "number" && 
        typeof hours == "number") {
        expDate.setDate(expDate.getDate( ) + parseInt(days));
        expDate.setHours(expDate.getHours( ) + parseInt(hours));
        expDate.setMinutes(expDate.getMinutes( ) + parseInt(minutes));
        return expDate.toGMTString( );
    }
},  
// utility function called by getCookie( )
getCookieVal:function(offset) {
    var endstr = document.cookie.indexOf (";", offset);
    if (endstr == -1) {
        endstr = document.cookie.length;
    }
    return unescape(document.cookie.substring(offset, endstr));
},
// primary function to retrieve cookie by name
getCookie:function(name) {
    var arg = name + "=";
    var alen = arg.length;
    var clen = document.cookie.length;
    var i = 0;
    while (i < clen) {
        var j = i + alen;
        if (document.cookie.substring(i, j) == arg) {
            return Cookies.getCookieVal(j);
        }
        i = document.cookie.indexOf(" ", i) + 1;
        if (i == 0) break; 
    }
    return "";
},
getCookies:function(){
	 _Cookie = new Array();
	 if(document.cookie.indexOf(";")!=-1){
          var _sp,_name,_tp,_tars,_tarslength; 
          var _item=document.cookie.split("; "); 
          var _itemlength=_item.length; 
          for(i=0;i<_itemlength;i++){
          	_sp = _item[i].split("=");
          	_name=_sp[0];
          	_value =_sp[1];
          	_coo = new Array();
          	_coo['name']=_name;
          	_coo['value']=_value;
          	_Cookie.push(_coo);
          }
     } 
     return _Cookie;  	
},
// store cookie value with optional details as needed
setCookie:function(name, value, expires, path, domain, secure) {
	if(expires)
	{
		expires = new Date((new Date()).getTime() + expires * 1000);
		expires=expires.toGMTString();
	}
    document.cookie = name + "=" + escape (value) +
        ((expires) ? "; expires=" + expires : "") +
        ((path) ? "; path=" + path : "") +
        ((domain) ? "; domain=" + domain : "") +
        ((secure) ? "; secure" : "");
},
// remove the cookie by setting ancient expiration date
deleteCookie:function(name,path,domain) {
    if (Cookies.getCookie(name)) {
        document.cookie = name + "=" +
            ((path) ? "; path=" + path : "") +
            ((domain) ? "; domain=" + domain : "") +
            "; expires=Thu, 01-Jan-70 00:00:01 GMT";
    }
},
clearCookie:function(){
	cookies = Cookies.getCookies();
	for(i=0;i<cookies.length;i++){
		Cookies.deleteCookie(cookies[i]['name']);
	}
},
getCookieString:function(){
	return document.cookie;	
}
}