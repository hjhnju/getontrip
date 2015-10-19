$(document).ready(function() {
    var List = function() {
        var FORMATER = 'YYYY-MM-DD HH:mm:ss';
        var newBtn = '<button type="button" class="btn btn-success btn-xs save"  title="保存" data-toggle="tooltip"><i class="fa fa-save"></i></button>' + '<button type="button" class="btn btn-danger btn-xs cancel"  title="取消" data-toggle="tooltip" data-mode="new"><i class="fa fa-remove"></i></button>';
        var saveBtn = '<button type="button" class="btn btn-success btn-xs save"  title="保存" data-toggle="tooltip"><i class="fa fa-save"></i></button>' + '<button type="button" class="btn btn-danger btn-xs cancel"  title="取消" data-toggle="tooltip"><i class="fa fa-remove"></i></button>';
        var editBtn = '<button type="button" class="btn btn-primary btn-xs edit" title="编辑" data-toggle="tooltip"><i class="fa fa-pencil"></i></button>';
        var delBtn = '<button type="button" class="btn btn-danger btn-xs delete" title="删除" data-toggle="tooltip"><i class="fa fa-trash-o"></i></button>';

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
                    "url": "/admin/sourceapi/list",
                    "type": "POST",
                    "data": function(d) {
                        //添加额外的参数传给服务器
                        d.params = {};
                        if ($('#form-type').val()) {
                            d.params.type = $('#form-type').val();
                        }
                        if ($("#form-title").val()) {
                            d.params.name = $.trim($("#form-title").val());
                        }
                        if ($('#form-user_id').attr("checked")) {
                            d.params.create_user = $('#form-user_id').val();
                        }
                    }
                },
                "columnDefs": [{
                    "targets": [],
                    "visible": false,
                    "searchable": false
                }],
                "columns": [{
                    "data": "id"
                }, {
                    "data": "name"
                }, {
                    "data": "url"
                }, {
                    "data": "typename"
                }, {
                    "data": "groupname"
                }, {
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
                }, {
                    "data": function(e) {
                        return editBtn + delBtn;
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
                //状态下拉列表 
                $('#form-type').selectpicker();


                //分组下拉列表 
                //$('#source-group').selectpicker();

                //点击打开来源创建模态框
                $('.openSource').click(function(event) {
                    event.preventDefault();
                    //打开模态框
                    $('#source')[0].reset();
                    $('#source input[name="type"][value="2"]').click();
                    $('#source-group').val('0'); 
                    //$('#source-group').selectpicker(); 
                    //$('#source-group').selectpicker('val', '0');
                    $('#source-group').val('0'); 
                    $('#source-group').selectpicker('render');
                    $('#myModal').modal();
                    


                });

                $('#source input[name="type"]').click(function(event) {
                    if ($(this).val() == 1) {
                        //微信公众号
                        $('#source label[for="source-name"]').text('公众号名称:');
                        $('#source .source-url').hide();
                        $('#source-url').val('mp.weixin.qq.com');
                    } else if ($(this).val() == 2) {
                        $('#source label[for="source-name"]').text('来源名称:');
                        $('#source .source-url').show();
                        $('#source-url').val('');
                    } else if ($(this).val() == 3) {
                        $('#source label[for="source-name"]').text('来源名称:');
                        $('#source .source-url').hide();
                        $('#source-url').val('');
                    }
                });

                //点击创建话题来源
                $('#addSource-btn').click(function(event) {
                    var url = '/admin/Sourceapi/add';
                    var params = {
                        name: $('#source-name').val(),
                        url: $('#source-url').val(),
                        type: $('#source input[name="type"]:checked').val(),
                        group: $('#source-group').val()
                    }
                    if ($('#source-id').val()) {
                        params.id = $('#source-id').val();
                        url = '/admin/Sourceapi/edit';
                    }
                    bindEvents.saveSource(url, params);
                });

                //编辑
                $('#editable button.edit').live('click', function(e) {
                    e.preventDefault();
                    var nRow = $(this).parents('tr')[0];
                    var data = oTable.api().row(nRow).data();
                    $('#source input[name="type"][value="' + data.type + '"]').attr('checked', 'ture');
                    $('#source input[name="type"][value="' + data.type + '"]').click();
                    $('#source-id').val(data.id);
                    $('#source-name').val(data.name);
                    $('#source-url').val(data.url);

                  /*  $('#source-group').selectpicker(); 
                    $('#source-group').val(data.group);*/
                    //$('#source-group').selectpicker('val', data.group);
                    $('#source-group').val(data.group);
                    $('#source-group').selectpicker('render');
                    //打开模态框
                    $('#myModal').modal();
                });

                //删除
                $('#editable button.delete').live('click', function(e) {
                    e.preventDefault();
                    if (confirm("确定删除 ?") == false) {
                        return;
                    }
                    var nRow = $(this).parents('tr')[0];
                    var data = oTable.api().row(nRow).data();
                    $.ajax({
                        "url": "/admin/Sourceapi/del",
                        "data": data,
                        "type": "post",
                        "error": function(e) {
                            alert("服务器未正常响应，请重试");
                        },
                        "success": function(response) {
                            if (response.status == 0) {
                                toastr.success('删除成功');
                                oTable.fnRefresh();
                            } else {
                                alert(response.statusInfo);
                            }
                        }
                    });
                });
            },
            /**
             * 提交数据
             * @param  {[type]} url [description]
             * @return {[type]}     [description]
             */
            saveSource: function(url, data) {
                if (!data.name) {
                    toastr.warning('名称不能为空');
                    return false;
                }
                if (data.type == '2' && !data.url) {
                    toastr.warning('url不能为空');
                    return false;
                }
                $.ajax({
                    "url": url,
                    "data": data,
                    "async": false,
                    "error": function(e) {
                        alert("服务器未正常响应，请重试");
                    },
                    "success": function(response) {
                        if (response.status != 0) {
                            alert(response.statusInfo);
                        } else {
                            //手工关闭模态框
                            $('#myModal').modal('hide');
                            document.getElementById("source").reset();
                            oTable.fnRefresh();
                        }
                    }
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
            $('#form-type').change(function(event) {
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
