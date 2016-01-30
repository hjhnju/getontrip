define('common/imgSize', [
    'require',
    'jquery'
], function (require) {
    var $ = require('jquery');
    $.fn.imgSize = function (options) {
        var opts = $.extend({}, $.fn.imgSize.defaults, options);
        var _this = $(this);
        var imgs = $(this).find('img');
        var width = $(this).width();
        return imgs.each(function (key, img) {
            var realWidth;
            var realHeight;
            $('<img/>').attr('src', $(img).attr('src')).load(function () {
                realWidth = this.width;
                realHeight = this.height;
                var rate = opts.rate ? opts.rate : realHeight / realWidth;
                if (realWidth >= width) {
                    $(img).css('width', width + 'px').css('height', rate * width + 'px');
                } else {
                    $(img).css('width', realWidth + 'px').css('height', realHeight + 'px');
                }
            });
        });
    };
    $.fn.imgSize.defaults = { rate: null };
    $.fn.imgSize.setDefaults = function (settings) {
        $.extend($.fn.imgSize.defaults, settings);
    };
});