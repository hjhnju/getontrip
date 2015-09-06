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
            "url": "/admin/msgapi/list",
            "type": "POST",
            "data": function(d) {
                d.params = {};
                //添加额外的参数传给服务器 
                if ($("#form-type").val()) {
                    d.params.type = Number($.trim($("#form-type").val()));
                }
                if ($("#form-status").val()) {
                    d.params.status = Number($.trim($("#form-status").val()));
                }
            }
        },
        "columnDefs": [{
            "targets": [0],
            "visible": true,
            "searchable": false
        }, {
            "targets": [1],
            "orderable": false,
            "width": 20
        }],
        "columns": [{
            "data": function() {
                return '<td class="center "><a class="btn btn-success btn-xs" for="details" title="详情" data-toggle="tooltip"><i class="fa fa-plus"></i></a></td>';
            }
        }, {
            "data": "mid"
        }, {
            "data": 'title'
        }, {
            "data": 'type_name'
        }, {
            "data": function(e) {
                if (e.image) {
                    return '<a href="/pic/' + e.image + '" target="_blank"><img alt="" src="/pic/' + e.image.getNewImgByImg(80, 22, 'f') + '"/></a>';
                }
                return "未上传";
            }
        }, {
            "data": "status_name"
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
                return '<a class="btn btn-success btn-xs view" title="查看" data-toggle="tooltip" href="/admin/msg/edit?action=view&id=' + e.mid + '"><i class="fa fa-eye"></i></a>';
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
        $('#form-status,#form-type').selectpicker();


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

    function filters() {
        $('#form-status,#form-type').change(function(event) {
            //触发dt的重新加载数据的方法
            api.ajax.reload();
        });
    }

    /**
     * 打开详情
     * @param  {[type]} oTable [description]
     * @param  {[type]} nTr    [description]
     * @return {[type]}        [description]
     */
    function fnFormatDetails(oTable, nTr) {
        // return moment.unix(e.update_time).format(FORMATER);
        var aData = oTable.fnGetData(nTr);
        var sOut = '<table cellpadding="5" cellspacing="0" border="0" width="100%">';
        sOut += '<tr><td>消息内容：:' + aData.content+ '</td></tr>';
        sOut += '</table>';
        return sOut;
    }
});
