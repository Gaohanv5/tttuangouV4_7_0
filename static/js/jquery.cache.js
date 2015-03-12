/*
 * Cache Manager 1.0 Beta, jQuery plugin
 *
 * (C) 2010 Moyo
 * http://moyo.im
 *
 * Licensed under the MIT License
 */



jQuery.cache.__storage = new Array();
jQuery.cache.check = function(key)
{
	if (this.__storage[key] == undefined)
	{
		return false;
	}
	return true;
};
jQuery.cache.set = function(key, val)
{
	this.__storage[key] = val;
};
jQuery.cache.get = function(key)
{
	return this.__storage[key];
};
