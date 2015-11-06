/*

 用户评论列表
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
                    "url": "/admin/Commentapi/list",
                    "type": "POST",
                    "data": function(d) {
                        d.obj_id = $.trim($('#form-obj_id').val());
                        d.type = $.trim($('#form-type').val())
                        d.params = {};
                        //添加额外的参数传给服务器 
                        if ($("#form-status").val()) {
                            d.params.status = $.trim($("#form-status").val());
                        }
                    }
                },
                "columnDefs": [{
                    "targets": [0],
                    "visible": true,
                    "searchable": false
                }, {
                    "targets": [0, 1, 2, 3, 4, 9],
                    "width": 20
                }, {
                    "targets": [6, 7, 8],
                    "width": 70
                }],
                "columns": [{
                    "data": function() {
                        return '<td class="center "><a class="btn btn-success btn-xs" for="details" title="回复列表" data-toggle="tooltip"><i class="fa fa-plus"></i></a></td>';
                    }
                }, {
                    "data": "id"
                }, {
                    "data": 'obj_id'
                }, {
                    "data": function(e) {
                        return '<a href="#?id=' + e.from_user_id + '" title="' + e.from_name + '">' + e.from_name.subString(10) + '</a>';
                    }
                }, {
                    "data": function(e) {
                        return '<a href="#?id=' + e.to_user_id + '" title="' + e.to_name + '">' + e.to_name.subString(10) + '</a>';
                    }
                }, {
                    "data": "content"
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
                    "data": "status_name"
                }, {
                    "data": function(e) {
                        if (e.status_name == '未发布') {
                            return '<button type="button" data-id="' + e.id + '" class="btn btn-primary btn-xs publish" title="发布" data-toggle="tooltip" ><i class="fa fa-check-square-o"></i></button>';
                        } else {
                            return '<button type="button" data-id="' + e.id + '"  class="btn btn-warning btn-xs cel-publish" title="取消发布" data-toggle="tooltip" ><i class="fa fa-close"></i></button>';
                        }
                        //return '<button type="button" class="btn btn-danger btn-xs delete"  title="删除" data-toggle="tooltip"><i class="fa fa-trash-o "></i></button>';
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
                });

                //状态下拉列表 
                $('#form-status').selectpicker();

                //打开关闭回复列表
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
                        //工具提示框
                        $('[data-toggle="tooltip"]').tooltip();
                    }
                });

                //发布操作
                $('#editable button.publish,#editable button.cel-publish').live('click', function(e) {
                    e.preventDefault();
                    if ($(this).hasClass('publish')) {
                        action = 'PUBLISHED';
                    } else {
                        action = 'NOTPUBLISHED';
                    }
                    var delComment = new Remoter('/admin/Commentapi/changeStatus');
                    delComment.remote({
                        id: $(this).attr('data-id'),
                        action: action
                    });
                    delComment.on('success', function(data) {
                        //刷新当前页
                        oTable.fnRefresh();
                    });
                    delComment.on('fail', function(data) {
                        alert(data);
                    });
                });
            }
        }

        /*
               过滤事件
           */
        var filter = function() {
            $('#form-status').change(function(event) {
                //触发dt的重新加载数据的方法
                api.ajax.reload();
            });
        }



        var fnFormatDetails = function(oTable, nTr) {
            var aData = oTable.fnGetData(nTr).subComment;
            var sOut = '<table cellpadding="5" cellspacing="0" border="0" width="100%">';
            for (var i = 0; i < aData.length; i++) {
                sOut += '<tr><td style="width:29px;"></td>';
                sOut += '<td  style="width:50px;">ID:' + aData[i].id + '</td>';
                sOut += '<td><a href="#?id=' + aData[i].from_user_id + '" title="' + aData[i].from_name + '">' + aData[i].from_name.subString(10) + '</a>';
                sOut += '回复<a href="#?id=' + aData[i].to_user_id + '" title="' + aData[i].to_name + '">' + aData[i].to_name.subString(10) + '</a>';

                sOut += ':<div>' + aData[i].content + '</div></td>';
                sOut += '<td style="width:70px;">' + moment.unix(aData[i].create_time).format(FORMATER) + '</td>';
                sOut += '<td style="width:40px;">' + aData[i].status_name + '</td>';
                if (aData[i].status_name == '未发布') {
                    sOut += '<td style="width:40px;"><button type="button" data-id="' + aData[i].id + '" class="btn btn-primary btn-xs publish" title="发布" data-toggle="tooltip" ><i class="fa fa-check-square-o"></i></button></td>';
                } else {
                    sOut += '<td style="width:40px;"><button type="button" data-id="' + aData[i].id + '"  class="btn btn-warning btn-xs cel-publish" title="取消发布" data-toggle="tooltip" ><i class="fa fa-close"></i></button></td>';
                }
                sOut += '<tr>';
            };
            sOut += '</table>';
            return sOut;
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
