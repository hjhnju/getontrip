define('common/common', [
    'require',
    './common.tpl',
    'etpl'
], function (require) {
    var tpl = require('./common.tpl');
    var etpl = require('etpl');
    function init() {
        etpl.compile(tpl);
    }
    var myScrollEvents = function (myScroll, pullUpAction, pullDownAction) {
        document.addEventListener('touchmove', function (e) {
            e.preventDefault();
        }, false);
        var pullDownEl = document.getElementById('pullDown');
        var pullDownOffset = pullDownEl.offsetHeight;
        var pullUpEl = document.getElementById('pullUp');
        var pullUpOffset = pullUpEl.offsetHeight;
        myScroll.on('scroll', function () {
            if (this.y > 5 && !pullDownEl.className.match('flip')) {
                pullDownEl.className = 'flip';
                pullDownEl.querySelector('.pullDownLabel').innerHTML = '\u91CA\u653E\u540E\u52A0\u8F7D\u6700\u65B0';
                this.minScrollY = 0;
            } else if (this.y < 5 && pullDownEl.className.match('flip')) {
                pullDownEl.className = '';
                pullDownEl.querySelector('.pullDownLabel').innerHTML = '\u4E0B\u62C9\u52A0\u8F7D\u66F4\u591A';
                this.minScrollY = -pullDownOffset;
            } else if (this.y < this.maxScrollY - 5 && !pullUpEl.className.match('flip')) {
                pullUpEl.className = 'flip';
                pullUpEl.querySelector('.pullUpLabel').innerHTML = '\u91CA\u653E\u540E\u52A0\u8F7D\u6700\u65B0';
                this.maxScrollY = this.maxScrollY;
            } else if (this.y > this.maxScrollY + 5 && pullUpEl.className.match('flip')) {
                pullUpEl.className = '';
                pullUpEl.querySelector('.pullUpLabel').innerHTML = '\u4E0A\u62C9\u52A0\u8F7D\u66F4\u591A';
                this.maxScrollY = pullUpOffset;
            }
        });
        myScroll.on('scrollEnd', function () {
            if (pullDownEl.className.match('flip')) {
                pullDownEl.className = 'loading';
                pullDownEl.querySelector('.pullDownLabel').innerHTML = '\u6B63\u5728\u52AA\u529B\u52A0\u8F7D\u4E2D...';
                pullDownAction();
            } else if (pullUpEl.className.match('flip')) {
                pullUpEl.className = 'loading';
                pullUpEl.querySelector('.pullUpLabel').innerHTML = '\u6B63\u5728\u52AA\u529B\u52A0\u8F7D\u4E2D...';
                pullUpAction();
            }
        });
        myScroll.on('refresh', function () {
            if (pullDownEl.className.match('loading')) {
                pullDownEl.className = 'hidden';
                pullDownEl.querySelector('.pullDownLabel').innerHTML = '\u4E0B\u62C9\u52A0\u8F7D\u66F4\u591A';
            } else if (pullUpEl.className.match('loading')) {
                pullUpEl.className = 'hidden';
                pullUpEl.querySelector('.pullUpLabel').innerHTML = '\u4E0A\u62C9\u52A0\u8F7D\u66F4\u591A';
            }
        });
    };
    var COOKIES = {
            getCookie: function (name) {
                var arr, reg = new RegExp('(^| )' + name + '=([^;]*)(;|$)');
                if (arr = document.cookie.match(reg))
                    return unescape(arr[2]);
                else
                    return null;
            },
            delCookie: function (name) {
                var exp = new Date();
                exp.setTime(exp.getTime() - 1);
                var cval = getCookie(name);
                if (cval != null)
                    document.cookie = name + '=' + cval + ';expires=' + exp.toGMTString();
            },
            setCookie: function (name, value, time) {
                var strsec = this.getsec(time);
                var exp = new Date();
                exp.setTime(exp.getTime() + strsec * 1);
                document.cookie = name + '=' + escape(value) + ';expires=' + exp.toGMTString();
            },
            getsec: function (str) {
                alert(str);
                var str1 = str.substring(1, str.length) * 1;
                var str2 = str.substring(0, 1);
                if (str2 == 's') {
                    return str1 * 1000;
                } else if (str2 == 'h') {
                    return str1 * 60 * 60 * 1000;
                } else if (str2 == 'd') {
                    return str1 * 24 * 60 * 60 * 1000;
                }
            }
        };
    return {
        init: init,
        myScrollEvents: myScrollEvents,
        COOKIES: COOKIES
    };
});