define('common/mcommon', [
    'require',
    'jquery',
    './mcommon.tpl',
    'etpl',
    'common/Remoter',
    'common/fastclick',
    'common/iscroll'
], function (require) {
    var $ = require('jquery');
    var tpl = require('./mcommon.tpl');
    var etpl = require('etpl');
    var Remoter = require('common/Remoter');
    var fastClick = require('common/fastclick');
    var IScroll = require('common/iscroll');
    function init() {
        etpl.compile(tpl);
        fastClick.attach(document.body);
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
                pullUpEl.querySelector('.pullUpLabel').innerHTML = '\u91CA\u653E\u540E\u52A0\u8F7D\u66F4\u591A';
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
                pullUpEl.className = 'color_hide';
                pullUpEl.querySelector('.pullUpLabel').innerHTML = '';
            }
        });
    };
    var bindEvents = {
            navScroll: function () {
                var boxWidth = $('#nav').width();
                var maxNum = Math.floor(boxWidth / 60);
                var size = $('#nav ul li').size();
                totalWidth = 0;
                $('#nav ul li').each(function () {
                    totalWidth = totalWidth + $(this).width() + 10;
                    ;
                });
                if (totalWidth > boxWidth) {
                    $('#nav .scroller').width(totalWidth);
                } else {
                    $('#nav ul li').width(boxWidth / size - 10);
                }
                $('#scroller').width(boxWidth * size);
                navScroll = new IScroll('#nav', {
                    scrollX: true,
                    scrollY: false,
                    bindToWrapper: true,
                    mouseWheel: true
                });
                navScroll.scrollToElement(document.querySelector('#nav li.selected'), 'auto', true, false);
                return navScroll;
            },
            hideAddressBar: function () {
                var win = window;
                var doc = win.document;
                if (!win.navigator.standalone && !location.hash && win.addEventListener) {
                    win.scrollTo(0, 1);
                    var scrollTop = 1, getScrollTop = function () {
                            return win.pageYOffset || doc.compatMode === 'CSS1Compat' && doc.documentElement.scrollTop || doc.body.scrollTop || 0;
                        }, bodycheck = setInterval(function () {
                            if (doc.body) {
                                clearInterval(bodycheck);
                                scrollTop = getScrollTop();
                                win.scrollTo(0, scrollTop === 1 ? 0 : 1);
                            }
                        }, 15);
                    win.addEventListener('load', function () {
                        setTimeout(function () {
                            if (getScrollTop() < 20) {
                                win.scrollTo(0, scrollTop === 1 ? 0 : 1);
                            }
                        }, 0);
                    }, false);
                }
            }
        };
    var getData = {
            initNavData: function (params) {
                tagId = params.tagId;
                var getNavList = new Remoter('NAV_LIST');
                getNavList.remote(params);
                getNavList.on('success', function (data) {
                    if (data.bizError) {
                        renderError(data);
                    } else {
                        $('#nav ul').html(etpl.render('returnNavList', {
                            list: data.tags,
                            sightId: data.id
                        }));
                        $('#nav ul li[data-id="' + tagId + '"]').addClass('selected');
                        bindEvents.navScroll();
                    }
                });
            }
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
        COOKIES: COOKIES,
        bindEvents: bindEvents,
        getData: getData
    };
});