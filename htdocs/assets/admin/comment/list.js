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
        "ordering":false,
        "ajax": {
            "url": "/admin/Keywordapi/list",
            "type": "POST",
            "data": function(d) {
                //添加额外的参数传给服务器
                d.sight_id = '';
                if ($("#form-sight").attr('data-sight_id')) {
                    d.sight_id = Number($.trim($("#form-sight").attr('data-sight_id')));
                } 
                if ($("#form-status").val()) {
                    d.status = $.trim($("#form-status").val());
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
            "data": "sight_name"
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
            "data": function(e){
                if(e.status==2){
                   return '<span class="span-status" data-id="'+e.id+'"><i class="fa fa-2x fa-check color-check"></i></span>'
                }else if(e.status==1){
                   return '<span class="span-status" data-id="'+e.id+'"><i class="fa fa-2x fa-close color-uncheck"></i></span>'
                }else{
                    return '<span class="span-status" data-id="'+e.id+'">未知状态</span>';
                }
                
            }
        }, {
            "data": function(e) {
                return '<button class="btn btn-warning  btn-xs confirm" title="确认url" data-toggle="tooltip"><i class="fa fa-check-square-o"></i></button><a class="btn btn-success btn-xs edit" title="查看" data-toggle="tooltip" href="/admin/keyword/edit?action=view&id=' + e.id + '"><i class="fa fa-eye"></i></a><a class="btn btn-primary btn-xs edit" title="编辑" data-toggle="tooltip" href="/admin/keyword/edit?action=edit&id=' + e.id + '"><i class="fa fa-pencil"></i></a>' + '<button type="button" class="btn btn-danger btn-xs delete"  title="删除" data-toggle="tooltip"><i class="fa fa-trash-o "></i></button>';
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


    function bindEvents(){
          //绑定draw事件
        $('#editable').on('draw.dt', function() {
            //工具提示框
            $('[data-toggle="tooltip"]').tooltip();
        });

        //状态下拉列表 
        $('#form-status').selectpicker();
    }

    function filters(){
           $('#form-status').change(function(event) {
            //触发dt的重新加载数据的方法
            api.ajax.reload();
        });
    }
 });