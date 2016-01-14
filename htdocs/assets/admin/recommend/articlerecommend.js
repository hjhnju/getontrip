/*
   景点列表
   author:fyy
 */
$(document).ready(function() {
    var List = function() {
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
                    "url": "/admin/recommendapi/articlerecommendlist",
                    "type": "POST",
                    "data": function(d) {
                        //添加额外的参数传给服务器
                        d.params = {}; 
                        if ($("#form-title").val()) {
                            d.params.title = $.trim($("#form-title").val());
                        }
                        if ($("#form-sight").val()) {
                            d.params.sight = $.trim($("#form-sight").attr('data-sight_id'));
                        } 
                        if ($("#form-tag").val()) {
                            d.params.tag = $.trim($("#form-tag").attr('data-tag_id'));
                        }
                        if ($("#form-status").val()) {
                            d.params.status = $.trim($("#form-status").val());
                        }
                    }
                },
                "columnDefs": [ {
                    "targets": [0],
                    "width": 30
                }, {
                    "targets": [1],
                    "width": 450
                }, {
                    "targets": [2],
                    "width": 100
                }   ],
                "columns": [{
                    "data": function(e) {
                        return '<span class="maintd_'+e.obj_id+'" data-rowspan="'+e.group.length+'">'+e.obj_id+'</span>';
                    }
                } , {
                    "data": function(e) {
                        //return '<a class="btn btn-primary btn-xs edit" title="查看" data-toggle="tooltip" href="/admin/sight/edit?action=edit&id=' + e.id + '">'+e.groupname+'</a>';
                   
                        var str = '<a class="maintd_'+e.obj_id+'" data-rowspan="'+e.group.length+'" href="/admin/recommend/articledetail?id='+e.obj_id+'" target="_blank">'+e.title+'</a>';
                        str = str + '&nbsp;['+e.source+','+e.issue+']<br><span id="content_'+e.obj_id+'">'+e.subcontent+'</span>&nbsp;&nbsp;<a href="/admin/recommend/articledetail?id='+e.obj_id+'" target="_blank">查看更多</a>';
                        return str;
                    }
                }, {
                    "data": function(e) { 
                        return '<span class="maintd_'+e.obj_id+'" data-rowspan="'+e.group.length+'">'+e.subtitle+'</span>';
                    }
                } , {
                    "data": function(e) { 
                        return '<span class="maintd_'+e.tagid+'" data-rowspan="'+e.group.length+'">'+e.tagname+'</span>';
                    }
                } ,{
                    "data":  function(e) { 
                        $parent = $('.maintd_'+e.obj_id).parent().parent(); 
                        var className = 'danger';
                        //for (var i = 1; i  e.group.length; i++) {
                        for (var i = e.group.length-1; i >=1; i--) {
                            $tr = $('<tr id="group_'+e.obj_id+'_'+i+'" role="row"></tr>'); 
                            $tr.addClass($parent.attr('class')); 
                            $tr.addClass('group');
                            className = e.group[i].label_type==1?'danger':'warning'; 
                            $tr.append('<td>'+'<span class="label label-'+className+'" type="'+e.group[i].label_type+'" id="'+e.group[i].label_id+'" name="'+e.group[i].name+'">'+e.group[i].name+'</span>'+'</td>'); 
                            $parent.after($tr); 
                        } 
                        if (e.group==0) {
                            e.group[0] = e;
                        } 
                        className = e.group[0].label_type==1?'danger':'warning'; 
                        return '<span class="label label-'+className+'" type="'+e.group[0].label_type+'" id="'+e.group[0].label_id+'" name="'+e.group[0].name+'">'+e.group[0].name+'</span>';
                     }
                }, {
                    "data": function(e){ 
                        for (var i = 1; i < e.group.length; i++) {
                            $tr =$('#group_'+e.obj_id+'_'+i);
                            $tr.append('<td>'+e.group[i].rate+'</td>'); 
                        };
                        if (e.group==0) {
                            e.group[0] = e;
                        } 
                        return '<span class="">'+e.group[0].rate+'</span>'
                    }
                }, {
                    "data": function(e){ 
                        for (var i = 1; i < e.group.length; i++) {
                            $tr =$('#group_'+e.obj_id+'_'+i);
                            $tr.append('<td>'+e.group[i].status_name+'</td>'); 
                        };
                        if (e.group==0) {
                            e.group[0] = e;
                        } 
                        return '<span class="">'+e.group[0].status_name+'</span>'
                    }
                }, {
                    "data": function(e) {  
                        for (var i = 1; i < e.group.length; i++) {
                            $tr =$('#group_'+e.obj_id+'_'+i);
                            var str = '<span class="btn btn-default btn-xs opt-status" data-action="ACCEPT"  data-obj_id="'+e.obj_id+'"  data-label_id="'+e.group[i].label_id+'" data-label_type="'+e.group[i].label_type+'" >接受</span>'
                               +'<span class="btn btn-default btn-xs opt-status" data-action="REJECT"  data-obj_id="'+e.obj_id+'"   data-label_id="'+e.group[i].label_id+'" data-label_type="'+e.group[i].label_type+'" >拒绝</span>'
                               +'<span class="btn btn-default btn-xs opt-status" data-action="NOT_DEAL"  data-obj_id="'+e.obj_id+'"   data-label_id="'+e.group[i].label_id+'" data-label_type="'+e.group[i].label_type+'">待处理</span>'  ;

                            $tr.append('<td>'+str+'</td>');  
                        }; 
                        if (e.group==0) {
                            e.group[0] = e;
                        } 
                        return '<span class="btn btn-default btn-xs opt-status" data-action="ACCEPT" data-obj_id="'+e.obj_id+'"  data-label_id="'+e.group[0].label_id+'" data-label_type="'+e.group[0].label_type+'" >接受</span>'
                               +'<span class="btn btn-default btn-xs opt-status" data-action="REJECT" data-obj_id="'+e.obj_id+'"  data-label_id="'+e.group[0].label_id+'" data-label_type="'+e.group[0].label_type+'" >拒绝</span>'
                               +'<span class="btn btn-default btn-xs opt-status" data-action="NOT_DEAL" data-obj_id="'+e.obj_id+'" data-label_id="'+e.group[0].label_id+'" data-label_type="'+e.group[0].label_type+'" >待处理</span>'  ;

                        //return '<a class="btn btn-primary btn-xs edit" title="查看" data-toggle="tooltip" href="/admin/sight/edit?action=edit&id=' + e.id + '"><i class="fa fa-eye"></i></a>' ;
                    }
                }, {
                    "data": function(e) {
                        return '<button class="maintd_'+e.obj_id+' btn btn-default btn-xs opt" data-rowspan="'+e.group.length+'"  data-id="'+e.obj_id+'">提交</button>';
                    }
                } ],
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

                //选择状态 radio效果
                $('#editable span.opt-status').live('click', function(e) {
                    var all = $(this).parent().find('span.opt-status');
                    all.removeClass('checked btn-danger');
                    $(this).addClass('checked  btn-danger');
                });   
                
              //景点框后的清除按钮，清除所选的景点
                $('.label').live('click', function(e) {
                	var id   = $(this).attr('id');
                	var name = $(this).attr('name');
                	var type = $(this).attr('type');
                	if(type == 1){
                		$("#form-sight").val(name);
                    	$("#form-sight").attr('data-sight_id', id);
                	}else{
                		 $("#form-tag").val(name);
                         $("#form-tag").attr('data-tag_id', id);
                	}
                     //触发dt的重新加载数据的方法
                    api.ajax.reload();
                });

                //保存
                $('#editable button.opt').live('click', function(e) { 
                    e.preventDefault();
                    if (confirm("确定提交么 ?") == false) {
                        return;
                    } 
                    //提取当前文章的所有状态
                    var obj_id = $(this).attr('data-id');
                    var items = [];
                    var test  = false;
                    $.each($('#editable span.opt-status.checked[data-obj_id="'+obj_id+'"]'),function(i,ele){
                        var data = {
                            action : $(ele).attr('data-action'), 
                            label_id : $(ele).attr('data-label_id'),
                            label_type : $(ele).attr('data-label_type'),
                        };
                       if($(ele).attr('data-action') == 'ACCEPT'){
                    	   test = true;
                       }
                       items.push(data);
                    }) 
                    if (items.length==0) {
                         toastr.warning('请选择一个状态'); 
                         return;
                    }             
                    $.ajax({
                        "url": "/admin/recommendapi/dealArticle",
                        "data": {id:obj_id,params:items},
                        "type": "post",
                        "error": function(e) {
                            alert("服务器未正常响应，请重试");
                        },
                        "success": function(response) {
                            if (response.status == 0) {
                                toastr.success('操作成功！'); 
                                //刷新当前页
                                oTable.fnRefresh();
                                //打开新的链接
                                if(test == true){
                                	window.open("/admin/topic/edit?action=edit&id="+response.data);
                                }
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


            //标签输入框自动完成
            $('#form-tag').typeahead({
                display: 'name',
                val: 'id',
                ajax: {
                    url: '/admin/tagapi/getTagGeneralList',
                    triggerLength: 1
                },
                itemSelected: function(item, val, text) {
                    $("#form-tag").val(text);
                    $("#form-tag").attr('data-tag_id', val);
                    //触发dt的重新加载数据的方法
                    api.ajax.reload();
                }
            });

            //标签框后的清除按钮，清除所选的景点
            $('#clear-tag').click(function(event) {
                $("#form-tag").val('');
                $("#form-tag").attr('data-tag_id', '');
                //触发dt的重新加载数据的方法
                api.ajax.reload();
            });


            //只看我自己发布的
            $('#form-user_id').click(function(event) {
                api.ajax.reload();
            });

            $('#form-status').change(function(event) {
                //触发dt的重新加载数据的方法
                api.ajax.reload();
            });

              //输入内容点击回车查询
            $("#form-title").keydown(function(event) {
                if (event.keyCode == 13) {
                    api.ajax.reload();
                }
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