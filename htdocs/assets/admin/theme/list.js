$(document).ready(function() {
    var editBtn = '<a class="btn btn-primary btn-xs edit" title="编辑" data-toggle="tooltip"><i class="fa fa-pencil"></i></a>' + '<button type="button" class="btn btn-danger btn-xs delete"  title="删除" data-toggle="tooltip"><i class="fa fa-trash-o "></i></button>';
    var FORMATER = 'YYYY-MM-DD HH:mm:ss';
    var oTable = $('#editable').dataTable({
        "serverSide": true, //分页，取数据等等的都放到服务端去
        "processing": true, //载入数据的时候是否显示“载入中”
        "pageLength": 10, //首次加载的数据条数  
        "searching": false, //是否开启本地分页
        "ordering": false,
        "ajax": {
            "url": "/admin/themeapi/list",
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
                    d.params.create_user = $('#form-user_id').val();
                }
            }
        },
        "columnDefs": [{
            "targets": [],
            "visible": true,
            "searchable": false
        }, {
            "targets": [0],
            "width": 20
        }, {
            "targets": [1,2],
            "width": 250
        }, {
            "targets": [3,4,5],
            "width": 80
        }],
        "columns": [
            /*{
                        "data": function() {
                            return '<td class="center "><a class="btn btn-success btn-xs" for="details" title="详情" data-toggle="tooltip"><i class="fa fa-plus"></i></a></td>';
                        }
                    },*/
            {
                "data": "id"
            }, {
                "data": 'name'
            }, {
                "data": "title"
            }, {
                "data": function(e) {
                    if (e.landscape) {
                        var landscape = e.landscape;
                        var strArray = [];
                        for (var i = 0; i < landscape.length; i++) {
                            strArray.push(landscape[i].name);
                        }
                        return strArray.join(',');
                    }
                    return '';
                }

            }, {
                "data": function(e) {
                    if (e.image) {
                        return '<a href="/pic/' + e.image + '" target="_blank"><img alt="" src="/pic/' + e.image.getNewImgByImg(80,22,'f') + '"/></a>';
                    }
                    return "未上传";
                }
            }, {
                "data": function(e) {
                    return e.period;
                }
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
                    return '<a class="btn btn-primary btn-xs edit" title="编辑" data-toggle="tooltip" href="/admin/theme/edit?action=edit&id=' + e.id + '" target="_blank"><i class="fa fa-pencil"></i></a>' + '<button type="button" class="btn btn-danger btn-xs delete"  title="删除" data-toggle="tooltip"><i class="fa fa-trash-o "></i></button>';
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

        filters();

        //绑定draw事件
        $('#editable').on('draw.dt', function() {
            //工具提示框
            $('[data-toggle="tooltip"]').tooltip();
 
        });

        //删除操作
        $('#editable button.delete').live('click', function(e) {
            e.preventDefault();
            if (confirm("确定删除么？删除后不可恢复！") == false) {
                return false;
            }
            var nRow = $(this).parents('tr')[0];
            var data = oTable.api().row(nRow).data();
            $.ajax({
                "url": "/admin/themeapi/del",
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
            var params={
                id: data.id 
            } 
            if ($(this).hasClass('publish')) {
                params.action = 'publish';
            } else {
                params.action = 'save';
            }
            $.ajax({
                "url": "/admin/themeapi/publish",
                "data":params,
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
        // return moment.unix(e.update_time).format(FORMATER);
        var aData = oTable.fnGetData(nTr);
        var sOut = '<table cellpadding="5" cellspacing="0" border="0" width="100%">';
        //sOut += '<tr><td>副标题:' + aData.subtitle?aData.subtitle:'空' + '</td><td></td></tr>'; 
        sOut += '<tr><td>创建时间:' + moment.unix(aData.create_time).format(FORMATER) + '</td><td>更新时间:' + moment.unix(aData.update_time).format(FORMATER) + '</td></tr>';
        sOut += '</table>';
        return sOut;
    }

});
