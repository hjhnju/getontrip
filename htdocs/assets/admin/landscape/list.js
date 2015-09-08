/*
   景观列表
   author:fyy
 */
$(document).ready(function() {
    var oTable = $('#editable').dataTable({
        "serverSide": true, //分页，取数据等等的都放到服务端去
        "processing": true, //载入数据的时候是否显示“载入中”
        "pageLength": 10, //首次加载的数据条数  
        "searching": false, //是否开启本地分页
        "ordering": false,
        "ajax": {
            "url": "/admin/landscapeapi/list",
            "type": "POST",
            "data": function(d) {
                //添加额外的参数传给服务器
                d.params = {};
                if ($("#form-landscape").attr('data-landscape_id')) {
                    d.params.id = $("#form-landscape").attr('data-landscape_id');
                } else if ($("#form-city").attr('data-city_id')) {
                    d.params.city_id = $("#form-city").attr('data-city_id');
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
            "targets": [1],
            "visible": false,
            "searchable": false
        }, {
            "targets": [3, 4, 5, 6, 7],
            "width": 80
        }],
        "columns": [{
            "data": "id"
        }, {
            "data": 'city_id'
        }, {
            "data": "name"
        }, {
            "data": function(e) {
                if (e.image) {
                    return '<a href="/pic/' + e.image + '" target="_blank"><img alt="" src="/pic/' + e.image.getNewImgByImg(80, 22, 'f') + '"/></a>';
                }
                return "未上传";
            }
        }, {
            "data": 'city_name'
        }, {
            "data": 'x'
        }, {
            "data": 'y'
        }, {
            "data": function(e) {
                if (e.statusName == '未发布') {
                    return e.statusName + '<button type="button" class="btn btn-primary btn-xs publish" title="发布" data-toggle="tooltip" ><i class="fa fa-check-square-o"></i></button>';
                } else {
                    return e.statusName + '<button type="button" class="btn btn-warning btn-xs cel-publish" title="取消发布" data-toggle="tooltip" ><i class="fa fa-close"></i></button>';
                }

            }
        }, {
            "data": function(e) {
                return '<a class="btn btn-primary btn-xs edit" title="编辑" data-toggle="tooltip" href="/admin/landscape/edit?action=edit&id=' + e.id + '"><i class="fa fa-pencil"></i></a>'; //  + '<button type="button" class="btn btn-danger btn-xs delete"  title="删除" data-toggle="tooltip"><i class="fa fa-trash-o "></i></button>';
            }
        }],
        "initComplete": function(setting, json) {
            //工具提示框
            //$('[data-toggle="tooltip"]').tooltip();
        }
    });

    var api = oTable.api();
    bindEvents();
    filter();

    var validate = null;


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

        $('#editable button.delete').live('click', function(e) {
            e.preventDefault();
            if (confirm("确定删除么 ?") == false) {
                return;
            }
            var nRow = $(this).parents('tr')[0];
            var data = oTable.api().row(nRow).data();
            $.ajax({
                "url": "/admin/landscapeapi/del",
                "data": data,
                "type": "post",
                "error": function(e) {
                    alert("服务器未正常响应，请重试");
                },
                "success": function(response) {
                    if (response.status == 0) {
                        toastr.success('删除成功');
                        oTable.fnDeleteRow(nRow);
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
            var publish = new Remoter('/admin/landscapeapi/publish');
            publish.remote({
                id: data.id,
                action: action
            });
            publish.on('success', function(data) {
                //刷新当前页
                oTable.fnRefresh();
            }); 
        });
    }


    function filter() {
        //输入内容点击回车查询
        $("#form-landscape").keydown(function(event) {
            if (event.keyCode == 13) {
                api.ajax.reload();
            }
        });
        //景观输入框自动完成
        $('#form-landscape').typeahead({
            display: 'name',
            val: 'id',
            ajax: {
                url: '/admin/landscapeapi/getLandscapeListAction',
                triggerLength: 1
            },
            itemSelected: function(item, val, text) {
                $("#form-landscape").val(text);
                $("#form-landscape").attr('data-landscape_id', val);
                //触发dt的重新加载数据的方法
                api.ajax.reload();
            }
        });

        //景观框后的清除按钮，清除所选的景观
        $('#clear-landscape').click(function(event) {
            $("#form-landscape").val('');
            $("#form-landscape").attr('data-landscape_id', '');
            //触发dt的重新加载数据的方法
            api.ajax.reload();
        });


        //城市输入框自动完成
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

        //景观框后的清除按钮，清除所选的景观
        $('#clear-city').click(function(event) {
            $("#form-city").val('');
            $("#form-city").attr('data-city_id', '');
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
    }


});
