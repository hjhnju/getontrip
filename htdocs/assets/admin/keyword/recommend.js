/*

 景观推荐
  author:fyy
 */
$(document).ready(function() {
    var List = function() {

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
                    "url": "/admin/Keywordapi/recommend",
                    "type": "POST",
                    "data": function(d) {
                        //添加额外的参数传给服务器
                        // d.params.sight_id = '';
                        d.params = {};
                        if ($("#form-status").val()) {
                            d.params.status = $.trim($("#form-status").val());
                        }
                        if ($("#form-city").val()) {
                            d.params.city = $.trim($("#form-city").val());
                        }
                    }
                },
                "columnDefs": [{
                    "targets": [0],
                    "width": 20
                }, {
                    "targets": [2],
                    "width": 20
                }, {
                    "targets": [1],
                    "width": 80
                }],
                "columns": [{
                        "data": function(e){
                        	return '<span class="maintd_'+e.id+'" data-rowspan="'+e.sights.length+'">'+e.id+'</span>';
                        }
                    }, {
                        "data": function(e){
                        	return '<span class="maintd_'+e.id+'" data-rowspan="'+e.sights.length+'">'+e.name+'</span>';
                        }
                    },  {
                        "data": function(e){
                        	return '<span class="maintd_'+e.id+'" data-rowspan="'+e.sights.length+'">'+e.city+'</span>';
                        }
                    },{
                        "data":  function(e) { 
                            $parent = $('.maintd_'+e.id).parent().parent();  
                            var className = 'danger';
                            //for (var i = 1; i < e.sights.length; i++) {
                            for (var i = e.sights.length-1; i >=1; i--) {
                                $tr = $('<tr id="group_'+e.id+'_'+i+'" role="row"></tr>'); 
                                $tr.addClass($parent.attr('class')); 
                                $tr.addClass('group');
                                $tr.append('<td>'+'<span class="label label-'+className+'" id="'+e.sights[i].id+'" name="'+e.sights[i].name+'">'+e.sights[i].name+'</span>'+'</td>'); 
                                $parent.after($tr); 
                            } 
                            if (e.sights==0) {
                            	return '<span class="label label-'+className+'"  id="" name=""></span>';
                            } 
                            return '<span class="label label-'+className+'"  id="'+e.sights[0].id+'" name="'+e.sights[0].name+'">'+e.sights[0].name+'</span>';
                         }
                    }, {
                        "data": function(e){ 
                            for (var i = 1; i < e.sights.length; i++) {
                                $tr =$('#group_'+e.id+'_'+i);
                                $tr.append('<td>'+e.sights[i].city+'</td>'); 
                            };
                            if (e.sights==0) {
                            	return '<span class=""></span>';
                            } 
                            return '<span class="">'+e.sights[0].city+'</span>'
                        }
                    },{
                        "data": function(e){ 
                            for (var i = 1; i < e.sights.length; i++) {
                                $tr =$('#group_'+e.id+'_'+i);
                                if(e.sights[i].status == 0){
                                	$tr.append('<td>未处理</td>'); 
                                }else if(e.sights[i].status == 1){
                                	$tr.append('<td>已接受</td>'); 
                                }else{
                                	$tr.append('<td>已拒绝</td>'); 
                                }
                            };
                            if (e.sights==0) {
                            	return '<span class="">未处理</span>'
                            } 
                            if(e.sights[0].status == 0){
                            	return '<span class="">未处理</span>'
                            }else if(e.sights[0].status == 1){
                            	return '<span class="">已接受</span>'
                            }else{
                            	return '<span class="">已拒绝</span>'
                            }
                        }
                    },{
                        "data": function(e) {  
                            for (var i = 1; i < e.sights.length; i++) {
                                $tr =$('#group_'+e.id+'_'+i);
                                var str = '<button class="btn-accept" data-id="'+e.id+'"  data-sight_id="'+e.sights[i].id+'">接受</button>&nbsp;'
                                   +'<button class="btn-reject" data-id="'+e.id+'"   data-sight_id="'+e.sights[i].id+'">拒绝</button>&nbsp;';
                                $tr.append('<td>'+str+'</td>');  
                            }; 
                            if (e.sights==0) {
                                return '';
                            } 
                            return '<button class="btn-accept" data-id="'+e.id+'"  data-sight_id="'+e.sights[0].id+'">接受</button>&nbsp;'
                                   +'<button class="btn-reject" data-id="'+e.id+'"  data-sight_id="'+e.sights[0].id+'">拒绝</button>&nbsp;';

                            //return '<a class="btn btn-primary btn-xs edit" title="查看" data-toggle="tooltip" href="/admin/sight/edit?action=edit&id=' + e.id + '"><i class="fa fa-eye"></i></a>' ;
                        }
                    }, 
                ],
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

                //绑定draw事件
                $('#editable').on('draw.dt', function() {
                    //工具提示框
                    $('[data-toggle="tooltip"]').tooltip();
                });

                //状态下拉列表 
                $('#form-status').selectpicker();

                //接受
                $('.btn-accept').live('click', function(e) { 
                    //提取当前文章的所有状态
                    var id = $(this).attr('data-id');
                    var sightid = $(this).attr('data-sight_id');
                    $.ajax({
                        "url": "/admin/keywordapi/dealRecommend",
                        "data": {id:id,sightId:sightid,status:1},
                        "type": "post",
                        "error": function(e) {
                            alert("服务器未正常响应，请重试");
                        },
                        "success": function(response) {
                            if (response.status == 0) {
                                toastr.success('操作成功！'); 
                                //刷新当前页
                                oTable.fnRefresh();
                            }
                            else{
                                toastr.warning(response.statusInfo); 
                            }
                        }
                    });
                });
                 
                //拒绝
                $('.btn-reject').live('click', function(e) { 
                    //提取当前文章的所有状态
                    var id = $(this).attr('data-id');
                    var sightid = $(this).attr('data-sight_id');
                    $.ajax({
                        "url": "/admin/keywordapi/dealRecommend",
                        "data": {id:id,sightId:sightid,status:2},
                        "type": "post",
                        "error": function(e) {
                            alert("服务器未正常响应，请重试");
                        },
                        "success": function(response) {
                            if (response.status == 0) {
                                toastr.success('操作成功！'); 
                                //刷新当前页
                                oTable.fnRefresh();
                            }
                            else{
                                toastr.warning(response.statusInfo); 
                            }
                        }
                    });
                });
            } 
        }

        /*
          过滤事件
         */
        var filter = function() {

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
                    $("#form-city").attr('data-sight_id', val);
                    //触发dt的重新加载数据的方法
                    api.ajax.reload();
                }
            });

            //景点框后的清除按钮，清除所选的景点
            $('#clear-city').click(function(event) {
                $("#form-city").val('');
                $("#form-city").attr('data-sight_id', '');
                //触发dt的重新加载数据的方法
                api.ajax.reload();
            });

            $('#form-status').change(function(event) {
                //触发dt的重新加载数据的方法
                api.ajax.reload();
            });

            //只看我自己发布的
            $('#form-user_id').click(function(event) {
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
