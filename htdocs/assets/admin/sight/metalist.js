/*
   景点编辑情况
   author:fyy
 */
$(document).ready(function() {
    var List = function() {
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
                    "url": "/admin/sightapi/metaList",
                    "type": "POST",
                    "data": function(d) {
                        //添加额外的参数传给服务器
                        d.params = {};
                        if ($("#form-name").val()) {
                            d.params.name = $("#form-name").val();
                        }
                        if ($("#form-province").val()) {
                            d.params.province = $("#form-province").val();
                        }
                        if ($("#form-city").val()) {
                            d.params.city = $("#form-city").val();
                        }
                        if ($("#form-type").val()) {
                            d.params.type = $("#form-type").val();
                        }
                        if ($('#form-is_china').attr("checked")) {
                            d.params.is_china = $('#form-is_china').val();
                        } 
                    }
                },
                "columnDefs": [{
                    "targets": [],
                    "visible": false,
                    "searchable": false
                }, {
                    "targets": [0],
                    "width": 30
                }],
                "columns": [{
                    "data": "id"
                }, {
                    "data": "name"
                }, {
                    "data": "level"
                }, {
                    "data": "type"
                }, {
                    "data": "continent"
                }, {
                    "data": "country"
                }, {
                    "data": "province"
                }, {
                    "data": "city"
                }, {
                    "data": "region"
                }, {
                    "data": function(e) {
                        if (e.sight_id) {
                            return '已绑定'+e.sight_id;
                        }
                        return '未绑定';
                    }
                }, {
                    "data": function(e) {
                        return '';
                        //return '<a class="btn btn-warning btn-xs" title="列表" data-toggle="tooltip"  target="_blank"  href="/admin/video/list?sight_id=' + e.id + '">列表</a>';

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
                this.initEvents();
            },
            initEvents: function() {
                //绑定draw事件
                $('#editable').on('draw.dt', function() {
                    //工具提示框
                    $('[data-toggle="tooltip"]').tooltip();
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
                        action = 'PUBLISHED';
                    } else {
                        action = 'NOTPUBLISHED';
                    }
                    var publish = new Remoter('/admin/sightapi/publish');
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
            //输入内容点击回车查询
            $("#form-name,#form-province,#form-city,#form-type").keydown(function(event) {
                if (event.keyCode == 13) {
                    api.ajax.reload();
                }
            }); 

            //只看国内的
            $('#form-is_china').click(function(event) {
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
