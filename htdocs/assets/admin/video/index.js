 /*

       京东书籍列表
        author:fyy
       */
 $(document).ready(function() {
     var List = function() {
         var editBtn = '<a class="btn btn-primary btn-xs edit" title="编辑" data-toggle="tooltip"><i class="fa fa-pencil"></i></a>' + '<button type="button" class="btn btn-success btn-xs addKeyword"  title="删除" data-toggle="tooltip"><i class="fa fa-trash-o "></i></button>';
         var FORMATER = 'YYYY-MM-DD HH:mm:ss';
         /**
          * 初始化表格 
          */
         var initTable = function() {
                 oTable = $('#editable').dataTable({
                     "serverSide": true, //分页，取数据等等的都放到服务端去
                     "processing": true, //载入数据的时候是否显示“载入中”
                     "pageLength": 10, //首次加载的数据条数  
                     "searching": false, //是否开启本地分页
                     "ordering": false,
                     "ajax": {
                         "url": "/admin/videoapi/list",
                         "type": "POST",
                         "data": function(d) {
                             //添加额外的参数传给服务器
                             d.sight_id = 1;
                             if ($("#form-sight").attr('data-sight_id')) {
                                 d.sight_id = $.trim($("#form-sight").attr('data-sight_id'));
                             }
                         }
                     },
                     "columnDefs": [{
                         "targets": [1, 2],
                         "orderable": false,
                         "width": 150
                     }],
                     "columns": [{
                             "data": 'id'
                         }, {
                             "data": 'title'
                         }, {
                             "data": function(e) {
                                 if (e.image) {
                                     return '<a href="/pic/' + e.image + '" target="_blank"><img alt="" src="' + e.image.getNewUrlByUrl(80, 22, 'f') + '"/></a><button type="button" class="btn btn-primary btn-xs editPic" title="修改图片" data-toggle="tooltip" ><i class="fa fa-image"></i></button>';
                                 }
                                 return '暂无';
                             }
                         }, {
                             "data": 'from'
                         }, {
                             "data": 'typeName'
                         }, {
                             "data": function(e) {
                                 if (e.url) {
                                     return '<a href="' + e.url + '" target="_blank" title="' + e.url + '">' + (e.url.length > 20 ? e.url.substr(0, 20) + '...' : e.url) + '</a>';
                                 }
                                 return '暂无';
                             }
                         }, {
                             "data": function(e) {
                                 if (e.statusName == '未发布') {
                                     return e.statusName + '<button type="button" class="btn btn-primary btn-xs publish" title="发布" data-toggle="tooltip" ><i class="fa fa-check-square-o"></i></button><button type="button" class="btn btn-default btn-xs to-black" title="加入黑名单" data-toggle="tooltip" ><i class="fa fa-frown-o"></i></button>';
                                 } else if (e.statusName == '已发布') {
                                     return e.statusName + '<button type="button" class="btn btn-warning btn-xs cel-publish" title="取消发布" data-toggle="tooltip" ><i class="fa fa-close"></i></button><button type="button" class="btn btn-default btn-xs to-black" title="加入黑名单" data-toggle="tooltip" ><i class="fa fa-frown-o"></i></button>';
                                 } else {
                                     return e.statusName + '<button type="button" class="btn btn-default btn-xs cel-black" title="取消黑名单" data-toggle="tooltip" ><i class="fa fa-smile-o"></i></button>';
                                 }

                             }
                         },
                         /* {
                                                  "data": function(e) {
                                                      if (e.create_time) {
                                                          return moment.unix(e.create_time).format(FORMATER);
                                                      }
                                                      return "空";
                                                  }
                                              }, */
                         {
                             "data": function(e) {
                                 return '';
                                 //评论
                                 return '<a href="/admin/comment/list?id=' + e.id + '&table=video" target="_blank" class="btn btn-warning btn-xs comments" title="评论列表" data-toggle="tooltip"><i class="fa fa-comments-o"></i></a>';
                                 return '<a class="btn btn-success btn-xs edit" title="查看" data-toggle="tooltip" href="/admin/keyword/edit?action=view&id=' + e.id + '"><i class="fa fa-eye"></i></a><a class="btn btn-primary btn-xs edit" title="编辑" data-toggle="tooltip" href="/admin/keyword/edit?action=edit&id=' + e.id + '"><i class="fa fa-pencil"></i></a>' + '<button type="button" class="btn btn-danger btn-xs delete"  title="删除" data-toggle="tooltip"><i class="fa fa-trash-o "></i></button>';
                             }
                         }
                     ],
                     "initComplete": function(setting, json) {
                         //工具提示框
                         //$('[data-toggle="tooltip"]').tooltip();
                     }
                 });

                 api = oTable.api();
             }
             /**
              * 绑定事件
              *  
              */
         var bindEvents = {
             init: function() {
                 this.initEvents();
                 this.editPic();
             },
             initEvents: function() {
                 //绑定draw事件
                 $('#editable').on('draw.dt', function() {
                     //工具提示框
                     $('[data-toggle="tooltip"]').tooltip();
                 });

                 //删除操作
                 $('#editable button.delete').live('click', function(e) {
                     e.preventDefault();
                     if (confirm("确定删除么 ?") == false) {
                         return;
                     }
                     var nRow = $(this).parents('tr')[0];
                     var data = oTable.api().row(nRow).data();
                     $.ajax({
                         "url": "/admin/Keywordsapi/del",
                         "data": data,
                         "type": "post",
                         "error": function(e) {
                             alert("服务器未正常响应，请重试");
                         },
                         "success": function(response) {
                             if (response.status == 0) {
                                 toastr.success('删除成功');
                                 oTable.fnDeleteRow(nRow);
                                 oTable.fnDraw();
                             }
                         }
                     });
                 });

                 //发布操作
                 $('#editable button.publish,#editable button.cel-publish').live('click', function(e) {
                     e.preventDefault();
                     var nRow = $(this).parents('tr')[0];
                     var data = oTable.api().row(nRow).data();
                     var action;
                     if ($(this).hasClass('publish')) {
                         if (!data.image) {
                             toastr.warning('发布之前必须上传背景图片');
                             return;
                         }
                         action = 'PUBLISHED';
                     } else {
                         action = 'NOTPUBLISHED';
                     }
                     var publish = new Remoter('/admin/videoapi/save');
                     publish.remote({
                         id: data.id,
                         action: action
                     });
                     publish.on('success', function(data) {
                         //刷新当前页
                         oTable.fnRefresh();
                     });

                 });

                 //黑名单操作操作
                 $('#editable button.to-black,#editable button.cel-black').live('click', function(e) {
                     e.preventDefault();
                     var nRow = $(this).parents('tr')[0];
                     var data = oTable.api().row(nRow).data();
                     var action;
                     if ($(this).hasClass('to-black')) {
                         action = 'BLACKLIST';
                     } else {
                         action = 'NOTPUBLISHED';
                     }
                     var publish = new Remoter('/admin/videoapi/save');
                     publish.remote({
                         id: data.id,
                         action: action
                     });
                     publish.on('success', function(data) {
                         //刷新当前页
                         oTable.fnRefresh();
                     });

                 });
             },
             editPic: function() {
                 //上传图片，得到url 
                 $('#editable button.editPic').live('click', function(e) {
                     e.preventDefault();
                     var nRow = $(this).parents('tr')[0];
                     var data = oTable.api().row(nRow).data();
                     $('#uploadpicModal .modal-title').html('修改图片：' + data.title);
                     $('#imageView').html('<img src="/pic/' + data.image + '@f190w_f80h" alt="">').addClass('imagedis');
                     $('#image-id').val(data.id);
                     $('#ModalUpload-btn').removeClass('hide');
                     $('#uploadpicModal').modal();
                 });

                 $("#upload-img").click(function(event) {
                     $.ajaxFileUpload({
                         url: '/upload/pic?filename=' + $('#form-filename').val(),
                         secureuri: false,
                         fileElementId: 'imageBtn',
                         dataType: 'json',
                         success: function(res, status) {
                             $('#image').val(res.data.image);
                             $('#imageView').html('<img src="/pic/' + res.data.image.getNewImgByImg(190, 80, 'f') + '"  alt=""/>');
                             $('#imageView').removeClass('imageView');

                         },
                         error: function(data, status, e) {
                             alert(status.statusInfo);
                         }
                     })
                 });

                 $("#ModalUpload-btn").click(function(event) {
                     var upload = new Remoter('/admin/videoapi/save');
                     upload.remote({
                         id: $('#image-id').val(),
                         image: $('#image').val()
                     });
                     upload.on('success', function(data) {
                         $('#uploadpicModal').modal('hide');
                         //刷新当前页
                         oTable.fnRefresh();
                     });
                 });
             }
         }

         /*
              过滤事件
         */
         var filter = function() {
             //景点输入框自动完成
             $('#form-sight').typeahead({
                 display: 'name',
                 val: 'id',
                 ajax: {
                     url: '/admin/sightapi/getSightList',
                     triggerLength: 1
                 },
                 itemSelected: function(item, val, text) {
                     $("#form-sight").val(text);
                     $("#form-sight").attr('data-sight_id', val);
                     //触发dt的重新加载数据的方法
                     api.ajax.reload();
                 }
             });

             //景点框后的清除按钮，清除所选的景点
             $('#clear-sight').click(function(event) {
                 $("#form-sight").val('');
                 $("#form-sight").attr('data-sight_id', '');
                 //触发dt的重新加载数据的方法
                 api.ajax.reload();
             });
         }
         return {
             init: function() {
                 initTable()
                 bindEvents.init();
                 filter();
             }
         }
     }

     new List().init();
 });
