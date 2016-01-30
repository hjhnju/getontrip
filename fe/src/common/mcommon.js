/**
 * @ignore
 * @file common.js
 * @author fanyy
 * @time 15-4-28
 */

define(function(require) {
    var $ = require('jquery');
    var tpl = require('./mcommon.tpl');
    var etpl = require('etpl');
    var Remoter = require('common/Remoter');
    var fastClick = require('common/fastclick');
    var IScroll = require('common/iscroll');

    function init() {
        etpl.compile(tpl);

        //解决 click 的延迟, 还可以防止 穿透(跨页面穿透除外)
        fastClick.attach(document.body);
    }

    /**
     * 滚动事件
     *  
     */
    var myScrollEvents = function(myScroll, pullUpAction, pullDownAction) {
        document.addEventListener('touchmove', function(e) {
            e.preventDefault();
        }, false);
        var pullDownEl = document.getElementById('pullDown');
        //var pullDownEl = element.find('pullDown')[0];
        var pullDownOffset = pullDownEl.offsetHeight;
       var pullUpEl = document.getElementById('pullUp');
        //var pullUpEl = element.find('pullUp')[0];

        var pullUpOffset = pullUpEl.offsetHeight;
        myScroll.on('scroll', function() {
            if (this.y > 5 && !pullDownEl.className.match('flip')) {
                pullDownEl.className = 'flip';
                pullDownEl.querySelector('.pullDownLabel').innerHTML = '释放后加载最新';
                this.minScrollY = 0;
            } else if (this.y < 5 && pullDownEl.className.match('flip')) {
                pullDownEl.className = '';
                pullDownEl.querySelector('.pullDownLabel').innerHTML = '下拉加载更多';
                this.minScrollY = -pullDownOffset;
            } else if (this.y < (this.maxScrollY - 5) && !pullUpEl.className.match('flip')) {
                pullUpEl.className = 'flip';
                pullUpEl.querySelector('.pullUpLabel').innerHTML = '释放后加载更多';
                this.maxScrollY = this.maxScrollY;
            } else if (this.y > (this.maxScrollY + 5) && pullUpEl.className.match('flip')) {
                pullUpEl.className = '';
                pullUpEl.querySelector('.pullUpLabel').innerHTML = '上拉加载更多';
                this.maxScrollY = pullUpOffset;
            }
        });
        myScroll.on('scrollEnd', function() {
            if (pullDownEl.className.match('flip')) {
                pullDownEl.className = 'loading';
                pullDownEl.querySelector('.pullDownLabel').innerHTML = '正在努力加载中...';
                pullDownAction(); // Execute custom function (ajax call?)
            } else if (pullUpEl.className.match('flip')) {
                pullUpEl.className = 'loading';
                pullUpEl.querySelector('.pullUpLabel').innerHTML = '正在努力加载中...';
                pullUpAction(); // Execute custom function (ajax call?)
            }
        });
        myScroll.on('refresh', function() {
            if (pullDownEl.className.match('loading')) {
                pullDownEl.className = 'hidden';
                pullDownEl.querySelector('.pullDownLabel').innerHTML = "下拉加载更多";
            } else if (pullUpEl.className.match('loading')) {
                pullUpEl.className = 'color_hide';
                pullUpEl.querySelector('.pullUpLabel').innerHTML = '';
            }
        });
    }

    /**
     * [bindEvents 绑定事件]
     * @type {Object}
     */
    var bindEvents = {
        navScroll: function() {
            //导航滑动
            var boxWidth = $('#nav').width();
            var maxNum = Math.floor(boxWidth / 60);
            var size = $('#nav ul li').size();
            totalWidth = 0;
            $('#nav ul li').each(function() {
                totalWidth = totalWidth + $(this).width()+10;;
            });
            if (totalWidth > boxWidth) {
                $('#nav .scroller').width(totalWidth);
            } else {
                //平均分配li的宽度
                $('#nav ul li').width(boxWidth/size-10);
                //$('#nav ul').addClass('display_flex');
            }
             $('#scroller').width(boxWidth*size);
            navScroll = new IScroll('#nav', {
                scrollX: true,
                scrollY: false,
                bindToWrapper: true,
                mouseWheel: true
            }); 
            navScroll.scrollToElement(document.querySelector('#nav li.selected'),'auto',true,false);

            return navScroll;
        },
        hideAddressBar: function() {
            var win = window;
            var doc = win.document;

            // If there's a hash, or addEventListener is undefined, stop here
            if (!win.navigator.standalone && !location.hash && win.addEventListener) {

                //scroll to 1
                win.scrollTo(0, 1);
                var scrollTop = 1,
                    getScrollTop = function() {
                        return win.pageYOffset || doc.compatMode === "CSS1Compat" && doc.documentElement.scrollTop || doc.body.scrollTop || 0;
                    },

                    //reset to 0 on bodyready, if needed
                    bodycheck = setInterval(function() {
                        if (doc.body) {
                            clearInterval(bodycheck);
                            scrollTop = getScrollTop();
                            win.scrollTo(0, scrollTop === 1 ? 0 : 1);
                        }
                    }, 15);

                win.addEventListener("load", function() {
                    setTimeout(function() {
                        //at load, if user hasn't scrolled more than 20 or so...
                        if (getScrollTop() < 20) {
                            //reset to hide addr bar at onload
                            win.scrollTo(0, scrollTop === 1 ? 0 : 1);
                        }
                    }, 0);
                }, false);
            }
        }
    }

    var getData = {
        initNavData: function(params) {
            tagId = params.tagId;
            var getNavList = new Remoter('NAV_LIST');
            // 标签列表
            getNavList.remote(params);
            //成功
            getNavList.on('success', function(data) {
                if (data.bizError) {
                    renderError(data);
                } else { 
                    $('#nav ul').html(etpl.render('returnNavList', {
                        list: data.tags,
                        sightId: data.id,
                    }));
                    $('#nav ul li[data-id="'+tagId+'"]').addClass('selected');
                    bindEvents.navScroll();
                }
            });
        }
    }

    /**
     * [COOKIES 操作]
     * @type {Object}
     */
    var COOKIES = {
        //这是有设定过期时间的使用示例：
        //s20是代表20秒
        //h是指小时，如12小时则是：h12
        //d是天数，30天则：d30 
        //setCookie("name", "hayden", "s20");

        //读取cookies
        getCookie: function(name) {
            var arr, reg = new RegExp("(^| )" + name + "=([^;]*)(;|$)");

            if (arr = document.cookie.match(reg))

                return unescape(arr[2]);
            else
                return null;
        },

        //删除cookies
        delCookie: function(name) {
            var exp = new Date();
            exp.setTime(exp.getTime() - 1);
            var cval = getCookie(name);
            if (cval != null)
                document.cookie = name + "=" + cval + ";expires=" + exp.toGMTString();
        },

        //设置cookies
        setCookie: function(name, value, time) {
            var strsec = this.getsec(time);
            var exp = new Date();
            exp.setTime(exp.getTime() + strsec * 1);
            document.cookie = name + "=" + escape(value) + ";expires=" + exp.toGMTString();
        },

        getsec: function(str) { 
            var str1 = str.substring(1, str.length) * 1;
            var str2 = str.substring(0, 1);
            if (str2 == "s") {
                return str1 * 1000;
            } else if (str2 == "h") {
                return str1 * 60 * 60 * 1000;
            } else if (str2 == "d") {
                return str1 * 24 * 60 * 60 * 1000;
            }
        }
    }

    return {
        init: init,
        myScrollEvents: myScrollEvents,
        COOKIES: COOKIES,
        bindEvents: bindEvents,
        getData: getData
    };
});
