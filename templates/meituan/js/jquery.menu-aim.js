//增加特殊字符识别版本
(function($) {
    $.fn.menuAim = function(opts) {

        var $menu = $(this),
            activeRow = null,
            mouseLocs = [],
            lastDelayLoc = null,
            timeoutId = null,
            options = $.extend({
                rowSelector: "> li",
                submenuSelector: "*",
                tolerance: 75,  
                enter: $.noop,
                exit: $.noop,
                activate: $.noop,
                deactivate: $.noop
            }, opts);

        var MOUSE_LOCS_TRACKED = 3,  
            DELAY = 300;  

        var mousemoveDocument = function(e) {
                mouseLocs.push({x: e.pageX, y: e.pageY});

                if (mouseLocs.length > MOUSE_LOCS_TRACKED) {
                    mouseLocs.shift();
                }
            };

        var mouseleaveMenu = function() {
                if (timeoutId) {
                    clearTimeout(timeoutId);
                }
            };

        var mouseenterRow = function() {
                if (timeoutId) {
                    clearTimeout(timeoutId);
                }

                options.enter(this);
                possiblyActivate(this);
            },
            mouseleaveRow = function() {
                options.exit(this);
            };

        var activate = function(row) {
                if (row == activeRow) {
                    return;
                }

                if (activeRow) {
                    options.deactivate(activeRow);
                }

                options.activate(row);
                activeRow = row;
            };

        var possiblyActivate = function(row) {
                var delay = activationDelay();

                if (delay) {
                    timeoutId = setTimeout(function() {
                        possiblyActivate(row);
                    }, delay);
                } else {
                    activate(row);
                }
            };


        var activationDelay = function() {
                if (!activeRow || !$(activeRow).is(options.submenuSelector)) {
                    return 0;
                }

                var offset = $menu.offset(),
                    upperRight = {
                        x: offset.left + $menu.outerWidth(),
                        y: offset.top - options.tolerance
                    },
                    lowerRight = {
                        x: offset.left + $menu.outerWidth(),
                        y: offset.top + $menu.outerHeight() + options.tolerance
                    },
                    loc = mouseLocs[mouseLocs.length - 1],
                    prevLoc = mouseLocs[0];

                if (!loc) {
                    return 0;
                }

                if (!prevLoc) {
                    prevLoc = loc;
                }

                if (prevLoc.x < offset.left || prevLoc.x > lowerRight.x ||
                    prevLoc.y < offset.top || prevLoc.y > lowerRight.y) {
                    return 0;
                }

                if (lastDelayLoc &&
                        loc.x == lastDelayLoc.x && loc.y == lastDelayLoc.y) {
                    return 0;
                }

                function slope(a, b) {
                    return (b.y - a.y) / (b.x - a.x);
                };

                var upperSlope = slope(loc, upperRight),
                    lowerSlope = slope(loc, lowerRight),
                    prevUpperSlope = slope(prevLoc, upperRight),
                    prevLowerSlope = slope(prevLoc, lowerRight);

                if (upperSlope < prevUpperSlope &&
                        lowerSlope > prevLowerSlope) {
                    lastDelayLoc = loc;
                    return DELAY;
                }

                lastDelayLoc = null;
                return 0;
            };

        var init = function() {
            $menu
                .mouseleave(mouseleaveMenu)
                .find(options.rowSelector)
                    .mouseenter(mouseenterRow)
                    .mouseleave(mouseleaveRow);

            $(document).mousemove(mousemoveDocument);
        };

        init();
        return this;
    };
})(jQuery);

