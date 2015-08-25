/*
   景点列表
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
            "url": "/admin/sightapi/list",
            "type": "POST",
            "data": function(d) {
                //添加额外的参数传给服务器
                d.params = {};
                if ($("#form-sight").attr('data-sight_id')) {
                    d.params.id = $("#form-sight").attr('data-sight_id');
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
        },{
            "targets": [3,4,5,6,7],
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
                if(e.image){
                   return '<a href="/pic/' + e.image + '" target="_blank"><img alt="" src="/pic/' + e.image.getNewImgByImg(80,22,'f') + '"/></a>';
                }
                 return "未上传";
            }
        }, {
            "data": 'city_name'
        }, {
            "data": function(e) {
                if (e.level) {
                    return e.level;
                }
                return '未评级';
            }
        }, {
            "data": 'x'
        }, {
            "data": 'y'
        }, {
            "data": function(e){
                if (e.statusName == '未发布') {
                    return e.statusName + '<button type="button" class="btn btn-primary btn-xs publish" title="发布" data-toggle="tooltip" ><i class="fa fa-check-square-o"></i></button>';
                } else {
                    return e.statusName + '<button type="button" class="btn btn-warning btn-xs cel-publish" title="取消发布" data-toggle="tooltip" ><i class="fa fa-close"></i></button>';
                }
                
            }
        }, {
            "data": function(e) {
                return '<a class="btn btn-primary btn-xs edit" title="编辑" data-toggle="tooltip" href="/admin/sight/edit?action=edit&id=' + e.id + '"><i class="fa fa-pencil"></i></a>' + '<button type="button" class="btn btn-success btn-xs addKeyword"  title="添加词条" data-toggle="tooltip"><i class="fa fa-buysellads"></i></button>' + '<button type="button" class="btn btn-danger btn-xs delete"  title="删除" data-toggle="tooltip"><i class="fa fa-trash-o "></i></button>';
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
    addKeyword();

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
                "url": "/admin/sightapi/del",
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



        //点击打开添加词条模态框
        $("#editable button.addKeyword").live('click', function(event) {
            var nRow = $(this).parents('tr')[0];
            var data = oTable.api().row(nRow).data();
            $('#name').val('');
            $('#url').val('');
            $('#sight_name').val(data.name);
            $('#sight_id').val(data.id);
            $('#Form input').removeClass('error');
            $('#Form .error').hide();
            //打开模态框 
            $('#myModal').modal({});
        });

        //输入词条名称生成url
        $('#name').blur(function(event) {
            var name = $.trim($(this).val());
            if (name) {
                $('#url').val('http://baike.baidu.com/item/' + name);
                $('#view-link').attr('href', $('#url').val());
            }
        });
        //点击保存词条或者确认并保存按钮
        $('#Form button[type="submit"]').click(function(event) {
            $('#status').val($(this).attr('data-status'));
        });

        //发布操作
        $('#editable button.publish,#editable button.cel-publish').live('click', function(e) {
            e.preventDefault();
            var nRow = $(this).parents('tr')[0];
            var data = oTable.api().row(nRow).data(); 
            if ($(this).hasClass('publish')) {
                url = '/admin/sightapi/publish';
            } else {
                url = '/admin/sightapi/cancelpublish';
            }
            $.ajax({
                "url": url,
                "data": {
                    id: data.id 
                },
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
    }


    function filter() {
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

        //景点框后的清除按钮，清除所选的景点
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

    function getCityNameById(city_id) {
        for (var key in cityArray) {
            var item = cityArray[key];
            if (item.id == city_id) {
                return item.name;
            }
        }
        return "";
    }

    function addKeyword() {
        $.validator.setDefaults({
            submitHandler: function(data) {
                //序列化表单  
                var param = $("#Form").serializeObject();
                $.ajax({
                    "url": "/admin/keywordapi/add",
                    "data": param,
                    "type": "post",
                    "dataType": "json",
                    "error": function(e) {
                        alert("服务器未正常响应，请重试");
                    },
                    "success": function(response) {
                        if (response.status == 0) {
                            toastr.success('保存成功');
                            //手工关闭模态框
                            $('#myModal').modal('hide');
                        }
                    }
                });

            }
        });

        validations();



        /*
          表单验证
       */
        function validations() {
            // validate signup form on keyup and submit
            validate = $("#Form").validate({
                rules: {
                    name: "required",
                    sight_name: "required",
                    url: "required"
                },
                messages: {
                    name: "名称不能为空！",
                    sight_name: "景点名称不能为空哦！",
                    url: "链接url不能为空!"
                }
            });
        }
    }
});
