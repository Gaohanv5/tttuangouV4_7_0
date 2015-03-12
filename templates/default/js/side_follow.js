//**
//��߸���ģ�� @itom 201212
//**


(function ($, window, document, undefined) {
    var $win = $(window);
    var $document = $(document);
    var FixBox = function (element, options) {
        this.initialize('fixbox', element, options);
    };
    FixBox.prototype = {
        constructor:FixBox,
        initialize:function (type, element, options) {
            var _this = this;
            this.type = type;
            this.$element = $(element);
            this.options = this.options || this.getOptions(options);
            this.winH = $win.height();
            this.winW = $win.width();
            if (this.options.isFixdeHeight) {
                this.fixedBoxH = this.$element.outerHeight(true);
            }
            this.offsetT = this.$element.offset().top;
            this.resizeWindow();
            this.documentH = $document.height();
            $win.bind("resize", function () {
                _this.resizeWindow();
            });
        },

        getOptions:function (options) {
            options = $.extend({}, $.fn[this.type].defaults, this.$element.data(), options || {});

            return options;
        },
        resizeWindow:function () {
            var options = this.options;
            var _this = this;
            this.winH = $win.height();
            this.winW = $win.width();
            if (this.winW >= options.pagewidth) {
                this.doFix();
                $win.unbind("." + options.scrollEventName);
                $win.bind("scroll." + options.scrollEventName, function () {
                    _this.doFix();
                });
            } else {
                $win.unbind("." + options.scrollEventName);
                this.$element.css("position", "static");
            }
        },
        //����
        doFix:function () {
            var $element = this.$element;
            var options = this.options;
            var distanceToBottom = options.distanceToBottom;
            var distanceToTop = options.distanceToTop;
            if (!this.options.isFixdeHeight) {
                this.fixedBoxH = $element.outerHeight(true);
            }
            var fixedBoxH = this.fixedBoxH;
            var offsetT = this.offsetT;
            var fixedBoxPositionB = fixedBoxH + this.offsetT;
            var winH = this.winH;
            if (!options.isFixdeDocHeight) {
                this.documentH = $document.height();
            }
            var documentH = this.documentH;
            if (fixedBoxPositionB + distanceToBottom - options.threshold >= documentH) {
                return;
            }
            var scrollNum = fixedBoxPositionB - winH;
            var winST = $win.scrollTop();

            if (fixedBoxH < (winH - distanceToTop)) {
                if (winST > offsetT) {
                    if (winST >= ( documentH - distanceToBottom - fixedBoxH)) {
                        $element.css({
                            "position":"fixed",
                            "top":-(winST + distanceToBottom + fixedBoxH - documentH)
                        });
                        //}
                    } else {
                        $element.css({
                            "position":"fixed",
                            "top":distanceToTop							
                        });
                    }
                } else {
                    $element.css("position", "static");
                }
            }
            else {
                if (winST > scrollNum) {
                    if (winST > ( documentH - winH - distanceToBottom)) {
                        $element.css({
                            "position":"fixed",
                            "top":-(winST + distanceToBottom + fixedBoxH - documentH)
                        });
                    } else {
                        $element.css({
                            "position":"fixed",
                            "top":winH - fixedBoxH
                        });
                    }
                } else {
                    $element.css("position", "static");
                }
            }
        }
    };
    $.fn.fixbox = function (option) {
        var argumentsAry = [];
        for (var i = 0, len = arguments.length; i < len; i++) {
            argumentsAry.push(arguments[i]);
        }
        var newarg = argumentsAry.slice(1);
        return this.each(function () {
            var $this = $(this),
                data = $this.data('fixbox'),
                options = typeof option == 'object' && option;
            if (!data) {
                data = new FixBox(this, options);
                $this.data('fixbox', data);
            }
            if (typeof argumentsAry[0] == 'string') {
                data[argumentsAry[0]].apply(data, newarg);
            }
        });
    };
    $.fn.fixbox.Constructor = FixBox;

    $.fn.fixbox.defaults = {
        distanceToTop:0, 
        distanceToBottom:0,
        isFixdeHeight:true,
        isFixdeDocHeight:false,
        pagewidth:960,
        threshold:0, 
        scrollEventName:"followScroll"
    };
})(window.jQuery, window, document);