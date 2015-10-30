$(document).ready(function() {
    var List = function() {
        var editBtn = '<a class="btn btn-primary btn-xs edit" title="编辑" data-toggle="tooltip"><i class="fa fa-pencil"></i></a>' + '<button type="button" class="btn btn-danger btn-xs delete"  title="删除" data-toggle="tooltip"><i class="fa fa-trash-o "></i></button>';
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
                            d.params.create_user = $('#form-user_id').val();
                        }
                    }
                },
                "columnDefs": [{
                    "targets": [0],
                    "visible": true,
                    "searchable": false
                }, {
                    "targets": [0, 1],
                    "width": 20
                }, {
                    "targets": [2],
                    "width": 250
                }, {
                    "targets": [3, 4, 5],
                    "width": 50
                }, {
                    "targets": [6, 7, 8],
                    "width": 50
                }, {
                    "targets": [9],
                    "width": 100
                }],
                "columns": [{
                    "data": function() {
                        return '<td class="center "><a class="btn btn-success btn-xs" for="details" title="详情" data-toggle="tooltip"><i class="fa fa-plus"></i></a></td>';
                    }
                }, {
                    "data": "id"
                }, {
                    "data": 'title'
                }, {
                    "data": "fromName"
                }, {
                    "data": function(e) {
                        if (e.url) {
                            return '<a href="' + e.url + '" target="_blank" title="' + e.url + '">' + (e.url.length > 20 ? e.url.substr(0, 20) + '...' : e.url) + '</a>';
                        }
                        return '暂无';
                    }

                }, {
                    "data": function(e) {
                        var sights = e.sights;
                        var strArray = [];
                        for (var i = 0; i < sights.length; i++) {
                            strArray.push(sights[i].sight_name);
                        }
                        return strArray.join(',');
                    }

                }, {
                    "data": function(e) {
                        if (e.image) {
                            return '<a href="/pic/' + e.image + '" target="_blank"><img alt="" src="/pic/' + e.image.getNewImgByImg(80, 22, 'f') + '"/></a>';
                        }
                        return "未上传";
                    }
                }, {
                    "data": function(e) {
                        return e.collect + '/' + e.comment;
                    }
                }, {
                    "data": function(e) {
                        if (e.symbols) {
                            return '有';
                        }
                        return "无";
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
                        //return '<button type="button" class="btn btn-success btn-xs copy-button" title="复制链接" data-toggle="tooltip" data-clipboard-text="' + $('#webroot').html() + '/topic/detail?id=' + e.id + '"><i class="fa fa-eye"></i></button><a class="btn btn-primary btn-xs edit" title="编辑" data-toggle="tooltip" href="/admin/topic/edit?action=edit&id=' + e.id + '" target="_blank"><i class="fa fa-pencil"></i></a>' + '<button type="button" class="btn btn-danger btn-xs delete"  title="删除" data-toggle="tooltip"><i class="fa fa-trash-o "></i></button>';

                        return '<a href="/admin/comment/list?id=' + e.id + '&table=topic" target="_blank" class="btn btn-warning btn-xs comments" title="评论列表" data-toggle="tooltip"><i class="fa fa-comments-o"></i></a>' + '<button type="button" class="btn btn-success btn-xs copy-button" title="复制链接" data-toggle="tooltip" data-clipboard-text="' + $('#webroot').html() + '/topic/detail?id=' + e.id + '"><i class="fa fa-eye"></i></button><a class="btn btn-primary btn-xs edit" title="编辑" data-toggle="tooltip" href="/admin/topic/edit?action=edit&id=' + e.id + '" target="_blank"><i class="fa fa-pencil"></i></a>' + '<button type="button" class="btn btn-danger btn-xs delete"  title="删除" data-toggle="tooltip"><i class="fa fa-trash-o "></i></button>';
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
                //绑定draw事件
                $('#editable').on('draw.dt', function() {
                    //工具提示框
                    $('[data-toggle="tooltip"]').tooltip();

                    //复制链接 
                    var client = new ZeroClipboard($(".copy-button"));
                    client.on("ready", function(readyEvent) {
                        client.on("aftercopy", function(event) {
                            toastr.success('预览链接复制成功！');
                        });
                    });
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
                        if (!data.subtitle) {
                            toastr.warning('副标题不能为空！');
                            return;
                        }
                        if (!data.isContent) {
                            toastr.warning('正文不能为空！');
                            return;
                        }
                        if (!data.from) {
                            toastr.warning('指定来源不能为空！');
                            return;
                        }
                        if (!data.from_detail && !data.url) {
                            toastr.warning('详细来源和原文链接不能同时为空！');
                            return;
                        }
                        if (!data.sights.length) {
                            if (!data.tagList.generalTag.length) {
                                toastr.warning('通用标签不能为空！');
                                return;
                            } 
                        } else {
                            if (!data.tagList.classifyTag.length) {
                                toastr.warning('分类标签不能为空！');
                                return;
                            } 
                        }

                        action = 'PUBLISHED';
                    } else {
                        action = 'NOTPUBLISHED';
                    }
                    var publish = new Remoter('/admin/topicapi/changeStatus');
                    publish.remote({
                        id: data.id,
                        action: action
                    });
                    publish.on('success', function(data) {
                        //刷新当前页
                        oTable.fnRefresh();
                    });



                    /*     var status = data.status;
                         if ($(this).hasClass('publish')) {
                             status = 5;
                         } else {
                             status = 1;
                         }
                         $.ajax({
                             "url": "/admin/topicapi/changeStatus",
                             "data": {
                                 id: data.id,
                                 status: status
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
                         });*/
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
                        oTable.fnOpen(nTr, this.fnFormatDetails(oTable, nTr), 'details');
                    }
                });
            },
            fnFormatDetails: function(oTable, nTr) {
                // return moment.unix(e.update_time).format(FORMATER);
                var aData = oTable.fnGetData(nTr);
                var sOut = '<table cellpadding="5" cellspacing="0" border="0" width="100%">';
                //sOut += '<tr><td>副标题:' + aData.subtitle?aData.subtitle:'空' + '</td><td></td></tr>'; 
                sOut += '<tr><td>创建时间:' + moment.unix(aData.create_time).format(FORMATER) + '</td><td>更新时间:' + moment.unix(aData.update_time).format(FORMATER) + '</td></tr>';
                sOut += '</table>';
                return sOut;
            }
        }

        /*
              过滤事件
         */
        var filter = function() {
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
