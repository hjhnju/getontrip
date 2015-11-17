/**
 * @ignore
 * @file index.js
 * @author yangbinYB(1033371745@qq.com)
 * @time 14-12-11
 */

define(function(require) {
    var $ = require('jquery');
    require('common/imgSize');
    var rate = 650 / 1300;

    var $imgBox = $('#tuzhi_img_box');
    var $img = $imgBox.find('img');
    var $main = $('#main-wraper');
    var $tuzhiBox = $('#tuzhi_box');
    var $ourBox = $('#ours');


    //获取容器的宽度,高度
    var mainHeight = $main.height();
    var mainWigth = $main.width();

    function init() {

        Fun.changeSize();
        bindEvents.init();
    }





    //函数
    var Fun = {
        columns: 6,
        changeSize: function() {
            //获取容器的宽度,高度
            mainHeight = $main.height();
            mainWigth = $main.width();
            var mainRate = mainHeight / mainWigth;
            var boxwidth = $imgBox.width();
            //var boxheight = Math.ceil(rate * boxwidth);
            var imgright = boxwidth > 768 ? (boxwidth - 1300) : (boxwidth - 1200);
            var imgWidth = boxwidth > 1300 ? boxwidth : 1300;
            //var boxHeight = mainHeight > 1000 ? (mainHeight * 0.8) : (mainRate > 1 ? mainHeight : 650);
            var boxHeight = mainRate > 1 ? mainHeight : 650;


            $imgBox.css({
                'height': boxHeight + 'px',
            });
            Fun.setBackgroundImageCSS($('.tuzhi'));

            $tuzhiBox.css({
                'width': this.showWideVersion() ? this.getGridWidth(mainWigth) : mainWigth
            });
            $ourBox.css({
                'width': this.showWideVersion() ? this.getGridWidth(mainWigth) : mainWigth
            });
 
        },
        showWideVersion: function() {
            //判断是否显示宽屏效果
            return (mainWigth > 768);
        },
        //获取Grid宽度
        getGridWidth: function(e) {
            return this.getSizeOfColumns(e, this.columns)
        },
        getSizeOfColumns: function(e, t) {
            if (0 === t)
                return 0;
            var i = this.getCellSize(e),
                r = this.getGutter(e);
            return Math.round(t * i + (t - 1) * r)
        },
        getCellSize: function(e) {
            return this.getGutter(e) * this.getSubdivisions(e)
        },
        getGutter: function(e) {
            return Math.round(this.getTargetGridWidth(e) / (this.getSubdivisions(e) * this.columns + this.columns - 1))
        },
        getSubdivisions: function(e) {
           var linearFun=this.linear(320, 3, 1200, 8);
            return Math.round(linearFun(this.getTargetGridWidth(e)))
        },
        getTargetGridWidth: function(e) {
             var linearFun=this.linear(320, 276, 1100, 900);
            return Math.round(linearFun(e))
        },
        setBackgroundImageCSS: function(e) {
            if (this.showWideVersion()) {
                e.removeClass('mobile')
            } else {
                !e.hasClass('mobile') ? e.addClass('mobile') : null
            }
        },
        linear: function(e, t, r, o) {
            return function(i) {
                return t + (o - t) * (Fun.constrain(i, e, r) - e) / (r - e)
            }
        },
        constrain: function(e, t, r) {
            return Math.min(Math.max(e, this.coalesce(t, -1 / 0)), this.coalesce(r, 1 / 0))
        },
        coalesce: function() {
            for (var e = 0; e < arguments.length; e++)
                if (void 0 !== arguments[e])
                    return arguments[e];
            return null
        }
    }

    //绑定事件
    var bindEvents = {
        init: function() {
            //当浏览器窗口大小改变时，设置显示内容的高度  
            window.onresize = function() {
                Fun.changeSize();
            }
        }
    }

    return {
        init: init
    };
});
