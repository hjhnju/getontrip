/**
 * @ignore
 * @file detail.js
 * @author fyy
 * @time 15-8-7
 */

define(function(require) {
    var $ = require('jquery');
    require('common/extra/jquery.lazyload');
    require('common/imgSize');

    var rate = 93 / 133;

    function init() {
        //话题背景图片显示缩略图
        
        //获取容器的宽度,高度
        var width = $('.bg-img').width();
        var height = Math.ceil(rate * width);
        var img = $('#topic-bg'); 
        var imgUrl=img.attr('data-webroot') + '/pic/' + img.attr('data-img_hash') + '_' + width + '_' + height + '.'+img.attr('data-img_type');
        img.attr('src', imgUrl);
        img.css({
            width: width + 'px',
            height: height + 'px'
        }); 

        $(".rich-text img").css('display', 'block').lazyload({
            threshold: 400,
            container: '.rich-text'
        });

    }
    return {
        init: init
    };
});
