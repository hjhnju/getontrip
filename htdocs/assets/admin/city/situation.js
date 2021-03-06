/*
  城市编辑情况
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
                    "url": "/admin/cityapi/situationList",
                    "type": "POST",
                    "data": function(d) {
                        //添加额外的参数传给服务器
                        if ($("#form-city").attr('data-city_id')) {
                            d.city_id = $("#form-city").attr('data-city_id');
                        }
                        if ($('#form-user_id').attr("checked")) {
                            d.create_user = $('#form-user_id').val();
                        }
                    }
                },
                "columnDefs": [{
                    "targets": [0],
                    "visible": false,
                    "searchable": false
                }],
                "columns": [{
                    "data": 'id'
                }, {
                    "data": "name"
                }, {
                    "data": function(e) {
                        if (e.image) {
                            return '<a href="/pic/' + e.image + '" target="_blank"><img alt="" src="' + e.image.getNewUrlByUrl(80, 22, 'f') + '"/></a>';
                        }
                        return '暂无';
                    }
                }, {
                    "data": 'info'
                }, {
                    "data": function(e) {
                        return '<button    class="btn btn-warning btn-xs cel-publish" title="关闭" data-toggle="tooltip">关闭</button> <a href="/admin/sight/situation?city_id=' + e.id + '"  target="_blank" class="btn btn-success btn-xs" title="景点总览" data-toggle="tooltip">景点总览</button><a class="btn btn-primary btn-xs edit" title="编辑" data-toggle="tooltip" href="/admin/city/edit?action=edit&id=' + e.id + '" target="_blank">编辑</a></button>';
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

                //开通、关闭操作
                $('#editable button.publish,#editable button.cel-publish').live('click', function(e) {
                    e.preventDefault();
                    var nRow = $(this).parents('tr')[0];
                    var data = oTable.api().row(nRow).data();
                    var action;
                    if ($(this).hasClass('publish')) {
                        action = 'PUBLISHED';
                    } else {
                        action = 'NOTPUBLISHED';
                    }
                    var publish = new Remoter('/admin/cityapi/publish');
                    publish.remote({
                        id: data.id,
                        action: action
                    });
                    publish.on('success', function(data) {
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

            //景点框后的清除按钮，清除所选的景点
            $('#clear-sight').click(function(event) {
                $("#form-sight").val('');
                $("#form-sight").attr('data-sight_id', '');
                //触发dt的重新加载数据的方法
                api.ajax.reload();
            });

            //城市输入框自动完成
            $('#form-city').typeahead({
                display: 'name',
                val: 'id',
                ajax: {
                    url: '/admin/cityapi/getCityList',
                    triggerLength: 1
                },
                itemSelected: function(item, val, text) {
                    $("#form-city").val(text);
                    $("#form-city").attr('data-city_id', val);
                    //触发dt的重新加载数据的方法
                    api.ajax.reload();
                }
            });

            //城市框后的清除按钮，清除所选的景点
            $('#clear-city').click(function(event) {
                $("#form-city").val('');
                $("#form-city").attr('data-city_id', '');
                //触发dt的重新加载数据的方法
                api.ajax.reload();
            });

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
