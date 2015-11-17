/*

 搜索标签列表
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
                    "url": "/admin/searchapi/wordList",
                    "type": "POST",
                    "data": function(d) {
                        //添加额外的参数传给服务器 
                        d.params={};
                        if ($("#form-status").val()) {
                            d.params.status = $.trim($("#form-status").val());
                        }
                    }
                },
                "columnDefs": [],
                "columns": [{
                    "data": 'query'
                }, {
                    "data": function(e) {
                        if (e.status == 1) {
                            return e.statusName + '<i class="fa fa-2x fa-clock-o color-uncheck"></i>';
                        } else if (e.status == 2) {
                            return e.statusName + '<i class="fa fa-2x fa-check color-check"></i>';
                        } else if (e.status == 3) {
                            return e.statusName + '<i class="fa fa-2x fa-close color-uncheck"></i>';
                        } else {
                            return '未知状态';
                        }
                    }
                },{
                    "data": 'num'
                }, {
                    "data": function(e) {
                        return '<button class="btn btn-primary btn-xs status" data-action="AUDITPASS"  title="通过审核" data-toggle="tooltip" >通过审核</button>' + '<button class="btn btn-danger btn-xs status" data-action="AUDITFAILED" title="未通过审核" data-toggle="tooltip" >未通过审核</button>';

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
 
                //修改状态
                $('#editable').delegate('button.status', 'click', function(event) {
                    var nRow = $(this).parents('tr')[0];
                    var data = oTable.api().row(nRow).data();
                    var change = new Remoter('/admin/searchapi/changeWordStatus');
                    change.remote({
                        word: data.query,
                        action: $(this).attr('data-action')
                    });
                    change.on('success', function(data) {
                        //刷新当前页
                        oTable.fnRefresh();
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
