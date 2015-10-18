/*

 景点词条列表
  author:fyy
 */
$(document).ready(function() {
    var FORMATER = 'YYYY-MM-DD HH:mm:ss';
    var oTable = $('#editable').dataTable({
        "serverSide": true, //分页，取数据等等的都放到服务端去
        "processing": true, //载入数据的时候是否显示“载入中”
        "pageLength": 10, //首次加载的数据条数  
        "searching": false, //是否开启本地分页
        "ordering": false,
        "ajax": {
            "url": "/admin/Keywordapi/list",
            "type": "POST",
            "data": function(d) {
                //添加额外的参数传给服务器
                // d.params.sight_id = '';
                d.params = {};
                if ($("#form-sight").attr('data-sight_id')) {
                    d.params.sight_id = Number($.trim($("#form-sight").attr('data-sight_id')));
                }
                if ($('#form-user_id').attr("checked")) {
                    d.params.create_user = $('#form-user_id').val();
                }
                if ($("#form-status").val()) {
                    d.params.status = $.trim($("#form-status").val());
                }
            }
        },
        "columnDefs": [{
            "targets": [0],
            "visible": true,
            "searchable": false
        }, {
            "targets": [1,2,4,5,6,7], 
            "width": 20
        }, {
            "targets": [2,3], 
            "width": 70
        }],
        "columns": [{
            "data": "id"
        }, {
            "data": 'name'
        }, {
            "data": function(e) {
                if (e.url) {
                    return '<a href="' + e.url + '" target="_blank" title="' + e.url + '">' + (e.url.length > 20 ? e.url.substr(0, 20) + '...' : e.url) + '</a>';
                }
                return '暂无';
            }
        }, {
            "data": "sight_name"
        }, {
            "data": 'x'
        }, {
            "data": 'y'
        }/*, {
            "data": function(e) {
                if (e.create_time) {
                    return moment.unix(e.create_time).format(FORMATER);
                }
                return "空";
            }
        }, {
            "data": function(e) {
                if (e.update_time) {
                    return moment.unix(e.update_time).format(FORMATER);
                }
                return "空";
            }
        }*/, {
            'data': function(e) {
                if (e.weight) {
                    return e.weight + '  <button class="btn btn-primary  btn-xs weight" title="修改排序" data-toggle="tooltip"><i class="fa fa-reorder"></i></button>'
                }
            }
        }, {
            "data": function(e) {
                if (e.status == 2) {
                    return '<span class="span-status" data-id="' + e.id + '"><i class="fa fa-2x fa-check color-check"></i></span>'
                } else if (e.status == 1) {
                    return '<span class="span-status" data-id="' + e.id + '"><i class="fa fa-2x fa-close color-uncheck"></i></span>'
                } else {
                    return '<span class="span-status" data-id="' + e.id + '">未知状态</span>';
                }

            }
        }, {
            "data": function(e) {
                return '<button class="btn btn-warning  btn-xs confirm" title="确认url" data-toggle="tooltip"><i class="fa fa-check-square-o"></i></button><a class="btn btn-success btn-xs edit" title="查看" data-toggle="tooltip" href="/admin/keyword/edit?action=view&id=' + e.id + '"><i class="fa fa-eye"></i></a><a class="btn btn-primary btn-xs edit" title="编辑" data-toggle="tooltip" href="/admin/keyword/edit?action=edit&id=' + e.id + '"><i class="fa fa-pencil"></i></a>' + '<button type="button" class="btn btn-danger btn-xs delete"  title="删除" data-toggle="tooltip"><i class="fa fa-trash-o "></i></button>';
            }
        }],
        "initComplete": function(setting, json) {
            //工具提示框
            //$('[data-toggle="tooltip"]').tooltip();
        }
    });

    var api = oTable.api();
    filters();
    bindEvents();


    /*
      绑定事件
     */
    function bindEvents() {

        //绑定draw事件
        $('#editable').on('draw.dt', function() {
            //工具提示框
            $('[data-toggle="tooltip"]').tooltip();
        });

        //状态下拉列表 
        $('#form-status').selectpicker();

        //删除操作
        $('#editable button.delete').live('click', function(e) {
            e.preventDefault();
            if (confirm("确定删除么 ?") == false) {
                return;
            }
            var nRow = $(this).parents('tr')[0];
            var data = oTable.api().row(nRow).data();
            $.ajax({
                "url": "/admin/Keywordapi/del",
                "data": data,
                "type": "post",
                "error": function(e) {
                    alert("服务器未正常响应，请重试");
                },
                "success": function(response) {
                    if (response.status == 0) {
                        oTable.fnDeleteRow(nRow);
                    }
                }
            });
        });

        //确认url操作
        $('#editable button.confirm').live('click', function(e) {
            e.preventDefault();
            var nRow = $(this).parents('tr')[0];
            var data = oTable.api().row(nRow).data(); 
            var params = {
                id: data.id,
                status: 2 //确认的状态
            }
            $.ajax({
                "url": "/admin/Keywordapi/save",
                "data": params,
                "type": "post",
                "error": function(e) {
                    alert("服务器未正常响应，请重试");
                },
                "success": function(response) {
                    if (response.status == 0) {
                        //刷新当前页
                        oTable.fnRefresh();
                    }
                }
            });
        });

        //修改权重操作 
        $('#editable button.weight').live('click', function(e) {
            e.preventDefault();
            var nRow = $(this).parents('tr')[0];
            var data = oTable.api().row(nRow).data(); 
            sight_name = data.sight_name;
             var params = {
                'sight_id': data.sight_id
            }; 
            //查询当前景点下的所有词条
            $.ajax({
                "url": "/admin/Keywordapi/list",
                "data": {
                    params:params
                },
                "type": "post",
                "error": function(e) {
                    alert("服务器未正常响应，请重试");
                },
                "success": function(response) {
                    var data = response.data.data;
                    var li = '';
                    $.each(data, function(key, value) {
                        li = li + '<li class="list-primary" data-id="' + value.id + '"><div class="task-title"><span class="task-title-sp">' + value.name + '</span><span class="badge badge-sm label-info">' + sight_name + '</span></div></li>'
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
                            changeWeight($(li.item).attr('data-id'),newNum);
                        }
                    });
                    //弹出模态框
                    $('#myModal').modal();
                }
            });

            function changeWeight(id,to) {
                $.ajax({
                    "url": "/admin/Keywordapi/changeWeight",
                    "data": {
                        id:id,
                        to:to
                    },
                    "type": "post",
                    "error": function(e) {
                        alert("服务器未正常响应，请重试");
                    },
                    "success": function(response) { 
                        //关闭模态框
                        $('#myModal').modal('hide');
                        api.ajax.reload();
                    }
                });
            }

        });


    }

    /*
      过滤事件
     */
    function filters() {

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

        $('#form-status').change(function(event) {
            //触发dt的重新加载数据的方法
            api.ajax.reload();
        });

        //只看我自己发布的
        $('#form-user_id').click(function(event) {
            api.ajax.reload();
        });



    }



});
