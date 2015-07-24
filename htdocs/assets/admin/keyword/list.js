$(document).ready(function() {
    var editBtn = '<a class="btn btn-primary btn-xs edit" title="编辑" data-toggle="tooltip"><i class="fa fa-pencil"></i></a>' + '<button type="button" class="btn btn-success btn-xs addKeyword"  title="删除" data-toggle="tooltip"><i class="fa fa-trash-o "></i></button>' ;
    var FORMATER = 'YYYY-MM-DD HH:mm:ss';
    var oTable = $('#editable').dataTable({
        "serverSide": true, //分页，取数据等等的都放到服务端去
        "processing": true, //载入数据的时候是否显示“载入中”
        "pageLength": 10, //首次加载的数据条数  
        "searching": false, //是否开启本地分页
        "ajax": {
            "url": "/admin/topicapi/list",
            "type": "POST",
            "data": function(d) {
                //添加额外的参数传给服务器
                d.params = {};
                if ($("#form-sight").val()) {
                    d.params.sight_id = Number($.trim($("#form-sight").attr('data-sight_id')));
                }
                if ($("#form-title").val()) {
                    d.params.title = $.trim($("#form-title").val());
                }
                if ($("#form-status").val()) {
                    d.params.status = $.trim($("#form-status").val());
                }
                if ($('#form-user_id').attr("checked")) {
                    d.params.user_id = $('#form-user_id').val();
                }
            }
        },
        "columnDefs": [{
            "targets": [0],
            "visible": false,
            "searchable": false
        }, {
            "targets": [1],
            "orderable": false,
            "width": 20
        }],
        "columns": [{
            "data": "id"
        }, {
            "data": function() {
                return '<td class="center "><a class="btn btn-success btn-xs" for="details" title="详情" data-toggle="tooltip"><i class="fa fa-plus"></i></a></td>';
            }
        }, {
            "data": 'title'
        }, {
            "data": "subtitle"
        }, {
            "data": "statusName"
        }, {
            "data": function(e) {
                if (e.create_time) {
                    return moment.unix(e.create_time).format(FORMATER);
                }
                return "空";
            }
        }, {
            "data": function(e) {
                if (e.create_time) {
                    return moment.unix(e.update_time).format(FORMATER);
                }
                return "空";
            }
        }, {
            "data": function(e) {
                return '<a class="btn btn-success btn-xs edit" title="查看" data-toggle="tooltip" href="/admin/topic/edit?action=view&id=' + e.id + '"><i class="fa fa-eye"></i></a><a class="btn btn-primary btn-xs edit" title="编辑" data-toggle="tooltip" href="/admin/topic/edit?action=edit&id=' + e.id + '"><i class="fa fa-pencil"></i></a>' + '<button type="button" class="btn btn-danger btn-xs delete"  title="删除" data-toggle="tooltip"><i class="fa fa-trash-o "></i></button>';
            }
        }],
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

        filters();

        //绑定draw事件
        $('#editable').on('draw.dt', function() {
            //工具提示框
            $('[data-toggle="tooltip"]').tooltip();
        });

        //删除操作
        $('#editable button.delete').live('click', function(e) {
            e.preventDefault();
            if (confirm("确定删除么 ?") == false) {
                return;
            }
            var nRow = $(this).parents('tr')[0];
            var data = oTable.api().row(nRow).data();
            $.ajax({
                "url": "/admin/topicapi/del",
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

        //打开关闭详情
        $('#editable').delegate('tbody td a[for="details"]', 'click', function(event) {
            event.preventDefault();
            var nTr = $(this).parents('tr')[0];
            var img = $(this).find('i');
            if (oTable.fnIsOpen(nTr)) {
                /* This row is already open - close it */
                img.attr('class', 'fa fa-plus');
                oTable.fnClose(nTr);
            } else {
                /* Open this row */
                img.attr('class', 'fa fa-minus');
                oTable.fnOpen(nTr, fnFormatDetails(oTable, nTr), 'details');
            }
        });

     
    }

    /*
      过滤事件
     */
    function filters() {
        //输入内容点击回车查询
        $("#form-title,#form-sight").keydown(function(event) {
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
        //状态下拉列表 
        $('#form-status').selectpicker();

        //只看我自己发布的
        $('#form-user_id').click(function(event) {
            api.ajax.reload();
        });
    }

    function fnFormatDetails(oTable, nTr) {
        var aData = oTable.fnGetData(nTr);
        var sOut = '<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">';
        sOut += '<tr><td>Rendering engine:</td><td>' + aData[1] + ' ' + aData[4] + '</td></tr>';
        sOut += '<tr><td>Link to source:</td><td>Could provide a link here</td></tr>';
        sOut += '<tr><td>Extra info:</td><td>And any further details here (img etc)</td></tr>';
        sOut += '</table>';
        return sOut;
    }

});
