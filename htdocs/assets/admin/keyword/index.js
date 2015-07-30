/*

 景点词条列表
  author:fyy
 */
$(document).ready(function() {
    var editBtn = '<a class="btn btn-primary btn-xs edit" title="编辑" data-toggle="tooltip"><i class="fa fa-pencil"></i></a>' + '<button type="button" class="btn btn-success btn-xs addKeyword"  title="删除" data-toggle="tooltip"><i class="fa fa-trash-o "></i></button>' ;
    var FORMATER = 'YYYY-MM-DD HH:mm:ss';
    var oTable = $('#editable').dataTable({
        "serverSide": true, //分页，取数据等等的都放到服务端去
        "processing": true, //载入数据的时候是否显示“载入中”
        "pageLength": 10, //首次加载的数据条数  
        "searching": false, //是否开启本地分页
        "ordering":false,
        "ajax": {
            "url": "/admin/Keywordapi/list",
            "type": "POST",
            "data": function(d) {
                //添加额外的参数传给服务器
                d.sight_id = '';
                if ($("#form-sight").val()) {
                    d.sight_id = Number($.trim($("#form-sight").attr('data-sight_id')));
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
            "data": "id"
        }, {
            "data": 'name'
        }, {
            "data": function(e) {
                if (e.url) {
                    return '<a href="' + e.url + '" target="_blank" title="' + e.url + '">' + (e.url.length > 20 ? e.url.substr(0, 20)+'...' : e.url) + '</a>';
                }
                return '暂无';
            }
        }, {
            "data": "sightName"
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
                return '<a class="btn btn-success btn-xs edit" title="查看" data-toggle="tooltip" href="/admin/keyword/edit?action=view&id=' + e.id + '"><i class="fa fa-eye"></i></a><a class="btn btn-primary btn-xs edit" title="编辑" data-toggle="tooltip" href="/admin/keyword/edit?action=edit&id=' + e.id + '"><i class="fa fa-pencil"></i></a>' + '<button type="button" class="btn btn-danger btn-xs delete"  title="删除" data-toggle="tooltip"><i class="fa fa-trash-o "></i></button>';
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
                "url": "/admin/Keywordsapi/del",
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

      
     
    }

    /*
      过滤事件
     */
    function filters() { 

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
       
        //状态下拉列表 
        $('#form-status').selectpicker();

      
    }

 

});
