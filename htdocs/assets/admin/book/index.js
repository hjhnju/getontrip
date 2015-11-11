/*

 京东书籍列表
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
                    "url": "/admin/bookapi/list",
                    "type": "POST",
                    "data": function(d) {
                        //添加额外的参数传给服务器
                        d.params = {};
                        if ($("#form-sight").attr('data-sight_id')) {
                            d.params.sight_id = $.trim($("#form-sight").attr('data-sight_id'));
                        }
                        if ($("#form-isbn").val()) {
                            d.params.isbn = $.trim($("#form-isbn").val());
                        }
                        if ($('#form-title').val()) {
                            d.params.title = $('#form-title').val();
                        }
                        if ($("#form-status").val()) {
                            d.params.status = $.trim($("#form-status").val());
                        }
                    }
                },
                "columnDefs": [{
                    "targets": [0],
                    "visible": true,
                    "searchable": false,
                    "width": 30
                }, {
                    "targets": [1],
                    "orderable": false,
                    "width": 150
                }, {
                    "targets": [2, 3, 4, 7],
                    "orderable": false,
                    "width": 80
                }, {
                    "targets": [5, 6],
                    "orderable": false,
                    "width": 40
                }, {
                    "targets": [8],
                    "orderable": false,
                    "width": 110
                }],
                "columns": [{
                    "data": "id"
                }, {
                    "data": 'title'
                }, {
                    "data": 'author'
                }, {
                    "data": 'press'
                }, {
                    "data": 'isbn'
                }, {
                    "data": 'price_mart'
                }, {
                    "data": 'price_jd'
                }, {
                    "data": function(e) {
                        if (e.url) {
                            return '<a href="' + e.url + '" target="_blank" title="' + e.url + '">' + (e.url.length > 20 ? e.url.substr(0, 20) + '...' : e.url) + '</a>';
                        }
                        return '暂无';
                    }
                }, {
                    "data": function(e) {
                        var str = '';
                        if (e.sights.length) {
                            var sight = e.sights;
                            for (var i = 0; i < sight.length; i++) {
                                str = str + '  ' + sight[i].name + '[' + sight[i].weight + ']';
                            };
                            return str + '  <button class="btn btn-primary  btn-xs weight" title="修改排序" data-toggle="tooltip"><i class="fa fa-reorder"></i></button>';
                        }
                        return '暂无';
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
                        // return '';
                        return '<a class="btn btn-primary btn-xs edit" title="编辑" data-toggle="tooltip" href="/admin/book/edit?action=edit&id=' + e.id + '"><i class="fa fa-pencil"></i></a>';

                        return '<a class="btn btn-primary btn-xs edit" title="编辑" data-toggle="tooltip" href="/admin/book/edit?action=edit&id=' + e.id + '"><i class="fa fa-pencil"></i></a>' + '<button type="button" class="btn btn-danger btn-xs delete"  title="删除" data-toggle="tooltip"><i class="fa fa-trash-o "></i></button>';
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
                this.init_action();
                this.init_book();
            },
            init_book: function() {
                //书籍添加器类型下拉列表 
                $('#form-type').selectpicker();

                //点击添加弹出书籍添加器
                $('#addBook').click(function(event) {
                    $("#Form")[0].reset();
                    $('#myModal').modal();
                });

                $.validator.setDefaults({
                    submitHandler: function(data) {
                        //序列化表单  
                        var param = $("#Form").serializeObject();
                        //按钮disabled
                        $('#Form button[type="submit"]').btnDisable({
                            text: '正在生成书籍，请稍后'
                        });

                        $.ajax({
                            "url": "/admin/bookapi/add",
                            "data": param,
                            "type": "post",
                            "dataType": "json",
                            "error": function(e) {
                                alert("服务器未正常响应，请重试");
                                $('#Form button[type="submit"]').btnEnable();
                            },
                            "success": function(response) {
                                if (response.status == 0) {
                                    toastr.success('保存成功');

                                    $('#openWin').attr('href', '/admin/book/edit?action=edit&id=' + response.data);
                                    $('#openWin')[0].click();

                                    //刷新页面

                                    //手工关闭模态框
                                    $('#myModal').modal('hide');
                                } else {
                                    alert(response.statusInfo);
                                }
                                $('#Form button[type="submit"]').btnEnable();
                            }
                        });
                    }
                });

                validations();

                /*
                   添加书籍表单验证
                */
                function validations() {
                    // validate signup form on keyup and submit
                    validate = $("#Form").validate({
                        rules: {
                            strIsbn: "required"
                        },
                        messages: {
                            strIsbn: "skuid或isbn不能为空！"
                        }
                    });
                }

            },
            init_action: function() {

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
                        "url": "/admin/bookapi/del",
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
                            toastr.warning('发布之前必须上传背景图片！');
                            return;
                        }
                        if (!data.sights.length||!data.title||!data.author||!data.press||!data.url||!data.publish_time||!data.content_desc) {
                            toastr.warning('发布之前请将信息补全！');
                            return;
                        }
                        action = 'PUBLISHED';
                    } else {
                        action = 'NOTPUBLISHED';
                    }
                    var publish = new Remoter('/admin/bookapi/save');
                    publish.remote({
                        id: data.id,
                        sightId: data.sight_id,
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
                    if (confirm("确定加入黑名单么 ?加入黑名单后，将不再抓取该书籍，且该书籍与景点的关系将解除。") == false) {
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
                    var publish = new Remoter('/admin/bookapi/save');
                    publish.remote({
                        id: data.id,
                        sightId: data.sight_id,
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
                    /*var nRow = $(this).parents('tr')[0];
                    var data = oTable.api().row(nRow).data();*/
                    if (!$('#form-sight').attr('data-sight_id')) {
                        toastr.warning('请先选择一个景点！');
                        $('#form-sight').focus();
                        return false;
                    }
                    sight_name = $('#form-sight').val();
                    sight_id = $('#form-sight').attr('data-sight_id');
                    var params = {
                        sight_id: sight_id,
                        order: '`weight` asc',
                        action : 'PUBLISHED'
                    };
                    //查询当前景点下的所有词条
                    $.ajax({
                        "url": "/admin/bookapi/list",
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
                                li = li + '<li class="list-primary" data-id="' + value.id + '" data-weight="' + value.weight + '" data-key="' + (key + 1) + '"><div class="task-title"><span class="key" data-key="' + (key + 1) + '">【' + (key + 1) + '】</span><span class="task-title-sp">' + value.title + '</span><span class="badge badge-sm label-info">' + sight_name + '</span></div></li>'
                                index = index + '<li>' + (key + 1) + '</li>';
                            });

                            //$('#sortable_index').html(index);
                            $('#sortable').html(li);
                            $("#sortable").sortable({
                                //revert: true,
                                start: function(d, li) { 
                                    oldIndex = $(li.item).index()+1; 
                                    oldNum = Number($('#sortable li[data-key="' + oldIndex + '"]').attr('data-weight'));
                                },
                                stop: function(d, li) { 
                                     newIndex = $(li.item).index()+1; 
                                    newNum = Number($('#sortable li[data-key="' + newIndex + '"]').attr('data-weight'));
                                    if (oldNum === newNum) {
                                        return;
                                    }
                                    changeWeight($(li.item).attr('data-id'), newNum, sight_id,oldIndex,newIndex);
                                }
                            });
                            //弹出模态框
                            $('#orderModal').modal();
                        }
                    });

                    function changeWeight(id, to, sight_id,fromIndex,toIndex) {
                        $.ajax({
                            "url": "/admin/bookapi/changeWeight",
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
                                var $span = $('#sortable span[data-key="' + fromIndex + '"]');

                                if (fromIndex < toIndex) {
                                    for (var i = (fromIndex + 1); i <= toIndex; i++) {
                                        var $ospan = $('#sortable span[data-key="' + i + '"]').html('【' + (i - 1) + '】');
                                        $ospan.attr('data-key', i - 1);
                                    }
                                } else {
                                    for (var i = (fromIndex - 1); i >= toIndex; i--) {
                                        var $ospan = $('#sortable span[data-key="' + i + '"]').html('【' + (i + 1) + '】');
                                        $ospan.attr('data-key', i + 1);
                                    }
                                }
                                //最后处理移动的
                                $span.html('【' + toIndex + '】');
                                $span.attr('data-key', toIndex);

                                
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
                        if (confirm("确定加入黑名单么 ?加入黑名单后，将不再抓取该书籍，且该书籍与景点的关系将解除。") == false) {
                            return;
                        }
                    }
                    for (var i = 0; i < datas.length; i++) {
                        var data = datas[i];
                        if (action == 'PUBLISHED' && !data.image) {
                            toastr.warning('发布之前必须上传背景图片');
                            return;
                        }
                        idArray.push(data.id);
                    };
                    if (!idArray.length) {
                        toastr.warning('请选择一行！');
                        return false;
                    }

                    var publish = new Remoter('/admin/bookapi/changeStatus');
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


            }
        }

        /*
              过滤事件
         */
        var filter = function() {
            //输入内容点击回车查询
            $("#form-isbn,#form-sight,#form-title").keydown(function(event) {
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

            $('#form-status').change(function(event) {
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
