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
     var etpl = require('etpl');
     var tpl = require('./list.tpl');

     var Remoter = require('common/Remoter');


     var getFoodList = new Remoter('FOOD_LIST');
     var IScroll = require('common/iscroll');
     var myScroll,
         pullDownEl, pullDownOffset,
         pullUpEl, pullUpOffset,
         generatedCount = 0;
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

     /*
      *绑定事件
      */
     var bindEvents = {
         init: function() {
             this.initList();
             this.inintScroll();
             getRemoteListSuccess();
         },
         initList: function() {
             //解析定位结果 
             params = {
                 page: currentPage,
                 pageSize: 15,
                 tags: tagId,
                 sightId: sightId
             }
             getRemoteList.getFoodList(params);
         },
         inintScroll: function() {
             myScroll = new IScroll('#wrapper', {
                 probeType: 2,
                 bindToWrapper: true,
                 scrollY: true,
                 mouseWheel: true,
                 click: true
             });
             common.myScrollEvents(myScroll, bindEvents.pullUpAction, bindEvents.pullDownAction);

             setTimeout(function() {
                 document.getElementById('wrapper').style.left = '0';
             }, 800);
             document.addEventListener('touchmove', function(e) {
                 e.preventDefault();
             }, false); 
         },
         pullUpAction: function() {
             //向上滑动加载列表
             currentPage++;
             params.page = currentPage;
             getRemoteList.getFoodList(params);
             myScroll.refresh(); //刷新滚动框
         },
         pullDownAction: function() {
             //下拉加载最新数据
             currentPage = 1;
             params.page = currentPage;
             getRemoteList.getFoodList(params);
             myScroll.refresh();
         }


     };

     /**
      * 发送请求
      * @param  {[type]} page [页码]
      * @return {[type]}      [description]
      */
     var getRemoteList = {
         getFoodList: function(data) {
             // 美食列表
             getFoodList.remote(data);


         }
     };


     /**
      * 发送请求 
      * @param  {[type]} page [页码]
      * @return {[type]}      [description]
      */
     var getRemoteListSuccess = function() {
         //美食列表 成功 
         getFoodList.on('success', function(data) {
             ajaxCallbackfun(data, "returnFoodList");
         });
     }


     /**
      * ajax返回后执行的函数
      * @param  {[type]} data    [ajax数据]
      * @param  {[type]} tpl     [模板名称] 
      * @return {[type]}         [description]
      */
     var ajaxCallbackfun = function(data, tpl) {
         if (data.bizError) {
             renderError(data);
         } else {
             var pullUpEl = document.getElementById('pullUp');
             if (!data.length && currentPage == 1) {
                 htmlContainer.html(etpl.render('Error', {
                     msg: '当前没有数据哟,试试别的景点吧'
                 }));
                 return;
             }
             if (!data.length && currentPage > 1) {
                 //下拉没有更多啦
                 currentPage--;
                 pullUpEl.className = '';
                 pullUpEl.querySelector('.pullUpLabel').innerHTML = '全部加载完毕';

             } else {
                 renderHTML(tpl, data);
                 //刷新myScroll
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
     var renderHTML = function(tpl, data) {
         // 格式化时间
         /*for (var i = 0, l = data.content.length; i < l; i++) {
             data.list[i].timeInfo = moment.unix(data.list[i].tenderTime).format('YYYY-MM-DD HH:mm');
             if (data.list[i].endTime) {
                 data.list[i].endTimeInfo = moment.unix(data.list[i].endTime).format(FORMATER);
             }
         }*/
         if (currentPage == 1) {
             html = etpl.render(tpl, {
                 list: data
             });
         } else {
             html = html + etpl.render(tpl, {
                 list: data
             });
         }
         htmlContainer.html(html);
         $('#food_list .content-box .content').dotdotdot();
     }

     /**
      * 渲染错误提示
      * @param {*} data 请求返回的错误提示
      */
     var renderError = function(data) {
         htmlContainer.render(etpl.render('Error', {
             msg: data.statusInfo
         }));
     }


     return {
         init: init
     };
 });
