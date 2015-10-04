(function(window, document, undefined) {
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
                "searching": false,
                "ordering": false,
                "ajax": {
                    "url": "/admin/adminuserapi/list",
                    "type": "POST",
                    "data": function(d) {
                        //添加额外的参数传给服务器
                        d.params = {};
                        if ($("#form-name").val()) {
                            d.params.name = $.trim($("#form-name").val());
                        }
                        if ($('#form-role').val()) {
                            d.params.role = $.trim($("#form-role").val());
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
                    "data": "role_name"
                }, {
                    "data": function(e) {
                        if (e.create_time) {
                            return moment.unix(e.create_time).format(FORMATER);
                        }
                        return "-";
                    }
                }, {
                    "data": function(e) {
                        if (e.update_time) {
                            return moment.unix(e.update_time).format(FORMATER);
                        }
                        return "-";
                    }
                }, {
                    "data": function(e) {
                        if (e.login_time) {
                            return moment.unix(e.login_time).format(FORMATER);
                        }
                        return "-";
                    }
                }, {
                    "data": function(e) {
                        return '<a href="/admin/adminuser/edit?id=' + e.id + '" class="btn btn-primary btn-xs" title="编辑" data-toggle="tooltip"><i class="fa fa-pencil"></i></a>' + '<button class="btn btn-warning btn-xs passwd" title="重置密码" data-toggle="tooltip"><i class="fa fa-key"></i></button>';
                    }
                }],
                "initComplete": function(setting, json) {

                }
            });
            api = oTable.api();
        }

        /*
              绑定事件
         */
        var bindEvents = function() {
            //绑定draw事件
            $('#editable').on('draw.dt', function() {
                //工具提示框
                $('[data-toggle="tooltip"]').tooltip();
            });

            //角色下拉列表 
            $('#form-role').selectpicker();

            //重置密码操作
            $('#editable button.passwd').live('click', function(e) {

                if (confirm("确定重置么 ?") == false) {
                    return;
                }
                var nRow = $(this).parents('tr')[0];
                var data = oTable.api().row(nRow).data();
                var setpwd = new Remoter('/admin/AdminUserapi/setpwd');
                setpwd.remote({
                    id: data.id
                });
                setpwd.on('success', function(data) {
                    oTable.fnDeleteRow(nRow);
                    toastr.success('重置密码成功');
                });
            });
        }

        /*
          过滤事件
         */
        var filter = function() {

            //输入内容点击回车查询
            $("#form-name").keydown(function(event) {
                if (event.keyCode == 13) {
                    api.ajax.reload();
                }
            });
            //选择角色
            $('#form-role').change(function(event) {
                api.ajax.reload();
            });

        }

        return {
            init: function() {
                initTable()
                bindEvents();
                filter();
            }
        }
    }

    new List().init();

}(window, document));
