/*

 用户评论列表
  author:fyy
 */
$(document).ready(function() {
    var FORMATER = 'YYYY-MM-DD HH:mm:ss';
    var oTable = $('#editable').dataTable({
        "serverSide": true, //分页，取数据等等的都放到服务端去
        "processing": true, //载入数据的时候是否显示“载入中”
        "pageLength": 10, //首次加载的数据条数  
        "searching": false, //是否开启本地分页
        "ordering": false,
        "ajax": {
            "url": "/admin/adviseapi/list",
            "type": "POST",
            "data": function(d) {
                d.topicId = $.trim($('#topicId').val());
                d.params = {};
                //添加额外的参数传给服务器 
                if ($("#form-status").val()) {
                    d.params.status = $.trim($("#form-status").val());
                }
                if ($("#form-type").val()) {
                    d.params.type = $.trim($("#form-type").val());
                }
            }
        },
        "columnDefs": [{
            "targets": [0],
            "visible": true,
            "searchable": false
        }, {
            "targets": [0, 1, 2, 4, 8, 9],
            "width": 30
        }, {
            "targets": [5, 6, 7],
            "width": 80
        }],
        "columns": [{
            "data": function() {
                return '<td class="center "><a class="btn btn-success btn-xs" for="details" title="回复列表" data-toggle="tooltip"><i class="fa fa-plus"></i></a></td>';
            }
        }, {
            "data": "id"
        }, {
            "data": function(e) {
                return '<a href="#?id=' + e.userid + '" title="' + e.userid + '">' + e.userid + '</a>';
            }
        }, {
            "data": 'content'
        }, {
            "data": "type_name"
        }, {
            "data": function(e) {
                if (e.create_time) {
                    return moment.unix(e.create_time).format(FORMATER);
                }
                return "-";
            }
        }, {
            "data": function(e) {
                if (!e.create_user) {
                    return '-';
                }
                return '<a href="#?id=' + e.create_user + '" title="' + e.create_user + '">' + e.create_user.toString().subString(10) + '</a>';
            }
        }, {
            "data": function(e) {
                if (e.update_time) {
                    return moment.unix(e.update_time).format(FORMATER);
                }
                return "-";
            }
        }, {
            "data": "status_name"
        }, {
            "data": function(e) {
                /*if (e.status_name == '未发布') {
                    return '<button type="button" data-id="' + e.id + '" class="btn btn-primary btn-xs publish" title="发布" data-toggle="tooltip" ><i class="fa fa-check-square-o"></i></button>';
                } else {
                    return '<button type="button" data-id="' + e.id + '"  class="btn btn-warning btn-xs cel-publish" title="取消发布" data-toggle="tooltip" ><i class="fa fa-close"></i></button>';
                }*/
                return '<button type="button" class="btn btn-success btn-xs answer"  title="回复" data-toggle="tooltip"><i class="fa fa-pencil-square-o"></i></button>';
            }
        }],
        "initComplete": function(setting, json) {
            //工具提示框
            //$('[data-toggle="tooltip"]').tooltip();
        }
    });

    var api = oTable.api();
    filters();
    bindEvents();


    function bindEvents() {
        //绑定draw事件
        $('#editable').on('draw.dt', function() {
            //工具提示框
            $('[data-toggle="tooltip"]').tooltip();
        });

        //状态下拉列表 
        $('#form-status').selectpicker();

        //类型下拉列表 
        $('#form-type').selectpicker();

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
                oTable.fnOpen(nTr, fnFormatDetails(oTable, nTr), 'details answer-details');
                //工具提示框
                $('[data-toggle="tooltip"]').tooltip();
            }
        });

        //点击回复弹出框
        $('#editable button.answer').live('click', function(e) {
            e.preventDefault();
            var nRow = $(this).parents('tr')[0];
            var data = oTable.api().row(nRow).data();
            
            $('#user-id').val(data.id);
            $('#user-content').val(data.content);
            $('#myModal').modal(); 
        });

        //对某个反馈进行处理
        $('#answerForm #addAnswer').click(function(event) {
            if(!$('#content').val()){
                toastr.warning('回复内容不能为空！');
                return;
            } 
            var addAnswer = new Remoter('/admin/Adviseapi/addAnswer');
            addAnswer.remote({
                id: $('#user-id').val(),
                content: $.trim($('#content').val())
            });
            addAnswer.on('success', function(data) {
                //刷新当前页
                oTable.fnRefresh();
                //关闭模态框
                 $('#myModal').modal('hide'); 
            }); 
        });
    }

    function filters() {
        $('#form-status,#form-type').change(function(event) {
            //触发dt的重新加载数据的方法
            api.ajax.reload();
        });
    }



    fnFormatDetails = function(oTable, nTr) {
        var aData = oTable.fnGetData(nTr).answer;
        var sOut = '<table cellpadding="5" cellspacing="0" border="0" width="100%">';
        sOut += '<tr><th></th><th>ID</th><th>回复内容</th><th>处理人ID</th><th>处理时间</th></tr>';
        for (var i = 0; i < aData.length; i++) {
            sOut += '<tr><td style="width:29px;"></td>';
            sOut += '<td  style="width:50px;">' + aData[i].id + '</td>';
            sOut += '<td>' + aData[i].content + '</td>';
            sOut += '<td style="width:70px;"><a href="#?id=' + aData[i].create_user + '" title="' + aData[i].create_user + '">' + aData[i].create_user.toString().subString(10) + '</a>';
            sOut += '<td style="width:70px;">' + moment.unix(aData[i].create_time).format(FORMATER) + '</td>';
            sOut += '</tr>';
        };
        sOut += '</table>';
        return sOut;
    }
});
