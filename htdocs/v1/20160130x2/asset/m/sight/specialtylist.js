define('m/sight/specialtylist', [
    'require',
    'jquery',
    'common/mcommon',
    'common/extra/jquery.dotdotdot',
    'etpl',
    './list.tpl',
    'common/Remoter',
    'common/iscroll'
], function (require) {
    var $ = require('jquery');
    var common = require('common/mcommon');
    require('common/extra/jquery.dotdotdot');
    var etpl = require('etpl');
    var tpl = require('./list.tpl');
    var Remoter = require('common/Remoter');
    var getSpecialtyList = new Remoter('SPECIALTY_LIST');
    var IScroll = require('common/iscroll');
    var myScroll, pullDownEl, pullDownOffset, pullUpEl, pullUpOffset, generatedCount = 0;
    var currentPage = 1;
    var html = '';
    var sightId = $('#wrapper').attr('sightId');
    var tagId = $('#wrapper').attr('tagId');
    function init(acenter, alist) {
        common.init();
        common.getData.initNavData({
            sightId: sightId,
            tagId: tagId
        });
        htmlContainer = $('#food_list');
        etpl.compile(tpl);
        bindEvents.init();
    }
    var bindEvents = {
            init: function () {
                this.initList();
                this.inintScroll();
                getRemoteListSuccess();
            },
            initList: function () {
                params = {
                    page: currentPage,
                    pageSize: 15,
                    tags: tagId,
                    sightId: sightId
                };
                getRemoteList.getSpecialtyList(params);
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
                getRemoteList.getSpecialtyList(params);
                myScroll.refresh();
            },
            pullDownAction: function () {
                currentPage = 1;
                params.page = currentPage;
                getRemoteList.getSpecialtyList(params);
                myScroll.refresh();
            }
        };
    var getRemoteList = {
            getSpecialtyList: function (data) {
                getSpecialtyList.remote(data);
            }
        };
    var getRemoteListSuccess = function () {
        getSpecialtyList.on('success', function (data) {
            ajaxCallbackfun(data, 'returnSpecialtyList');
        });
    };
    var ajaxCallbackfun = function (data, tpl) {
        if (data.bizError) {
            renderError(data);
        } else {
            var pullUpEl = document.getElementById('pullUp');
            if (!data.length && currentPage == 1) {
                htmlContainer.html(etpl.render('Error', { msg: '\u5F53\u524D\u6CA1\u6709\u6570\u636E\u54DF,\u8BD5\u8BD5\u522B\u7684\u666F\u70B9\u5427' }));
                return;
            }
            if (!data.length && currentPage > 1) {
                currentPage--;
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
            html = etpl.render(tpl, { list: data });
        } else {
            html = html + etpl.render(tpl, { list: data });
        }
        htmlContainer.html(html);
        $('#food_list .content-box .content').dotdotdot();
    };
    var renderError = function (data) {
        htmlContainer.render(etpl.render('Error', { msg: data.statusInfo }));
    };
    return { init: init };
});