/**
 * Trail Format
 *
 * @package    format_trail
 * @copyright  &copy; 2019 Jose Wilson  in respect to modifications of grid format.
 * @author     &copy; 2012 G J Barnard in respect to modifications of standard topics format.
 * @author     G J Barnard - {@link http://about.me/gjbarnard} and
 *                           {@link http://moodle.org/user/profile.php?id=442195}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @license    Chartist is MIT licenced: https://raw.githubusercontent.com/gionkunz/chartist-js/master/LICENSE-MIT
 */


/* jshint ignore:start */
define(['jquery', 'core/log'], function($, log) {

    "use strict"; // jshint ;_;

    /* ========================================================================
     * Bootstrap: transition.js v3.3.7
     * http://getbootstrap.com/javascript/#transitions
     * ========================================================================
     * Copyright 2011-2016 Twitter, Inc.
     * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
     * ======================================================================== */

    // CSS TRANSITION SUPPORT (Shoutout: http://www.modernizr.com/)
    // ============================================================
    function transitionEnd() {
        var el = document.createElement('bootstrap');

        var transEndEventNames = {
            WebkitTransition: 'webkitTransitionEnd',
            MozTransition: 'transitionend',
            OTransition: 'oTransitionEnd otransitionend',
            transition: 'transitionend'
        };

        for (var name in transEndEventNames) {
            if (el.style[name] !== undefined) {
                return {end: transEndEventNames[name]};
            }
        }

        return false;
    }

    // http://blog.alexmaccaw.com/css-transitions
    $.fn.emulateTransitionEnd = function(duration) {
        var called = false;
        var $el = this;
        $(this).one('bsTransitionEnd', function() {
            called = true;
        });
        var callback = function() {
            if (!called) {
                $($el).trigger($.support.transition.end);
            }
        };
        setTimeout(callback, duration);
        return this;
    };

    $(function() {
        $.support.transition = transitionEnd();

        if (!$.support.transition) {
            return;
        }

        $.event.special.bsTransitionEnd = {
            bindType: $.support.transition.end,
            delegateType: $.support.transition.end,
            handle: function(e) {
                if ($(e.target).is(this)) {
                    return e.handleObj.handler.apply(this, arguments);
                }
            }
        };
    });

    /* ========================================================================
     * Bootstrap: tooltip.js v3.3.7
     * http://getbootstrap.com/javascript/#tooltip
     * Inspired by the original jQuery.tipsy by Jason Frame
     * ========================================================================
     * Copyright 2011-2016 Twitter, Inc.
     * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
     * ======================================================================== */

    // TOOLTIP PUBLIC CLASS DEFINITION
    // ===============================

    var TrailTooltip = function(element, options) {
        this.type = null;
        this.options = null;
        this.enabled = null;
        this.timeout = null;
        this.hoverState = null;
        this.$element = null;
        this.inState = null;

        this.init('trailtooltip', element, options);
    };

    TrailTooltip.VERSION = '3.3.7';

    TrailTooltip.TRANSITION_DURATION = 150;

    TrailTooltip.DEFAULTS = {
        animation: true,
        placement: 'top',
        selector: false,
        template: '<div class="tooltip" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>',
        trigger: 'hover focus',
        title: '',
        delay: 0,
        html: false,
        container: false,
        viewport: {
            selector: 'body',
            padding: 0
        }
    };

    TrailTooltip.prototype.init = function(type, element, options) {
        this.enabled = true;
        this.type = type;
        this.$element = $(element);
        this.options = this.getOptions(options);
        this.$viewport = this.options.viewport
                && $($.isFunction(this.options.viewport)
                        ? this.options.viewport.call(this, this.$element)
                        : (this.options.viewport.selector
                                || this.options.viewport));
        this.inState = {click: false, hover: false, focus: false};

        if (this.$element[0] instanceof document.constructor && !this.options.selector) {
            throw new Error('`selector` option must be specified when initializing '
                    + this.type + ' on the window.document object!');
        }

        var triggers = this.options.trigger.split(' ');

        for (var i = triggers.length; i--;) {
            var trigger = triggers[i];

            if (trigger == 'click') {
                this.$element.on('click.' + this.type, this.options.selector, $.proxy(this.toggle, this));
            } else if (trigger != 'manual') {
                var eventIn = trigger == 'hover' ? 'mouseenter' : 'focusin';
                var eventOut = trigger == 'hover' ? 'mouseleave' : 'focusout';

                this.$element.on(eventIn + '.' + this.type, this.options.selector, $.proxy(this.enter, this));
                this.$element.on(eventOut + '.' + this.type, this.options.selector, $.proxy(this.leave, this));
            }
        }

        if (this.options.selector) {
            (this._options = $.extend({}, this.options, {trigger: 'manual', selector: ''}));
        } else {
            this.fixTitle();
        }
    };

    TrailTooltip.prototype.getDefaults = function() {
        return TrailTooltip.DEFAULTS;
    };

    TrailTooltip.prototype.getOptions = function(options) {
        options = $.extend({}, this.getDefaults(), this.$element.data(), options);

        if (options.delay && typeof options.delay == 'number') {
            options.delay = {
                show: options.delay,
                hide: options.delay
            };
        }

        return options;
    };

    TrailTooltip.prototype.getDelegateOptions = function() {
        var options = {};
        var defaults = this.getDefaults();

        if (this._options) {
            $.each(this._options, function(key, value) {
                if (defaults[key] != value) {
                    options[key] = value;
                }
            });
        }
        return options;
    };

    TrailTooltip.prototype.enter = function(obj) {
        var self = obj instanceof this.constructor ?
                obj : $(obj.currentTarget).data('bs.' + this.type);

        if (!self) {
            self = new this.constructor(obj.currentTarget, this.getDelegateOptions());
            $(obj.currentTarget).data('bs.' + this.type, self);
        }

        if (obj instanceof $.Event) {
            self.inState[obj.type == 'focusin' ? 'focus' : 'hover'] = true;
        }

        if (self.tip().hasClass('in') || self.hoverState == 'in') {
            self.hoverState = 'in';
            return;
        }

        clearTimeout(self.timeout);

        self.hoverState = 'in';

        if (!self.options.delay || !self.options.delay.show) {
            return self.show();
        }

        self.timeout = setTimeout(function() {
            if (self.hoverState == 'in') {
                self.show();
            }
        }, self.options.delay.show);
    };

    TrailTooltip.prototype.isInStateTrue = function() {
        for (var key in this.inState) {
            if (this.inState[key]) {
                return true;
            }
        }
        return false;
    };

    TrailTooltip.prototype.leave = function(obj) {
        var self = obj instanceof this.constructor ?
                obj : $(obj.currentTarget).data('bs.' + this.type);

        if (!self) {
            self = new this.constructor(obj.currentTarget, this.getDelegateOptions());
            $(obj.currentTarget).data('bs.' + this.type, self);
        }

        if (obj instanceof $.Event) {
            self.inState[obj.type == 'focusout' ? 'focus' : 'hover'] = false;
        }

        if (self.isInStateTrue()) {
            return;
        }
        clearTimeout(self.timeout);

        self.hoverState = 'out';

        if (!self.options.delay || !self.options.delay.hide) {
            return self.hide();
        }
        self.timeout = setTimeout(function() {
            if (self.hoverState == 'out') {
                self.hide();
            }
        }, self.options.delay.hide);
    };

    TrailTooltip.prototype.show = function() {
        var e = $.Event('show.bs.' + this.type);

        if (this.hasContent() && this.enabled) {
            this.$element.trigger(e);

            var inDom = $.contains(this.$element[0].ownerDocument.documentElement, this.$element[0]);
            if (e.isDefaultPrevented() || !inDom) {
                return;
            }
            var that = this;

            var $tip = this.tip();

            var tipId = this.getUID(this.type);

            this.setContent();
            $tip.attr('id', tipId);
            this.$element.attr('aria-describedby', tipId);

            if (this.options.animation) {
                $tip.addClass('fade');
            }

            var placement = typeof this.options.placement == 'function' ?
                    this.options.placement.call(this, $tip[0], this.$element[0]) :
                    this.options.placement;

            var autoToken = /\s?auto?\s?/i;
            var autoPlace = autoToken.test(placement);
            if (autoPlace) {
                placement = placement.replace(autoToken, '') || 'top';
            }

            var css = {top: 0, left: 0, display: 'block'};

            $tip
                    .detach()
                    .css(css)
                    .addClass(placement)
                    .data('bs.' + this.type, this);

            if (this.options.container) {
                $tip.appendTo(this.options.container);
            } else {
                $tip.insertAfter(this.$element);
            }
            this.$element.trigger('inserted.bs.' + this.type);

            var pos = this.getPosition();
            var actualWidth = $tip[0].offsetWidth;
            var actualHeight = $tip[0].offsetHeight;

            if (autoPlace) {
                var orgPlacement = placement;
                var viewportDim = this.getPosition(this.$viewport);

                placement = placement == 'bottom' && pos.bottom + actualHeight > viewportDim.bottom ? 'top' :
                        placement == 'top' && pos.top - actualHeight < viewportDim.top ? 'bottom' :
                        placement == 'right' && pos.right + actualWidth > viewportDim.width ? 'left' :
                        placement == 'left' && pos.left - actualWidth < viewportDim.left ? 'right' :
                        placement;

                $tip
                        .removeClass(orgPlacement)
                        .addClass(placement);
            }

            var calculatedOffset = this.getCalculatedOffset(placement, pos, actualWidth, actualHeight);

            this.applyPlacement(calculatedOffset, placement);

            var complete = function() {
                var prevHoverState = that.hoverState;
                that.$element.trigger('shown.bs.' + that.type);
                that.hoverState = null;

                if (prevHoverState == 'out') {
                    that.leave(that);
                }
            };

            if ($.support.transition && this.$tip.hasClass('fade')) {
                $tip
                        .one('bsTransitionEnd', complete)
                        .emulateTransitionEnd(TrailTooltip.TRANSITION_DURATION);
            } else {
                complete();
            }
        }
    };

    TrailTooltip.prototype.applyPlacement = function(offset, placement) {
        var $tip = this.tip();
        var width = $tip[0].offsetWidth;
        var height = $tip[0].offsetHeight;

        // Manually read margins because getBoundingClientRect includes difference.
        var marginTop = parseInt($tip.css('margin-top'), 10);
        var marginLeft = parseInt($tip.css('margin-left'), 10);

        // We must check for NaN for ie 8/9.
        if (isNaN(marginTop)) {
            marginTop = 0;
        }
        if (isNaN(marginLeft)) {
            marginLeft = 0;
        }

        offset.top += marginTop;
        offset.left += marginLeft;

        // $.fn.offset doesn't round pixel values
        // So we use setOffset directly with our own function B-0.
        $.offset.setOffset($tip[0], $.extend({
            using: function(props) {
                $tip.css({
                    top: Math.round(props.top),
                    left: Math.round(props.left)
                });
            }
        }, offset), 0);

        $tip.addClass('in');

        // Check to see if placing tip in new offset caused the tip to resize itself.
        var actualWidth = $tip[0].offsetWidth;
        var actualHeight = $tip[0].offsetHeight;

        if (placement == 'top' && actualHeight != height) {
            offset.top = offset.top + height - actualHeight;
        }

        var delta = this.getViewportAdjustedDelta(placement, offset, actualWidth, actualHeight);

        if (delta.left) {
            offset.left += delta.left;
        } else {
            offset.top += delta.top;
        }
        var isVertical = /top|bottom/.test(placement);
        var arrowDelta = isVertical ? delta.left * 2 - width + actualWidth : delta.top * 2 - height + actualHeight;
        var arrowOffsetPosition = isVertical ? 'offsetWidth' : 'offsetHeight';

        $tip.offset(offset);
        this.replaceArrow(arrowDelta, $tip[0][arrowOffsetPosition], isVertical);
    };

    TrailTooltip.prototype.replaceArrow = function(delta, dimension, isVertical) {
        this.arrow()
                .css(isVertical ? 'left' : 'top', 50 * (1 - delta / dimension) + '%')
                .css(isVertical ? 'top' : 'left', '');
    };

    TrailTooltip.prototype.setContent = function() {
        var $tip = this.tip();
        var title = this.getTitle();

        $tip.find('.tooltip-inner')[this.options.html ? 'html' : 'text'](title);
        $tip.removeClass('fade in top bottom left right');
    };

    TrailTooltip.prototype.hide = function(callback) {
        var that = this;
        var $tip = $(this.$tip);
        var e = $.Event('hide.bs.' + this.type);
        // Complete.
        function complete() {
            if (that.hoverState != 'in') {
                $tip.detach();
            }
            if (that.$element) { // TODO: Check whether guarding this code with this `if` is really necessary.
                that.$element
                        .removeAttr('aria-describedby')
                        .trigger('hidden.bs.' + that.type);
            }
            if (callback) {
                callback();
            }
        }

        this.$element.trigger(e);

        if (e.isDefaultPrevented()) {
            return;
        }

        $tip.removeClass('in');

        if ($.support.transition && $tip.hasClass('fade')) {
            $tip
                    .one('bsTransitionEnd', complete)
                    .emulateTransitionEnd(TrailTooltip.TRANSITION_DURATION);
        } else {
            complete();
        }
        this.hoverState = null;

        return this;
    };

    TrailTooltip.prototype.fixTitle = function() {
        var $e = this.$element;
        if ($e.attr('title') || typeof $e.attr('data-original-title') != 'string') {
            $e.attr('data-original-title', $e.attr('title') || '').attr('title', '');
        }
    };

    TrailTooltip.prototype.hasContent = function() {
        return this.getTitle();
    };

    TrailTooltip.prototype.getPosition = function($element) {
        $element = $element || this.$element;

        var el = $element[0];
        var isBody = el.tagName == 'BODY';

        var elRect = el.getBoundingClientRect();
        if (elRect.width === null) {
            // Width and height are missing in IE8, so compute them manually; see https://github.com/twbs/bootstrap/issues/14093.
            elRect = $.extend({}, elRect, {width: elRect.right - elRect.left, height: elRect.bottom - elRect.top});
        }
        var isSvg = window.SVGElement && el instanceof window.SVGElement;
        // Avoid using $.offset() on SVGs since it gives incorrect results in jQuery 3.
        // See https://github.com/twbs/bootstrap/issues/20280
        var elOffset = isBody ? {top: 0, left: 0} : (isSvg ? null : $element.offset());
        var scroll = {scroll: isBody ? document.documentElement.scrollTop || document.body.scrollTop : $element.scrollTop()};
        var outerDims = isBody ? {width: $(window).width(), height: $(window).height()} : null;

        return $.extend({}, elRect, scroll, outerDims, elOffset);
    };

    TrailTooltip.prototype.getCalculatedOffset = function(placement, pos, actualWidth, actualHeight) {
        return placement == 'bottom' ? {top: pos.top + pos.height, left: pos.left + pos.width / 2 - actualWidth / 2} :
                placement == 'top' ? {top: pos.top - actualHeight, left: pos.left + pos.width / 2 - actualWidth / 2} :
                placement == 'left' ? {top: pos.top + pos.height / 2 - actualHeight / 2, left: pos.left - actualWidth} :
                {top: pos.top + pos.height / 2 - actualHeight / 2, left: pos.left + pos.width};

    };

    TrailTooltip.prototype.getViewportAdjustedDelta = function(placement, pos, actualWidth, actualHeight) {
        var delta = {top: 0, left: 0};
        if (!this.$viewport) {
            return delta;
        }
        var viewportPadding = this.options.viewport && this.options.viewport.padding || 0;
        var viewportDimensions = this.getPosition(this.$viewport);

        if (/right|left/.test(placement)) {
            var topEdgeOffset = pos.top - viewportPadding - viewportDimensions.scroll;
            var bottomEdgeOffset = pos.top + viewportPadding - viewportDimensions.scroll + actualHeight;
            if (topEdgeOffset < viewportDimensions.top) { // Top overflow.
                delta.top = viewportDimensions.top - topEdgeOffset;
            } else if (bottomEdgeOffset > viewportDimensions.top + viewportDimensions.height) { // Bottom overflow.
                delta.top = viewportDimensions.top + viewportDimensions.height - bottomEdgeOffset;
            }
        } else {
            var leftEdgeOffset = pos.left - viewportPadding;
            var rightEdgeOffset = pos.left + viewportPadding + actualWidth;
            if (leftEdgeOffset < viewportDimensions.left) { // Left overflow.
                delta.left = viewportDimensions.left - leftEdgeOffset;
            } else if (rightEdgeOffset > viewportDimensions.right) { // Right overflow.
                delta.left = viewportDimensions.left + viewportDimensions.width - rightEdgeOffset;
            }
        }

        return delta;
    };

    TrailTooltip.prototype.getTitle = function() {
        var title;
        var $e = this.$element;
        var o = this.options;

        title = $e.attr('data-original-title')
                || (typeof o.title == 'function' ? o.title.call($e[0]) : o.title);

        return title;
    };

    TrailTooltip.prototype.getUID = function(prefix) {
        do {
            prefix += Math.floor(Math.random() * 1000000);
        } while (document.getElementById(prefix));
        return prefix;
    };

    TrailTooltip.prototype.tip = function() {
        if (!this.$tip) {
            this.$tip = $(this.options.template);
            if (this.$tip.length != 1) {
                throw new Error(this.type + ' `template` option must consist of exactly 1 top-level element!');
            }
        }
        return this.$tip;
    };

    TrailTooltip.prototype.arrow = function() {
        return (this.$arrow = this.$arrow || this.tip().find('.tooltip-arrow'));
    };

    TrailTooltip.prototype.enable = function() {
        this.enabled = true;
    };

    TrailTooltip.prototype.disable = function() {
        this.enabled = false;
    };

    TrailTooltip.prototype.toggleEnabled = function() {
        this.enabled = !this.enabled;
    };

    TrailTooltip.prototype.toggle = function(e) {
        var self = this;
        if (e) {
            self = $(e.currentTarget).data('bs.' + this.type);
            if (!self) {
                self = new this.constructor(e.currentTarget, this.getDelegateOptions());
                $(e.currentTarget).data('bs.' + this.type, self);
            }
        }

        if (e) {
            self.inState.click = !self.inState.click;
            if (self.isInStateTrue()) {
                self.enter(self);
            } else {
                self.leave(self);
            }
        } else {
            if (self.tip().hasClass('in')) {
                self.leave(self);
            } else {
                self.enter(self);
            }
        }
    };

    TrailTooltip.prototype.destroy = function() {
        var that = this;
        clearTimeout(this.timeout);
        this.hide(function() {
            that.$element.off('.' + that.type).removeData('bs.' + that.type);
            if (that.$tip) {
                that.$tip.detach();
            }
            that.$tip = null;
            that.$arrow = null;
            that.$viewport = null;
            that.$element = null;
        });
    };


    // TOOLTIP PLUGIN DEFINITION
    // =========================
    function TrailTOOLTIPPlugin(option) {
        return this.each(function() {
            var $this = $(this);
            var data = $this.data('bs.trailtooltip');
            var options = typeof option == 'object' && option;

            if (!data && /destroy|hide/.test(option)) {
                return;
            }
            if (!data) {
                $this.data('bs.trailtooltip', (data = new TrailTooltip(this, options)));
            }
            if (typeof option == 'string') {
                data[option]();
            }
        });
    }

    var old = $.fn.trailtooltip;

    $.fn.trailtooltip = TrailTOOLTIPPlugin;
    $.fn.trailtooltip.Constructor = TrailTooltip;


    // TOOLTIP NO CONFLICT
    // ===================

    $.fn.trailtooltip.noConflict = function() {
        $.fn.trailtooltip = old;
        return this;
    };

    log.debug('Trail Format AMD');
    return {
        init: function() {
            $(document).ready(function($) {
                $("[data-toggle=trailtooltip]").trailtooltip();
            });
            log.debug('Trail Format AMD init');
        }
    };
});
/* jshint ignore:end */
