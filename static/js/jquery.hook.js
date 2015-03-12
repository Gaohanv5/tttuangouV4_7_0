/*
 * Event hook applys 1.0 Beta, jQuery plugin
 *
 * (C) 2010 Moyo
 * http://moyo.im
 *
 * Licensed under the MIT License
 */

jQuery.hook = {
	__hook_list: new Array(),
	__sid_list: new Array(),
	__register: function(key)
	{
		var hook = this.__hook_list[key];
		if (hook == undefined)
		{
			this.__hook_list[key] = new Array();
		}
	},
	__rand_key: function()
	{
		var salt = '0123456789qwertyuioplkjhgfdsazxcvbnm';
		var str = 'id_';
		for(var i=0; i<6; i++)
		{
			str += salt.charAt(Math.ceil(Math.random()*100000000)%salt.length);
		}
		return str;
	},
	add: function(key, callback)
	{
		this.__register(key);
		var sid = this.__rand_key();
		this.__hook_list[key][sid] = callback;
		this.__sid_list[sid] = key;
		return sid;
	},
	del: function(sid)
	{
		var key = this.__sid_list[sid];
		if (key == undefined) return false;
		delete this.__sid_list[sid];
		delete this.__hook_list[key][sid];
		return true;
	},
	call: function(key, options)
	{
		this.__register(key);
		var loops = this.__hook_list[key];
		for (sid in loops)
		{
			if (loops[sid] == undefined) continue;
			try
			{
				loops[sid](options);
			}
			catch(e){}
		}
	},
	callEx:function()
	{
		var key = arguments[0] ? arguments[0] : false; 
		this.__register(key);
		var loops = this.__hook_list[key];
		for (sid in loops)
		{
			if (loops[sid] == undefined) continue;
			try
			{
				if(arguments.length == 2)
					loops[sid](arguments[1]);
				else if(arguments.length == 3)
					loops[sid](arguments[1],arguments[2]);
				else if(arguments.length == 4)
					loops[sid](arguments[1],arguments[2],arguments[3]);
			}
			catch(e){}
		}
	}
};
