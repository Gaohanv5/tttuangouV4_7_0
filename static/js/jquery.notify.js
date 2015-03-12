/*
 * Notify UI Driver 1.0 beta, jQuery plugin
 *
 * (C) 2010 Moyo
 * http://moyo.im
 *
 * Licensed under the MIT License
 */

jQuery.notify = {
	success: function(text)
	{
		artDialog ? art.dialog.success(text) : alert(text);
	},
	failed: function(text)
	{
		artDialog ? art.dialog.failed(text) : alert(text);
	},
	alert: function(text)
	{
		artDialog ? art.dialog.alert(text) : alert(text);
	},
	show: function(text)
	{
		artDialog ? art.dialog.tips(text) : alert(text);
	},
    notice: function(_title, _content, _icon, _time, _width)
    {
        // only under artDialog
        var option = {
            title: _title,
            content: _content,
            icon: _icon || 'face-smile',
            time: _time || 5,
            width: _width || 220
        };
        art.dialog.notice(option);
    },
    __loadingImage: 'templates/admin/images/loading.gif',
    __loadingBox: null,
    loading: function(content)
    {
        if (typeof(content) == 'undefined' || !content)
        {
            this.__loadingBox != null && this.__loadingBox.close();
            return;
        }
        this.__loadingBox = art.dialog({
            id: 'LoadingBox',
            title: false,
            noFn: false,
            fixed: true
        }).content('<div style="padding: 0 1em;"><img src="' + this.__loadingImage + '" style="vertical-align:middle;margin-right:10px;" />' + content + '</div>');
    }
};

// preload __loadingImage
var__preload_loadingImager = new Image();
var__preload_loadingImager.src = $.notify.__loadingImage;