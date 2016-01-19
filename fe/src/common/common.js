/**
 * @ignore
 * @file common.js
 * @author fanyy
 * @time 15-4-28
 */

define(function(require) {
    var tpl = require('./common.tpl');
    var etpl = require('etpl');


    function init() {
        etpl.compile(tpl);
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
        var pullDownOffset = pullDownEl.offsetHeight;
        var pullUpEl = document.getElementById('pullUp');
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
                pullUpEl.querySelector('.pullUpLabel').innerHTML = '释放后加载最新';
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
                pullUpEl.className = 'hidden';
                pullUpEl.querySelector('.pullUpLabel').innerHTML = '上拉加载更多';
            }
        });
    }

    var COOKIES = {
        //这是有设定过期时间的使用示例：
        //s20是代表20秒
        //h是指小时，如12小时则是：h12
        //d是天数，30天则：d30 
        //setCookie("name", "hayden", "s20");

        //读取cookies
         getCookie : function(name) {
            var arr, reg = new RegExp("(^| )" + name + "=([^;]*)(;|$)");

            if (arr = document.cookie.match(reg))

                return unescape(arr[2]);
            else
                return null;
        },

        //删除cookies
         delCookie : function(name) {
            var exp = new Date();
            exp.setTime(exp.getTime() - 1);
            var cval = getCookie(name);
            if (cval != null)
                document.cookie = name + "=" + cval + ";expires=" + exp.toGMTString();
        },
        
        //设置cookies
         setCookie : function(name, value, time) {
            var strsec = this.getsec(time);
            var exp = new Date();
            exp.setTime(exp.getTime() + strsec * 1);
            document.cookie = name + "=" + escape(value) + ";expires=" + exp.toGMTString();
        },

         getsec : function(str) {
            alert(str);
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
        COOKIES:COOKIES
    };
});
