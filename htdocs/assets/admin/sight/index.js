$(document).ready(function() { 
    var oTable = $('#editable').dataTable({
        "serverSide": true, //分页，取数据等等的都放到服务端去
        "processing": true, //载入数据的时候是否显示“载入中”
        "pageLength": 10, //首次加载的数据条数  
        "searching": false, //是否开启本地分页
        "ajax": {
            "url": "/admin/sightapi/list",
            "type": "POST",
            "data": function(d) {
                //添加额外的参数传给服务器
                d.params = {};
                if ($("#form-city").attr('data-city_id')) {
                    d.params.city_id = $("#form-city").attr('data-city_id');
                }
            }
        },
        "columnDefs": [{
            "targets": [0, 1],
            "visible": false,
            "searchable": false
        }],
        "columns": [{
                "data": "id"
            }, {
                "data": 'city_id'
            }, {
                "data": "name"
            }, {
                "data": 'city_name'
            },
            /* {
                        "data": function(e) {
                            return getCityNameById(e.city_id);
                        }
                    }*/
            {
                "data": 'level'
            }, {
                "data": 'x'
            }, {
                "data": 'y'
            }, {
                "data": function(e) {
                    return '<a class="btn btn-primary btn-xs edit" title="编辑" data-toggle="tooltip" href="/admin/sight/edit?action=edit&id=' + e.id + '"><i class="fa fa-pencil"></i></a>' + '<button type="button" class="btn btn-danger btn-xs delete"  title="删除" data-toggle="tooltip"><i class="fa fa-trash-o "></i></button>'+ '<button type="button" class="btn btn-success btn-xs addKeyword"  title="添加词条" data-toggle="tooltip"><i class="fa fa-buysellads"></i></button>';
                }
            }
        ],
        "initComplete": function(setting, json) {
            //工具提示框
            //$('[data-toggle="tooltip"]').tooltip();
        }
    });

    var api = oTable.api();
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
                    alert('删除成功');
                    oTable.fnDeleteRow(nRow);
                    oTable.fnDraw();
                }
            });
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

        //添加词条
        $("#editable button.addKeyword").live('click', function(event) {
            //打开模态框 
            $('#myModal').modal({

            });
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
});
