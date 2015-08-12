define('common/imgSize', [
    'require',
    'jquery'
], function (require) {
    var $ = require('jquery');
    $.fn.imgSize = function () {
        var _this = $(this);
        var imgs = $(this).find('img');
        var width = $(this).width();
        return imgs.each(function (key, img) {
            var realWidth;
            var realHeight;
            $('<img/>').attr('src', $(img).attr('src')).load(function () {
                realWidth = this.width;
                realHeight = this.height;
                if (realWidth >= width) {
                    $(img).css('width', width + 'px').css('height', width + 'px');
                } else {
                    $(img).css('width', realWidth + 'px').css('height', realHeight + 'px');
                }
            });
        });
    };
});