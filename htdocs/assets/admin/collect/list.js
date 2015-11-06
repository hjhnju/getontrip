/*

 用户收藏列表
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
                    "url": "/admin/Collectapi/list",
                    "type": "POST",
                    "data": function(d) {
                        d.params = {};
                        //添加额外的参数传给服务器 
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
                    "targets": [0],
                    "width": 20
                }],
                "columns": [{
                    "data": "id"
                }, {
                    "data": function(e){
                        if (e.obj_id) {
                           return '<a href="/admin/'+e.obj_table+'/edit?action=view&id='+e.obj_id+'" target="_blank">'+e.obj_title+'</a>';
                        }
                    }
                }, {
                    "data": 'type_name'
                }, {
                    "data": 'user_id'
                }, {
                    "data": function(e) {
                        if (e.create_time) {
                            return moment.unix(e.create_time).format(FORMATER);
                        }
                        return "空";
                    }
                }, {
                    "data": function(e) {
                        return '';
                        //return '<button type="button" class="btn btn-danger btn-xs delete"  title="删除" data-toggle="tooltip"><i class="fa fa-trash-o "></i></button>';
                    }
                }] 
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
                $('#form-type').selectpicker();



                //发布操作
                $('#editable button.publish,#editable button.cel-publish').live('click', function(e) {
                    e.preventDefault();
                    if ($(this).hasClass('publish')) {
                        action = 'PUBLISHED';
                    } else {
                        action = 'NOTPUBLISHED';
                    }
                    var delcollect = new Remoter('/admin/Collectapi/changeStatus');
                    delCollect.remote({
                        id: $(this).attr('data-id'),
                        action: action
                    });
                    delCollect.on('success', function(data) {
                        //刷新当前页
                        oTable.fnRefresh();
                    });
                    delCollect.on('fail', function(data) {
                        alert(data);
                    });
                });
            }
        }

        /*
               过滤事件
           */
        var filter = function() {
            $('#form-type').change(function(event) {
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
