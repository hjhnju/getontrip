/**
 * @ignore
 * @file index.js
 * @author yangbinYB(1033371745@qq.com)
 * @time 14-12-11
 */

define(function(require) {
    var $ = require('jquery');
    require('common/imgSize');
    var winRate = 650 / 1300;

    //var nowRate = 1;
    //var $imgBox = $('#tuzhi_img_box');
    //var $img = $imgBox.find('img');
    var $main = $('#main-wraper');
    var $tuzhiBox = $('#tuzhi_box');
    var $ours = $('#ours');
    var $oursBox = $('#oursBox');
    var $download = $('.download');
    var oldWidth, oldHeight = 0;
    //获取容器的宽度,高度
    var mainWidth = $main.width();

    function init() {

        //Fun.changeSize();
        bindEvents.init();
    }





    //函数
    var Fun = {
        columns: 6,
        init: function() {
            mainWidth = $main.width();
            mainHeight = $main.height();
            //Fun.setBackgroundImageCSS($('.tuzhi'));
            $tuzhiBox.css({
                'width': Fun.getTuzhiBoxWidth() + 'px'
            });

            if (!this.isMobile()) {
                $ours.css({
                    'width': Fun.getTuzhiBoxWidth() + 'px',
                    'height': mainHeight * (300 / 650) + 'px'
                });
            } else {
                $ours.css({
                    'width': Fun.getTuzhiBoxWidth() + 'px',
                });
            }
        },
        changeSize: function() {
            //获取容器的宽度 
            mainWidth = $main.width();
            //nowRate = mainWidth / 1300;
            mainHeight = $main.height();
            /* $main.css({
                 'height': mainHeight + 'px'
             });*/
            mainRate = mainHeight / mainWidth;

            tuzhiBoxWidht = $tuzhiBox.width();


            Fun.setBackgroundImageCSS($('.tuzhi'));

            $tuzhiBox.css({
                'width': this.getTuzhiBoxWidth() + 'px'
            });
            $ours.css({
                'width': this.getTuzhiBoxWidth() + 'px',
                'height': mainHeight * (300 / 650) + 'px'
            });
            if (this.showWideVersion()) {
                this.setDownloadCSS();
                this.setWideOursBoxCSS();
            } else {
                $slogan = $('#ours .slogan');
                $phone3 = $('#ours .phone3');
                this.setOursBoxCSS();
            }


        },
        isMobile:function(){
            return $('.tuzhi').hasClass('mobile');
        },
        showWideVersion: function() {
            //判断是否显示宽屏效果
            return (mainWidth > 768);
        },
        //获取Grid宽度
        getGridWidth: function(e) {
            return this.getSizeOfColumns(e, this.columns)
        },
        getTuzhiBoxWidth: function() {
            var rate = 750 / 1300;
            return this.showWideVersion() ? mainWidth * rate : mainWidth;
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
            var linearFun = this.linear(320, 3, 1200, 8);
            return Math.round(linearFun(this.getTargetGridWidth(e)))
        },
        getTargetGridWidth: function(e) {
            var linearFun = this.linear(320, 276, 1100, 900);
            return Math.round(linearFun(e))
        },
        setBackgroundImageCSS: function(e) {
            if (this.showWideVersion()) {
                e.removeClass('mobile')
            } else {
                !e.hasClass('mobile') ? e.addClass('mobile') : null
            }
        },
        setOursBoxCSS: function() {
            var scale = 'scale(' + this.getPhoneScale() + ')';
            /* $oursBox.css({
                 'height': (0.55 * mainHeight) + 'px',
                 '-webkit-transform': scale,
                 '-moz-transform': scale,
                 '-ms-transform': scale,
                 '-o-transform': scale,
                 'transform': scale
             });*/
        },
        setWideOursBoxCSS: function() {
            $phone1 = $('#ours .phone1');
            $phone2 = $('#ours .phone2');
            $logo = $('#ours .logo');
            $slogan = $('#ours .slogan');
            var phone1Rate = 166 / 750;
            var phone2Rate = 145 / 750;
            var sloganRate = 300 / 750;
            var logoRate = 200 / 750;
            var topRate = 260 / 650;

            $ours.css({
                'top': mainHeight * topRate + 'px'
            });

            oldWidth = $logo.width();
            oldHeight = $logo.height();
            $logo.css({
                'width': tuzhiBoxWidht * logoRate + 'px',
                'height': (oldHeight * (tuzhiBoxWidht * logoRate / oldWidth)) + 'px'
            });

            oldWidth = $slogan.width();
            oldHeight = $slogan.height();
            $slogan.css({
                'width': tuzhiBoxWidht * sloganRate + 'px',
                'height': (oldHeight * (tuzhiBoxWidht * sloganRate / oldWidth)) + 'px',
                'line-height': (oldHeight * (tuzhiBoxWidht * sloganRate / oldWidth)) + 'px',
                'font-size': (oldHeight * (tuzhiBoxWidht * sloganRate / oldWidth)) * (16 / 30) + 'px'

            });

            oldWidth = $phone2.width();
            oldHeight = $phone2.height();
            $phone2.css({
                'width': tuzhiBoxWidht * phone2Rate + 'px',
                'height': (oldHeight * (tuzhiBoxWidht * phone2Rate / oldWidth)) + 'px'
            });

            oldWidth = $phone1.width();
            oldHeight = $phone1.height();

            $phone1.css({
                'width': tuzhiBoxWidht * phone1Rate + 'px',
                'height': (oldHeight * (tuzhiBoxWidht * phone1Rate / oldWidth)) + 'px',
                'right': $phone2.width() + 40 + 'px'
            });
            /* $download.css({
                 'height': ($apple_logo.height() +$erweima.height()+10) + 'px'
             });*/
        },
        setDownloadCSS: function() {
            //var $download = $('.download');
            var $erweima = $('.download .erweima');
            var $apple_logo = $('.download .apple_logo');
            var downloadRate = 100 / 750;

            oldWidth = $apple_logo.width();
            oldHeight = $apple_logo.height();
            $apple_logo.css({
                'width': tuzhiBoxWidht * downloadRate + 'px',
                'height': (oldHeight * (tuzhiBoxWidht * downloadRate / oldWidth)) + 'px'
            });
            $erweima.css({
                'height': tuzhiBoxWidht * downloadRate + 'px',
                'width': tuzhiBoxWidht * downloadRate + 'px',
                'top': ($apple_logo.height() + 10) + 'px'

            });
            $download.css({
                'height': ($apple_logo.height() + $erweima.height() + 10) + 'px'
            });
        },
        getHeroHeight: function() {
            return Math.max(490, mainHeight)
        },
        getBackgroundImageHeight: function() {
            return this.getHeroHeight()
        },
        scaleWidth: function() {
            return this.linear(320, .6, 750, 1)(mainWidth)
        },
        getPhoneScale: function() {
            var e = this.scaleWidth(),
                t = this.linear(500, .5, 975, 1)(this.getBackgroundImageHeight());
            return Math.min(t, e)
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
             Fun.init();
            window.onresize = function() {
                Fun.init();
            };
        }
    }

    return {
        init: init
    };
});
