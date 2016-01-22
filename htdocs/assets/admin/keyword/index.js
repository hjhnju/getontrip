/*

 景点词条列表
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
                        if ($("#form-level").val()) {
                            d.params.level = $.trim($("#form-level").val());
                        }
                    }
                },
                "columnDefs": [{
                    "targets": [0],
                    "width": 20
                }, {
                    "targets": [2, 3, 4,5, 6, 7,8],
                    "width": 20
                }, {
                    "targets": [1],
                    "width": 80
                }],
                "columns": [{
                        "data": "id"
                    }, {
                        "data": 'name'
                    }, {
                    "data": function(e) {
                        if (e.image) {
                            return '<a href="/pic/' + e.image + '" target="_blank"><img alt="" src="/pic/' + e.image.getNewImgByImg(80, 22, 'f') + '"/></a>';
                        }
                        return "未上传";
                    }
                }, {
                        "data": function(e) {
                            if (e.url) {
                                return '<a href="' + e.url + '" target="_blank" title="' + e.url + '">' + (e.url.length > 20 ? e.url.substr(0, 20) + '...' : e.url) + '</a>';
                            }
                            return '暂无';
                        }
                    }, {"data": function(e) {
                    	if (e.level == 1) {
                    		return '城市级';
                    	}else if (e.level == 2) {
                    		return '景点级';
                    	}else if (e.level == 3){
                    		return '景观级';
                    	}else{
                    		return '二级景观';
                    	}}
                    },{
                        "data": "sight_name"
                    }, {"data": function(e) {
                    	if (e.audio) {
                    		return '<audio src="http://' + e.audio+ '"  controls="controls"></audio>';
                    		}
                    	return '暂无';
                    	}
                    },{
                        "data": 'x'
                    }, {
                        "data": 'y'
                    }
                    /*, {
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
                            }*/
                    , {
                        'data': function(e) {
                            if (e.weight) {
                                return e.weight + '  <button class="btn btn-primary  btn-xs weight" title="修改排序" data-toggle="tooltip"><i class="fa fa-reorder"></i></button>'
                            }
                        }
                    }, {
                        'data': function(e) {
                            if (e.type == 1) {
                                return '是';
                            }
                            return '否';
                        }
                    },{
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
                        	if(e.type == 1){
                        		return '<button class="btn btn-warning  btn-xs confirm" title="确认url" data-toggle="tooltip"><i class="fa fa-check-square-o"></i></button><a class="btn btn-success btn-xs edit" title="查看" data-toggle="tooltip" href="/admin/keyword/edit?action=view&id=' + e.id + '"><i class="fa fa-eye"></i></a><a class="btn btn-primary btn-xs edit" title="编辑" data-toggle="tooltip" href="/admin/keyword/edit?action=edit&id=' + e.id + '"><i class="fa fa-pencil"></i></a>' + '<button type="button" class="btn btn-danger btn-xs delete"  title="删除" data-toggle="tooltip"><i class="fa fa-trash-o "></i></button>'+ '<button type="button" class="btn btn-danger btn-xs alias"  title="设为别名" data-toggle="tooltip" value="'+ e.id +'"><i class="fa fa-eyedropper"></i></button>'+ '<button type="button" class="btn btn-warning btn-xs type"  title="取消必玩" data-toggle="tooltip" value="'+ e.id +'"><i class="fa fa-adjust"></i></button>';
                        	}
                            return '<button class="btn btn-warning  btn-xs confirm" title="确认url" data-toggle="tooltip"><i class="fa fa-check-square-o"></i></button><a class="btn btn-success btn-xs edit" title="查看" data-toggle="tooltip" href="/admin/keyword/edit?action=view&id=' + e.id + '"><i class="fa fa-eye"></i></a><a class="btn btn-primary btn-xs edit" title="编辑" data-toggle="tooltip" href="/admin/keyword/edit?action=edit&id=' + e.id + '"><i class="fa fa-pencil"></i></a>' + '<button type="button" class="btn btn-danger btn-xs delete"  title="删除" data-toggle="tooltip"><i class="fa fa-trash-o "></i></button>'+ '<button type="button" class="btn btn-danger btn-xs alias"  title="设为别名" data-toggle="tooltip" value="'+ e.id +'"><i class="fa fa-eyedropper"></i></button>'+ '<button type="button" class="btn btn-warning btn-xs type"  title="设为必玩" data-toggle="tooltip" value="'+ e.id +'"><i class="fa fa-adjust"></i></button>';
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

                //绑定draw事件
                $('#editable').on('draw.dt', function() {
                    //工具提示框
                    $('[data-toggle="tooltip"]').tooltip();
                });

                //状态下拉列表 
                $('#form-status').selectpicker();
                $('#form-level').selectpicker();


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
                
              //添加别名操作
                $('#editable button.alias').live('click', function(e) {
                	var toid=prompt("请输入景观ID","");                 
                    if(toid){
                    	var fromid = $(this).val();
                    	$.ajax({
                            "url": "/admin/Keywordapi/addalias",
                            "data": {
                                from: fromid,
                                to: toid
                            },
                            "type": "post",
                            "error": function(e) {
                                alert("服务器未正常响应，请重试");
                            },
                            "success": function(response) {
                                api.ajax.reload();
                            }
                        });
                     }
                });
                
              //设为必玩
                $('#editable button.type').live('click', function(e) { 
                	var nRow = $(this).parents('tr')[0];
                    var data = oTable.api().row(nRow).data();
                    var type = 1;
                    if(data.type == 1){
                    	type = 0;
                    }
                	$.ajax({
                        "url": "/admin/Keywordapi/save",
                        "data": {
                            id: data.id,
                            type:type
                        },
                        "type": "post",
                        "error": function(e) {
                            alert("服务器未正常响应，请重试");
                        },
                        "success": function(response) {
                            api.ajax.reload();
                        }
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
                        'sight_id': data.sight_id
                    };
                    //查询当前景点下的所有词条
                    $.ajax({
                        "url": "/admin/Keywordapi/list",
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
                            var index = '';
                            totalNum = data.length;
                            $.each(data, function(key, value) {
                                li = li + '<li class="list-primary" data-id="' + value.id + '" data-weight="' + value.weight + '" data-key="' + (key + 1) + '"><div class="task-title"><span class="key" data-key="' + (key + 1) + '">【' + (key + 1) + '】</span><span class="task-title-sp">' + value.name + '</span><span class="badge badge-sm label-info">' + sight_name + '</span></div></li>'
                                index = index + '<li>' + (key + 1) + '</li>';
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
                                    changeWeight($(li.item).attr('data-id'), oldNum, newNum, sight_id, oldIndex, newIndex);
                                }
                            });
                            //弹出模态框
                            $('#myModal').modal();
                        }
                    });

                    function changeWeight(id, from, to, sight_id, fromIndex, toIndex) {
                        $.ajax({
                            "url": "/admin/Keywordapi/changeWeight",
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
                                //序号更新, 权重更新
                                var $span = $('#sortable span[data-key="' + fromIndex + '"]');
                                var $li = $('#sortable li[data-key="' + fromIndex + '"]');
                                if (fromIndex < toIndex) {
                                    //从上往下的情况
                                    //序号更新
                                    for (var i = (fromIndex + 1); i <= toIndex; i++) {
                                        var $ospan = $('#sortable span[data-key="' + i + '"]').html('【' + (i - 1) + '】');
                                        $ospan.attr('data-key', i - 1);

                                        var $oli = $('#sortable li[data-key="' + i + '"]');
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

            $('#form-status').change(function(event) {
                //触发dt的重新加载数据的方法
                api.ajax.reload();
            });
            $('#form-level').change(function(event) {
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
