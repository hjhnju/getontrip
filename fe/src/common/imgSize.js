/**
 * @     图片宽度设置
 * @file imgSize.js
 * @author fyy
 * @time 15-8-10
 */

define('common/imgSize', [
    'require',
    'jquery'
], function(require) {
    var $ = require('jquery');
    $.fn.imgSize = function(options) {
        var opts=$.extend({},$.fn.imgSize.defaults, options);
        var _this = $(this);
        var imgs = $(this).find('img');
        var width = $(this).width(); //获取容器的宽度

        return imgs.each(function(key, img) {
            var realWidth; //真实的宽度
            var realHeight; //真实的高度 
            $("<img/>").attr("src", $(img).attr("src")).load(function() {
                /*
                如果要获取图片的真实的宽度和高度有三点必须注意
                1、需要创建一个image对象：如这里的$("<img/>")
                2、指定图片的src路径
                3、一定要在图片加载完成后执行如.load()函数里执行
                */
                realWidth = this.width;
                realHeight = this.height;    
                var rate = opts.rate?opts.rate:(realHeight/realWidth);
                //如果真实的宽度大于浏览器的宽度就按照100%显示
                if (realWidth >= width) {
                    $(img).css("width", width +"px").css("height", rate*width + 'px');
                } else { //如果小于浏览器的宽度按照原尺寸显示
                    $(img).css("width", realWidth + 'px').css("height", realHeight + 'px');
                }
                //$(img).show();
            });

        });
    };

     $.fn.imgSize.defaults={
        rate:null  //高宽比

    };

    $.fn.imgSize.setDefaults=function(settings) {
        $.extend( $.fn.imgSize.defaults, settings );
    };
});
