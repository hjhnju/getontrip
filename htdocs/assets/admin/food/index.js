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
                     "url": "/admin/foodapi/list",
                     "type": "POST",
                     "data": function(d) {
                         //添加额外的参数传给服务器 
                         d.params = {};
                         if ($('#form-title').val()) {
                             d.params.title = $('#form-title').val();
                         }
                         if ($("#form-sight").attr('data-sight_id')) {
                             d.params.destination_id   = Number($.trim($("#form-sight").attr('data-sight_id')));
                             d.params.destination_type = 2;
                         }
                         if ($("#form-city").attr('data-city_id')) {
                             d.params.destination_id = Number($.trim($("#form-city").attr('data-city_id')));
                             d.params.destination_type = 3;
                         }
                         if ($("#form-status").val()) {
                             d.params.status = Number($.trim($("#form-status").val()));
                         }
                     }
                 },
                 "columnDefs": [{
                     "targets": [1],
                     "orderable": false,
                     "width": 150
                 }, {
                     "targets": [0, 3],
                     "orderable": false,
                     "width": 50
                 }, {
                     "targets": [2],
                     "orderable": false,
                     "width": 100
                 }],
                 "columns": [{
                	 "data": function(e){
                     	return '<span class="maintd_'+e.id+'" data-rowspan="'+e.dest.length+'">'+e.id+'</span>';
                     }
                 }, {
                	 "data": function(e){
                      	return '<span class="maintd_'+e.id+'" data-rowspan="'+e.dest.length+'">'+e.title+'</span>';
                     }
                 }, {
                     "data": function(e) {
                         if (e.image) {
                             return '<span class="maintd_'+e.id+'" data-rowspan="'+e.dest.length+'">'+'<a href="/pic/' + e.image + '" target="_blank"><img alt="" src="' + e.image.getNewUrlByUrl(80, 22, 'f') + '"/></a><button type="button" class="btn btn-primary btn-xs editPic" title="修改图片" data-toggle="tooltip" style="margin: 0px 0px 0px 5px;"><i class="fa fa-image"></i></button>'+'</span>';
                         }
                         return '暂无';
                     }
                 }, {
                	 "data": function(e){
                       	return '<span class="maintd_'+e.id+'" data-rowspan="'+e.dest.length+'">'+e.content+'</span>';
                      }
                 },  {
                     "data": function(e) {
                    	 if (e.statusName == '未发布') {
                             return '<span class="maintd_'+e.id+'" data-rowspan="'+e.dest.length+'">'+e.statusName + '<button type="button" class="btn btn-primary btn-xs publish" title="发布" data-toggle="tooltip" ><i class="fa fa-check-square-o"></i></button><button type="button" class="btn btn-danger btn-xs to-black" title="加入黑名单" data-toggle="tooltip" ><i class="fa fa-frown-o"></i></button>'+'</span>';
                         } else if (e.statusName == '已发布') {
                             return '<span class="maintd_'+e.id+'" data-rowspan="'+e.dest.length+'">'+e.statusName + '<button type="button" class="btn btn-warning btn-xs cel-publish" title="取消发布" data-toggle="tooltip" ><i class="fa fa-close"></i></button><button type="button" class="btn btn-danger btn-xs to-black" title="加入黑名单" data-toggle="tooltip" ><i class="fa fa-frown-o"></i></button>'+'</span>';
                         } else {
                             return '<span class="maintd_'+e.id+'" data-rowspan="'+e.dest.length+'">'+e.statusName + '<button type="button" class="btn btn-default btn-xs cel-black" title="取消黑名单" data-toggle="tooltip" ><i class="fa fa-smile-o"></i></button>'+'</span>';
                         }

                    }
                 },{
                     'data': function(e) {
                         var str = '';
                         if (e.shop.length) {
                             var shop = e.shop;
                             for (var i = 0; i < shop.length-1; i++) {
                                 str +=  shop[i].name + '<br>';
                             };
                             str +=  shop[i].name;
                             return '<span class="maintd_'+e.id+'" data-rowspan="'+e.dest.length+'">'+str+'</span>';
                         }
                         return '<span class="maintd_'+e.id+'" data-rowspan="'+e.dest.length+'">'+str+'</span>';
                         /*  if (e.weight) {
                             return e.weight + '  <button class="btn btn-primary  btn-xs weight" title="修改排序" data-toggle="tooltip"><i class="fa fa-reorder"></i></button>'
                         }*/
                     }
                 },{
                     'data': function(e) {
                    	 $parent = $('.maintd_'+e.id).parent().parent();  
                         var className = 'danger';
                         for (var i = e.dest.length-1; i >=1; i--) {
                        	 if(e.dest[i].type == 2){
                        		 className = 'danger';
                        	 }else{
                        		 className = 'warning';
                        	 }
                             $tr = $('<tr id="group_'+e.id+'_'+i+'" role="row"></tr>'); 
                             $tr.addClass($parent.attr('class')); 
                             $tr.addClass('group');
                             $tr.append('<td>'+'<span class="label label-'+className+'" id="'+e.dest[i].id+'" data-type="'+e.dest[i].type+'" name="'+e.dest[i].name+'">'+e.dest[i].name+ '[' + e.dest[i].weight + ']</span>'+ ' <button class="btn btn-primary  btn-xs weight" title="修改排序" data-toggle="tooltip"><i class="fa fa-reorder"></i></button>' +'</td>'); 
                             $parent.after($tr); 
                         } 
                         if (e.dest==0) {
                         	return '<span class="label label-'+className+'"  id="" name=""></span>';
                         } 
                         if(e.dest[0].type == 2){
                    		 className = 'danger';
                    	 }else{
                    		 className = 'warning';
                    	 }
                         return '<span class="label label-'+className+'"  id="'+e.dest[0].id+'" data-type="'+e.dest[0].type+'" name="'+e.dest[0].name+'">'+e.dest[0].name+ '[' + e.dest[0].weight + ']</span>'+ ' <button class="btn btn-primary  btn-xs weight" title="修改排序" data-toggle="tooltip"><i class="fa fa-reorder"></i></button>';
                     }
                 }, {
                     "data": function(e) {
                         return '<span class="maintd_'+e.id+'" data-rowspan="'+e.dest.length+'">'+'<a class="btn btn-primary btn-xs edit" title="编辑" data-toggle="tooltip" href="/admin/food/edit?action=edit&id=' + e.id + '"><i class="fa fa-pencil"></i></a></span>';
                     }
                 }],
                 "initComplete": function(setting, json) {
                     //工具提示框
                     //$('[data-toggle="tooltip"]').tooltip();
                	 $.each($('[data-rowspan]'),function (e) {
                         $(this).parent().attr('rowspan',$(this).attr('data-rowspan'));
                     })
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
                         "url": "/admin/foodapi/del",
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
                         if (!data.title || !data.content) {
                             toastr.warning('发布之前请将信息补全！');
                             return;
                         }
                         action = 'PUBLISHED';
                     } else {
                         action = 'NOTPUBLISHED';
                     }
                     var publish = new Remoter('/admin/foodapi/save');
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
                     var publish = new Remoter('/admin/foodapi/save');
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
                     /* var nRow = $(this).parents('tr')[0];
                     var data = oTable.api().row(nRow).data();*/

                     dest_name = $(this).prev().attr('name');
                     var destination_id = $(this).prev().attr('id');
                     var destination_type = $(this).prev().attr('data-type');
                     var params = {
                         destination_id: $(this).prev().attr('id'),
                         destination_type: $(this).prev().attr('data-type'),
                         order: '`weight` asc',
                         action: 'PUBLISHED'
                     };
                     //查询当前景点下的所有视频
                     $.ajax({
                         "url": "/admin/foodapi/list",
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
                                 li = li + '<li class="list-primary" data-id="' + value.id + '" data-weight="' + value.weight + '" data-key="' + (key + 1) + '"><div class="task-title"><span class="key" data-key="' + (key + 1) + '">【' + (key + 1) + '】</span><span class="task-title-sp">' + value.title + '</span><span class="badge badge-sm label-info">' + dest_name + '</span></div></li>'
                             });
                             $('#sortable').html(li);
                             $("#sortable").sortable({
                                 //revert: true,
                                 start: function(d, li) {
                                     oldIndex = $(li.item).index() + 1;
                                     oldNum = Number($('#sortable li[data-key="' + oldIndex + '"]').attr('data-weight'));
                                 },
                                 stop: function(d, li) {
                                     newIndex = $(li.item).index() + 1;
                                     newNum = Number($('#sortable li[data-key="' + newIndex + '"]').attr('data-weight'));

                                     if (oldNum === newNum) {
                                         return;
                                     }
                                     if (oldIndex < newIndex) {
                                         newNum++;
                                     }
                                     changeWeight($(li.item).attr('data-id'), oldNum, newNum, destination_id,destination_type, oldIndex, newIndex);
                                 }
                             });
                             //弹出模态框
                             $('#myModal').modal();
                         }
                     });

                     function changeWeight(id, from, to, destination_id, destination_type,fromIndex, toIndex) {
                         $.ajax({
                             "url": "/admin/foodapi/changeWeight",
                             "data": {
                                 id: id,
                                 to: to,
                                 destination_id: destination_id,
                                 destination_type: destination_type,
                             },
                             "type": "post",
                             "error": function(e) {
                                 alert("服务器未正常响应，请重试");
                             },
                             "success": function(response) {
                                 api.ajax.reload();

                                 //序号更新, 权重更新
                                 var $span = $('#sortable span[data-key="' + fromIndex + '"]');
                                 var $li = $('#sortable li[data-key="' + fromIndex + '"]');
                                 if (fromIndex < toIndex) {
                                     //从上往下的情况
                                     //序号更新
                                     for (var i = (fromIndex + 1); i <= toIndex; i++) {
                                         var $ospan = $('#sortable span[data-key="' + i + '"]').html('【' + (i - 1) + '】');
                                         $ospan.attr('data-key', i - 1);

                                         var $oli  = $('#sortable li[data-key="' + i + '"]');
                                         $oli.attr('data-key', i - 1);
                                     }
                                     //权重更新
                                     $("#sortable li").each(function() {
                                         var weight = Number($(this).attr('data-weight'));
                                         if (weight >= to) {
                                             $(this).attr('data-weight', (weight + 1));
                                         }
                                     });

                                 } else {
                                     //从下往上的情况
                                     //序号更新
                                     for (var i = (fromIndex - 1); i >= toIndex; i--) {
                                         var $ospan = $('#sortable span[data-key="' + i + '"]').html('【' + (i + 1) + '】');
                                         $ospan.attr('data-key', i + 1);

                                         var $oli = $('#sortable li[data-key="' + i + '"]');
                                         $oli.attr('data-key', i + 1);
                                     }
                                     //权重更新
                                     $("#sortable li").each(function() {
                                         var weight = Number($(this).attr('data-weight'));
                                         $(this).attr('data-weight', (weight + 1));
                                     });
                                 }
                                 //最后处理移动的
                                 $span.html('【' + toIndex + '】');
                                 $span.attr('data-key', toIndex);
                                 $li.attr('data-key', toIndex);
                                 $li.attr('data-weight', to);


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
                     var publish = new Remoter('/admin/foodapi/changeStatus');
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
                     var upload = new Remoter('/admin/foodapi/save');
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
             
           //景点输入框自动完成
             $('#form-city').typeahead({
                 display: 'name',
                 val: 'id',
                 ajax: {
                     url: '/admin/cityapi/getCityList',
                     triggerLength: 1
                 },
                 itemSelected: function(item, val, text) {
                     $("#form-city").val(text);
                     $("#form-city").attr('data-city_id', val);
                     //触发dt的重新加载数据的方法
                     api.ajax.reload();
                 }
             });

             //景点框后的清除按钮，清除所选的景点
             $('#clear-city').click(function(event) {
                 $("#form-city").val('');
                 $("#form-city").attr('data-city_id', '');
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
