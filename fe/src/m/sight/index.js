 /**
  * @ignore
  * @file food.js
  * @author fanyy(1178223444@qq.com)
  * @time 15-1-15
  */

 define(function(require) {
     var $ = require('jquery');
     var common = require('common/mcommon');

     require('common/extra/jquery.dotdotdot');
     require('common/extra/jquery.touchSwipe');
     var etpl = require('etpl');
     var tpl = require('./list.tpl');

     var Remoter = require('common/Remoter');


     var getTopicList = new Remoter('TOPIC_LIST');
     var getFoodList = new Remoter('FOOD_LIST');
     var getSpecialtyList = new Remoter('SPECIALTY_LIST');
     var getLandscapeList = new Remoter('LANDSCAPE_LIST');
     var getBookList = new Remoter('BOOK_LIST');
     var getVideoList = new Remoter('VIDEO_LIST');

     var IScroll = require('common/iscroll');
     var myScroll,
         pullDownEl, pullDownOffset,
         pullUpEl, pullUpOffset,
         generatedCount = 0;



     function init(acenter, alist) {
         common.init();
         navScroll = common.bindEvents.navScroll();
         etpl.compile(tpl);
         bindEvents.init();
     }

     /*
      *绑定事件
      */
     var bindEvents = {
         init: function() {
             this.inintParam();
             this.initMap();
             this.inintScroll();
             this.initSwipe();
             this.initClick();
             getRemoteList.getRemoteListSuccess();
             this.initList();
         },
         inintParam: function() {

             //获取tagId
             tagId = location.href.split('#')[1];
             //判断是否在tagList里面
             tagList = {};
             $('#nav li').each(function(index, el) {
                 tagList[$(el).attr('data-id')] = $(el).attr('data-type');
             });
             tagId = tagId && tagList[tagId] ? tagId : $('#nav ul li.selected').attr('data-id');
             //修改标签
             $('#nav li').removeClass('selected');
             $('#nav li[data-id="' + tagId + '"]').addClass('selected');
             navScroll.scrollToElement(document.querySelector('#nav li.selected'), 'auto', true, false);

             sightId = $('#wrapper').attr('sightId');

             currentPage = 1;
             currentPage = $('#list_' + tagId).attr('currentPage');
 
             html = '';
             wrapper = $('#wrapper');
             list_box = $('#list_box');
             scroller = $('#scroller');
             list = $('.list');
             currentList = 0;
             speed = 500;
             locationed = 0;

             params = {
                 page: currentPage,
                 pageSize: 15,
                 tags: tagId,
                 sightId: sightId
             };
         },
         initList: function() {
             //跳转到当前标签
             nextBox = $('#list_' + tagId);
             currentList = nextBox.index();
             currentList = Math.max(currentList, 0);
             bindEvents.scrollList(nextBox.width() * currentList, speed);
             getRemoteList.getList(params);
         },
         inintScroll: function() {
             myScroll = new IScroll('#wrapper', {
                 probeType: 2,
                 bindToWrapper: true,
                 scrollY: true,
                 mouseWheel: true,
                 click: true,
                 preventDefault: false
             });
             common.myScrollEvents(myScroll, pullUpAction, pullDownAction);

             function pullUpAction() {
                 //向上滑动加载列表
                 currentPage = Number($('#list_' + tagId).attr('currentPage'));
                 currentPage++;
                 $('#list_' + tagId).attr('currentPage', currentPage);
                 params.page = currentPage;
                 getRemoteList.getList(params);
             }

             function pullDownAction() {
                 //下拉加载最新数据
                 currentPage = 1;
                 $('#list_' + tagId).attr('currentPage', currentPage);
                 params.page = currentPage;
                 getRemoteList.getList(params);
             }
         },
         initSwipe: function() {
             LIST_WIDTH = wrapper.width();


             maxLists = $('#nav ul li').size();

             list_box.width(LIST_WIDTH * maxLists);
             list_box.find('.list').width(LIST_WIDTH);
             list_box.find('.list').css('height', 'initial');

             $('#pullDown,#pullUp').width(LIST_WIDTH);


             list_box.swipe({
                 triggerOnTouchEnd: true,
                 swipeStatus: swipeStatus,
                 //allowPageScroll: "vertical",
                 threshold: 75,
             });

             /**
              * Catch each phase of the swipe.
              * move : we drag the div
              * cancel : we animate back to where we were
              * end : we animate to the next image
              */
             function swipeStatus(event, phase, direction, distance) {
                 //If we are moving before swipe, and we are going L or R in X mode, or U or D in Y mode then drag.
                 if (phase == "move" && (direction == "left" || direction == "right")) {
                     var duration = 0;

                     if (direction == "left") {
                         bindEvents.scrollList((LIST_WIDTH * currentList) + distance, duration);
                     } else if (direction == "right") {
                         bindEvents.scrollList((LIST_WIDTH * currentList) - distance, duration);
                     }

                 } else if (phase == "cancel") {
                     bindEvents.scrollList(LIST_WIDTH * currentList, speed);
                 } else if (phase == "end") {
                     if (direction == "right") {
                         previousList();
                     } else if (direction == "left") {
                         nextList();
                     }
                 }
             }

             function previousList() {
                 list.css('visibility', 'visible');
                 currentList = Math.max(currentList - 1, 0);
                 bindEvents.scrollList(LIST_WIDTH * currentList, speed);

                 selected = $('#nav li.selected');
                 next = selected.prev();
                 if (!next.length) {
                     return;
                 }
                 getList();
             }

             function nextList() {
                 list.css('visibility', 'visible');
                 currentList = Math.min(currentList + 1, maxLists - 1);
                 bindEvents.scrollList(LIST_WIDTH * currentList, speed);

                 selected = $('#nav li.selected');
                 next = selected.next();
                 if (!next.length) {
                     return;
                 }
                 getList();

             }

             //获取新的数据
             function getList() {
                 //切换标签
                 selected.removeClass('selected');
                 next.addClass('selected');
                 navScroll.scrollToElement(document.querySelector('#nav li.selected'), 'auto', true, false);
                 tagId = next.attr('data-id');
                 params.tags = tagId;
                 nextBox = $('#list_' + tagId);
                 //$('#list_' + tagId).css('visibility', 'visible');
                 
                 //添加url参数
                 window.location.href = '#' + tagId;

                 //刷新myScroll 
                 scroller.css({
                     'height': nextBox.height() + 30 + 'px',
                     'min-height': wrapper.height() - 60 + 'px'
                 });
                 list_box.css({
                     'height': nextBox.height() + 'px',
                     'min-height': wrapper.height() - 60 + 'px'
                 });
                 setTimeout(function() {
                     myScroll.refresh();
                 }, 0);


                 //加载数据
                 //判断下一页是否有数据
                 if (!nextBox.find('li').size()) {
                     params.tags = tagId;
                     params.page = 1;
                     getRemoteList.getList(params);
                 }

             }

         },
         scrollList: function(distance, duration) {
             /**
              * Manually update the position of the imgs on drag
              */
             list_box.css("transition-duration", (duration / 1000).toFixed(1) + "s");
             //inverse the number we set in the css
             var value = (distance < 0 ? "" : "-") + Math.abs(distance).toString();
             /* if (value==0) {
                 alert('sss');
              }*/

             list_box.css("transform", "translate(" + value + "px,0)");
             setTimeout(function() {
                 $('#list_' + tagId).next().css('visibility', 'visible');
                 $('#list_' + tagId).prev().css('visibility', 'visible');
             }, duration);

         },
         initClick: function() {
             $('.list').delegate('li', 'click', function(event) {
                 var href = $(this).attr('href');
                 if (!href) {
                     return;
                 }
                 //common.COOKIES.setCookie('tagId', tagId, 'd1');
                 window.location = href;
                 // window.location = href +(!Number(tagId) ? '&' : '?') +'tagId=' + tagId + '&sd=' + sightId;
             })
             $('#nav').delegate('li', 'click', function(event) {

                 next = $(this);

                 //切换标签
                 selected = $('#nav li.selected');
                 selected.removeClass('selected');
                 next.addClass('selected');
                 navScroll.scrollToElement(document.querySelector('#nav li.selected'), 'auto', true, false);
                 tagId = next.attr('data-id');
                 params.tags = tagId;
                 nextBox = $('#list_' + tagId);
                 list.css('visibility', 'hidden');
                 nextBox.css('visibility', 'visible');

                 //添加url参数
                 window.location.href = '#' + tagId;

                 currentList = nextBox.index();
                 currentList = Math.max(currentList, 0);
                 bindEvents.scrollList(nextBox.width() * currentList, speed);




                 //刷新myScroll 
                 scroller.css({
                     'height': nextBox.height() + 30 + 'px',
                     'min-height': wrapper.height() - 60 + 'px'
                 });
                 list_box.css({
                     'height': nextBox.height() + 'px',
                     'min-height': wrapper.height() - 60 + 'px'
                 });
                 setTimeout(function() {
                     myScroll.refresh();
                 }, 0);

                 //加载数据
                 //判断下一页是否有数据
                 if (!nextBox.find('li').size()) {
                     params.tags = tagId;
                     params.page = 1;
                     getRemoteList.getList(params);
                 }
             })
         },
         initMap: function() {
             //加载地图，调用浏览器定位服务
             map = new AMap.Map('container', {
                 resizeEnable: true
             });
             map.plugin('AMap.Geolocation', function() {
                 geolocation = new AMap.Geolocation({
                     enableHighAccuracy: true, //是否使用高精度定位，默认:true
                     timeout: 10000, //超过10秒后停止定位，默认：无穷大
                     buttonOffset: new AMap.Pixel(10, 20), //定位按钮与设置的停靠位置的偏移量，默认：Pixel(10, 20)
                     zoomToAccuracy: true, //定位成功后调整地图视野范围使定位位置及精度范围视野内可见，默认：false
                     buttonPosition: 'RB'
                 });
                 map.addControl(geolocation);
                 geolocation.getCurrentPosition();
                 AMap.event.addListener(geolocation, 'complete', onComplete); //返回定位信息
                 AMap.event.addListener(geolocation, 'error', onError); //返回定位出错信息
             });

             function onComplete(data) {
                 //解析定位结果 
                 params.x = data.position.getLng();
                 params.y = data.position.getLat();
                 if (locationed == 0) {
                     locationed = 1;
                     params.tag = 'landscape';
                     getRemoteList.getList(params);
                 }
             }

             function onError(data) {
                 //解析定位错误信息
                 $('.list').html('<div class="error-text">啊哦，定位失败!<a href="javascript:void(0)" class="refresh-btn"  onclick="location.reload();">&nbsp</a></div>');

             }


         }


     };

     /**
      * 发送请求
      * @param  {[type]} page [页码]
      * @return {[type]}      [description]
      */
     var getRemoteList = {
         getList: function(params) {
             if (params.page == 1) {
                 //加载loading 
                 $('#pullDown').removeClass('hidden');
                 $('#pullDown .pullDownLabel').html('加载中...');
             }

             switch (params.tags) {
                 case 'food':
                     //美食列表
                     params.version = '1.1';
                     getFoodList.remote(params);
                     break;
                 case 'specialty':
                     //特产列表
                     params.version = '1.1';
                     getSpecialtyList.remote(params);
                     break;
                 case 'book':
                     //书籍列表
                     params.version = '1.1';
                     getBookList.remote(params);
                     break;
                 case 'video':
                     //视频列表
                     params.version = '1.1';
                     getVideoList.remote(params);
                     break;
                 case 'landscape':
                     //景观列表
                     params.version = '1.1';
                     if (!params.x) {
                         bindEvents.initMap();
                     } else {
                         getLandscapeList.remote(params);
                     }
                     break;
                 default:
                     // 话题列表 
                     params.version = '1.1';
                     getTopicList.remote(params);
                     break;
             }

         },
         getRemoteListSuccess: function() {
             //美食列表 成功 
             getFoodList.on('success', function(data) {
                 ajaxCallbackfun(data, "returnFoodList", $('#list_food'));
             });

             //特产列表 成功 
             getSpecialtyList.on('success', function(data) {
                 ajaxCallbackfun(data, "returnSpecialtyList", $('#list_specialty'));
             });
             //附近景观列表 成功 
             getLandscapeList.on('success', function(data) {
                 ajaxCallbackfun(data, "returnLandscapeList", $('#list_landscape'));
             });
             //话题列表 成功
             getTopicList.on('success', function(data) {
                 var tag_id = getRemoteList.getTagIdByData(data);
                 ajaxCallbackfun(data, "returnTopicList", $('#list_' + tag_id));
             });
             //视频列表 成功
             getVideoList.on('success', function(data) {
                 ajaxCallbackfun(data, "returnVideoList", $('#list_video'));
             });
             //书籍列表 成功
             getBookList.on('success', function(data) {
                 ajaxCallbackfun(data, "returnBookList", $('#list_book'));
             });

         },
         getTagIdByData: function(data) {
             var tag_id = tagId;
             if (data.length) {
                 tag_id = data[0].tagid;
             }
             return tag_id;
         }
     };




     /**
      * ajax返回后执行的函数
      * @param  {[type]} data    [ajax数据]
      * @param  {[type]} tpl     [模板名称] 
      * @return {[type]}         [description]
      */
     var ajaxCallbackfun = function(data, tpl, htmlContainer) {
         if (data.bizError) {
             renderError(data);
         } else {
             var pullUpEl = document.getElementById('pullUp');
             /* if (!data.length && currentPage == 1) {
                  htmlContainer.html(etpl.render('Error', {
                      msg: '当前没有数据哟,试试别的景点吧'
                  }));
                  return;
              }*/
             if (!data.length && currentPage > 1) {
                 currentPage--;
                 $('#list_' + tagId).attr('currentPage', currentPage);

                 //下拉没有更多啦
                 pullUpEl.className = 'color_hide';
                 pullUpEl.querySelector('.pullUpLabel').innerHTML = '全部加载完毕';

             } else {
                 renderHTML(tpl, data, htmlContainer);

                 //刷新myScroll
                 $('#pullDown').addClass('hidden');
                 scroller.css({
                     'height': htmlContainer.height() + 30 + 'px',
                     'min-height': wrapper.height() - 60 + 'px'
                 });
                 list_box.css({
                     'height': htmlContainer.height() + 'px',
                     'min-height': wrapper.height() - 60 + 'px'
                 });
                 setTimeout(function() {
                     myScroll.refresh();
                 }, 0);

             }
         }
     }

     /**
      * 渲染页面
      * @param {string} tpl 模板target
      * @param {*} data 请求返回数据
      */
     var renderHTML = function(tpl, data, htmlContainer) {
         // 格式化时间
         /*for (var i = 0, l = data.content.length; i < l; i++) {
             data.list[i].timeInfo = moment.unix(data.list[i].tenderTime).format('YYYY-MM-DD HH:mm');
             if (data.list[i].endTime) {
                 data.list[i].endTimeInfo = moment.unix(data.list[i].endTime).format(FORMATER);
             }
         }*/
         if (currentPage == 1) {
             html = etpl.render(tpl, {
                 list: data,
                 currentPage: currentPage
             });
         } else {
             html = html + etpl.render(tpl, {
                 list: data,
                 currentPage: currentPage
             });
         }
         htmlContainer.html(html);
         $('.list .content-box .topic_name,.list .item .content,.list .title-box .describe').dotdotdot();
     }

     /**
      * 渲染错误提示
      * @param {*} data 请求返回的错误提示
      */
     var renderError = function(data, htmlContainer) {
         htmlContainer.render(etpl.render('Error', {
             msg: data.statusInfo
         }));
     }


     return {
         init: init
     };
 });
