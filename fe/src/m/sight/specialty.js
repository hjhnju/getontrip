 /**
  * @ignore
  * @file specialty.js
  * @author fanyy(1178223444@qq.com)
  * @time 15-1-15
  */

 define(function(require) {
     var $ = require('jquery');
     var common = require('common/mcommon');
     var etpl = require('etpl');
     var tpl = require('./list.tpl');

     var Remoter = require('common/Remoter');
     var getTopicList = new Remoter('SPECIALTY_TOPIC');
     var getProductList = new Remoter('SPECIALTY_PRODUCT');

     var currentPage = 1;
     var topicContainer = $('#topic_list');
     var productContainer = $('#product_list');

     function init() {
         common.init();
         etpl.compile(tpl);
         bindEvents.init();
         getRemoteList.init();
     }

     /*
      *绑定事件
      */
     var bindEvents = {
         init: function() {
             this.initPage();
         },
         initPage: function() {
             $('.page-box .next,.page-box .previous').click(function(event) {
                 if ($(this).hasClass('no-action')) {
                     return;
                 }
                 var parent = $(this).parent();
                 var type = parent.attr('type');
                 var action = $(this).attr('action');
                 var totalPage = parent.attr('total');
                 var currentPage = parent.attr('current');
                 var pageSize = parent.attr('pageSize');
                 var id = parent.attr('postid');
                 if (action == 'next') {
                     currentPage++;
                 } else {
                     currentPage--;
                 }
                 if (currentPage == totalPage) {
                     parent.find('.next').addClass('no-action');
                 }
                 if (currentPage < totalPage) {
                     parent.find('.next').removeClass('no-action');
                 }
                 if (currentPage > 1) {
                     parent.find('.previous').removeClass('no-action');
                 }
                 if (currentPage == 1) {
                     parent.find('.previous').addClass('no-action');
                 }
                 parent.attr('current', currentPage);

                 param = {
                     id: id,
                     page: currentPage,
                     pageSize: pageSize
                 }

                 switch (type) {
                     case 'topic':
                         getTopicList.remote(param);
                         break;
                     case 'product':
                         getProductList.remote(param);
                         break;
                 }

             });

         }

     };

     /**
      * 发送请求
      * @param  {[type]} page [页码]
      * @return {[type]}      [description]
      */
     var getRemoteList = {
         init: function() {
             this.getTopicList();
             this.getProductList();
         },
         getTopicList: function(data) {
             // 相关话题列表 
             //成功
             getTopicList.on('success', function(data) {
                 ajaxCallbackfun(data, topicContainer, "returnTopicList");
             });

         },
         getProductList: function(data) {
             // 推荐名品列表 
             //成功
             getProductList.on('success', function(data) {
                 ajaxCallbackfun(data, productContainer, "returnProductList");
             });

         }
     };


     /**
      * ajax返回后执行的函数
      * @param  {[type]} data    [ajax数据]
      * @param  {[type]} htmlContainer     [容器名称] 
      * @param  {[type]} tpl     [模板名称] 
      * @return {[type]}         [description]
      */
     var ajaxCallbackfun = function(data, htmlContainer, tpl) {
         htmlContainer.html(etpl.render('Loading'));
         if (data.bizError) {
             renderError(data);
         } else {
             if (!data.length && currentPage == 1) {
                 htmlContainer.html(etpl.render('Error', {
                     msg: '当前没有数据哟'
                 }));
                 return;
             }
             renderHTML(htmlContainer, tpl, data);
         }
     }

     /**
      * 渲染页面
      * @param {string} tpl 模板target
      * @param {*} data 请求返回数据
      */
     var renderHTML = function(htmlContainer, tpl, data) {
         htmlContainer.html(etpl.render(tpl, {
             list: data
         }));
     }

     /**
      * 渲染错误提示
      * @param {*} data 请求返回的错误提示
      */
     var renderError = function(htmlContainer, data) {
         htmlContainer.render(etpl.render('Error', {
             msg: data.statusInfo
         }));
     }


     return {
         init: init
     };
 });
