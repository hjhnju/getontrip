 /*
             视频列表
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
                         d.params = {};
                         if ($('#form-title').val()) {
                             d.params.title = $('#form-title').val();
                         }
                         if ($("#form-sight").attr('data-sight_id')) {
                             d.params.sight_id = Number($.trim($("#form-sight").attr('data-sight_id')));
                         }
                         if ($("#form-status").val()) {
                             d.params.status = Number($.trim($("#form-status").val()));
                         }

                         if ($("#form-type").val()) {
                             d.params.type = Number($.trim($("#form-type").val()));
                         }
                     }
                 },
                 "columnDefs": [{
                     "targets": [1],
                     "orderable": false,
                     "width": 150
                 }, {
                     "targets": [0, 3, 4, 5, 8],
                     "orderable": false,
                     "width": 50
                 }, {
                     "targets": [2, 6, 7],
                     "orderable": false,
                     "width": 100
                 }],
                 "columns": [{
                     "data": 'id'
                 }, {
                     "data": 'title'
                 }, {
                     "data": function(e) {
                         if (e.image) {
                             return '<a href="/pic/' + e.image + '" target="_blank"><img alt="" src="' + e.image.getNewUrlByUrl(80, 22, 'f') + '"/></a><button type="button" class="btn btn-primary btn-xs editPic" title="修改图片" data-toggle="tooltip" style="margin: 0px 0px 0px 5px;"><i class="fa fa-image"></i></button>';
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
                     'data': function(e) {
                         var str = '';
                         if (e.sights.length) {
                             var sight = e.sights;
                             for (var i = 0; i < sight.length; i++) {
                                 str = str + '  ' + sight[i].name + '[' + sight[i].weight + ']';
                             };
                             return str + '  <button class="btn btn-primary  btn-xs weight" title="修改排序" data-toggle="tooltip"><i class="fa fa-reorder"></i></button>';
                         }
                         return '暂无';
                         /*  if (e.weight) {
                             return e.weight + '  <button class="btn btn-primary  btn-xs weight" title="修改排序" data-toggle="tooltip"><i class="fa fa-reorder"></i></button>'
                         }*/
                     }
                 }, {
                     "data": function(e) {
                         if (e.statusName == '未发布') {
                             return e.statusName + '<button type="button" class="btn btn-primary btn-xs publish" title="发布" data-toggle="tooltip" ><i class="fa fa-check-square-o"></i></button><button type="button" class="btn btn-danger btn-xs to-black" title="加入黑名单" data-toggle="tooltip" ><i class="fa fa-frown-o"></i></button>';
                         } else if (e.statusName == '已发布') {
                             return e.statusName + '<button type="button" class="btn btn-warning btn-xs cel-publish" title="取消发布" data-toggle="tooltip" ><i class="fa fa-close"></i></button><button type="button" class="btn btn-danger btn-xs to-black" title="加入黑名单" data-toggle="tooltip" ><i class="fa fa-frown-o"></i></button>';
                         } else {
                             return e.statusName + '<button type="button" class="btn btn-default btn-xs cel-black" title="取消黑名单" data-toggle="tooltip" ><i class="fa fa-smile-o"></i></button>';
                         }

                     }
                 }, {
                     "data": function(e) {
                         return '<a class="btn btn-primary btn-xs edit" title="编辑" data-toggle="tooltip" href="/admin/video/edit?action=edit&id=' + e.id + '"><i class="fa fa-pencil"></i></a>';

                         return '<a class="btn btn-primary btn-xs edit" title="编辑" data-toggle="tooltip" href="/admin/video/edit?action=edit&id=' + e.id + '"><i class="fa fa-pencil"></i></a>' + '<button type="button" class="btn btn-danger btn-xs delete"  title="删除" data-toggle="tooltip"><i class="fa fa-trash-o "></i></button>';

                     }
                 }],
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
                 this.init_table();
                 this.initEvents();
                 this.editPic();
             },
             init_table: function() {

                 //绑定draw事件
                 $('#editable').on('draw.dt', function() {
                     //工具提示框
                     $('[data-toggle="tooltip"]').tooltip();

                     //绑定选择事件
                     $('#editable tbody tr').click(function(event) {
                         $(this).toggleClass('selected');
                     });
                 });


             },
             initEvents: function() {

                 //状态下拉列表 
                 $('#form-status').selectpicker();

                 //类型下拉列表 
                 $('#form-type').selectpicker();

                 //删除操作
                 $('#editable button.delete').live('click', function(e) {
                     e.preventDefault();
                     if (confirm("确定删除么 ?") == false) {
                         return;
                     }
                     var nRow = $(this).parents('tr')[0];
                     var data = oTable.api().row(nRow).data();
                     $.ajax({
                         "url": "/admin/videoapi/del",
                         "data": data,
                         "type": "post",
                         "error": function(e) {
                             alert("服务器未正常响应，请重试");
                         },
                         "success": function(response) {
                             if (response.status == 0) {
                                 toastr.success('删除成功');
                                 //刷新当前页
                                 oTable.fnRefresh();
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
                     if (confirm("确定加入黑名单么 ?加入黑名单后，将不再抓取该视频，且该视频与景点的关系将解除。") == false) {
                         return;
                     }
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

                 //修改权重操作 
                 $('#editable button.weight').live('click', function(e) {
                     e.preventDefault();
                     var nRow = $(this).parents('tr')[0];
                     var data = oTable.api().row(nRow).data();


                     if (!$('#form-sight').attr('data-sight_id')) {
                         toastr.warning('请先选择一个景点！');
                         $('#form-sight').focus();
                         return false;
                     }
                     sight_name = $('#form-sight').val();
                     sight_id = $('#form-sight').attr('data-sight_id');
                     var params = {
                         'sight_id': sight_id,
                         'order': '`weight` asc'
                     };
                     //查询当前景点下的所有视频
                     $.ajax({
                         "url": "/admin/videoapi/list",
                         "data": {
                             params: params
                         },
                         "type": "post",
                         "error": function(e) {
                             alert("服务器未正常响应，请重试");
                         },
                         "success": function(response) {
                             var data = response.data.data;
                             var li = '';
                             $.each(data, function(key, value) {
                                 li = li + '<li class="list-primary" data-id="' + value.id + '"><div class="task-title"><span class="key" data-key="' + (key + 1) + '">【' + (key + 1) + '】</span><span class="task-title-sp">' + value.title + '</span><span class="badge badge-sm label-info">' + sight_name + '</span></div></li>'
                             });
                             $('#sortable').html(li);
                             $("#sortable").sortable({
                                 //revert: true,
                                 start: function(d, li) {
                                     oldNum = $(li.item).index() + 1
                                 },
                                 stop: function(d, li) {
                                     newNum = $(li.item).index() + 1
                                     if (oldNum === newNum) {
                                         return;
                                     }
                                     changeWeight($(li.item).attr('data-id'), oldNum, newNum, sight_id);
                                 }
                             });
                             //弹出模态框
                             $('#myModal').modal();
                         }
                     });

                     function changeWeight(id, from, to, sight_id) {
                         $.ajax({
                             "url": "/admin/videoapi/changeWeight",
                             "data": {
                                 id: id,
                                 to: to,
                                 sightId: sight_id
                             },
                             "type": "post",
                             "error": function(e) {
                                 alert("服务器未正常响应，请重试");
                             },
                             "success": function(response) {
                                 api.ajax.reload();

                                 //序号更新
                                 var $span = $('#sortable span[data-key="' + from + '"]');

                                 if (from < to) {
                                     for (var i = (from + 1); i <= to; i++) {
                                         var $ospan = $('#sortable span[data-key="' + i + '"]').html('【' + (i - 1) + '】');
                                         $ospan.attr('data-key', i - 1);
                                     }
                                 } else {
                                     for (var i = (from - 1); i >= to; i--) {
                                         var $ospan = $('#sortable span[data-key="' + i + '"]').html('【' + (i + 1) + '】');
                                         $ospan.attr('data-key', i + 1);
                                     }
                                 }
                                 //最后处理移动的
                                 $span.html('【' + to + '】');
                                 $span.attr('data-key', to);


                             }
                         });
                     }

                 });

                 //批量操作
                 $('#editable button.all-action').live('click', function(e) {
                     e.preventDefault();
                     var datas = oTable.api().rows('.selected').data();
                     var idArray = [];
                     var action = $(this).attr('data-action');
                     if (action == 'BLACKLIST') {
                         if (confirm("确定加入黑名单么 ?加入黑名单后，将不再抓取该视频，且该视频与景点的关系将解除。") == false) {
                             return;
                         }
                     }
                     for (var i = 0; i < datas.length; i++) {
                         var data = datas[i];
                         /* if (action=='PUBLISHED'&&!data.image) {
                              toastr.warning('发布之前必须上传背景图片');
                              return;
                          }*/
                         idArray.push(data.id);
                     };
                     if (!idArray.length) {
                         toastr.warning('请选择一行！');
                         return false;
                     }
                     var publish = new Remoter('/admin/videoapi/changeStatus');
                     publish.remote({
                         idArray: idArray,
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
             //输入内容点击回车查询
             $("#form-title").keydown(function(event) {
                 if (event.keyCode == 13) {
                     api.ajax.reload();
                 }
             });

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

             $('#form-status,#form-type').change(function(event) {
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
