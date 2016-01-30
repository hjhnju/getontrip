define('keyword/guide', [
    'require',
    'jquery',
    'common/common',
    'moment',
    'etpl',
    './list.tpl',
    'common/Remoter',
    'common/iscroll'
], function (require) {
    var $ = require('jquery');
    var common = require('common/common');
    var moment = require('moment');
    var etpl = require('etpl');
    var tpl = require('./list.tpl');
    var Remoter = require('common/Remoter');
    var getLandscapeList = new Remoter('LANDSCAPE_LIST');
    var IScroll = require('common/iscroll');
    var myScroll, pullDownEl, pullDownOffset, pullUpEl, pullUpOffset, generatedCount = 0;
    var currentPage = 1;
    var html = '';
    function init(acenter, alist) {
        common.init();
        htmlContainer = $('#landscape_list');
        etpl.compile(tpl);
        bindEvents.init();
    }
    var bindEvents = {
            init: function () {
                this.initMap();
                this.inintScroll();
            },
            initMap: function () {
                map = new AMap.Map('container', { resizeEnable: true });
                map.plugin('AMap.Geolocation', function () {
                    geolocation = new AMap.Geolocation({
                        enableHighAccuracy: true,
                        timeout: 10000,
                        buttonOffset: new AMap.Pixel(10, 20),
                        zoomToAccuracy: true,
                        buttonPosition: 'RB'
                    });
                    map.addControl(geolocation);
                    geolocation.getCurrentPosition();
                    AMap.event.addListener(geolocation, 'complete', bindEvents.onComplete);
                    AMap.event.addListener(geolocation, 'error', bindEvents.onError);
                });
            },
            onComplete: function (data) {
                params = {
                    page: currentPage,
                    x: data.position.getLng(),
                    y: data.position.getLat()
                };
                getRemoteList.getNearByList(params);
            },
            onError: function (data) {
                $('#sight_list').html('<div class="error-text">\u554A\u54E6\uFF0C\u5B9A\u4F4D\u5931\u8D25!<a href="javascript:void(0)" class="refresh-btn"  onclick="location.reload();">&nbsp</a></div>');
            },
            inintScroll: function () {
                myScroll = new IScroll('#wrapper', {
                    probeType: 2,
                    bindToWrapper: true,
                    scrollY: true,
                    mouseWheel: true,
                    click: true
                });
                common.myScrollEvents(myScroll, bindEvents.pullUpAction, bindEvents.pullDownAction);
                setTimeout(function () {
                    document.getElementById('wrapper').style.left = '0';
                }, 800);
                document.addEventListener('touchmove', function (e) {
                    e.preventDefault();
                }, false);
            },
            pullUpAction: function () {
                currentPage++;
                params.page = currentPage;
                getRemoteList.getNearByList(params);
                myScroll.refresh();
            },
            pullDownAction: function () {
                getRemoteList.getNearByList(params);
                myScroll.refresh();
            }
        };
    var getRemoteList = {
            getNearByList: function (data) {
                getLandscapeList.remote(data);
                getLandscapeList.on('success', function (data) {
                    ajaxCallbackfun(data, 'returnSightNearbyList');
                });
            }
        };
    var ajaxCallbackfun = function (data, tpl) {
        if (data.bizError) {
            renderError(data);
        } else {
            var pullUpEl = document.getElementById('pullUp');
            if (!data.content.length && currentPage == 1) {
                htmlContainer.html(etpl.render('Error', { msg: '\u60A8\u5F53\u524D\u6CA1\u6709\u6570\u636E\u54DF' }));
                return;
            }
            if (!data.content.length && currentPage > 1) {
                pullUpEl.className = '';
                pullUpEl.querySelector('.pullUpLabel').innerHTML = '\u5168\u90E8\u52A0\u8F7D\u5B8C\u6BD5';
            } else {
                renderHTML(tpl, data);
                setTimeout(function () {
                    myScroll.refresh();
                }, 0);
            }
        }
    };
    var renderHTML = function (tpl, data) {
        if (currentPage == 1) {
            html = etpl.render(tpl, { list: data.content });
        } else {
            html = html + etpl.render(tpl, { list: data.content });
        }
        htmlContainer.html(html);
    };
    var renderError = function (data) {
        htmlContainer.render(etpl.render('Error', { msg: data.statusInfo }));
    };
    return { init: init };
});