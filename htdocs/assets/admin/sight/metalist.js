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
                        if (e.city_id) {
                            return '已绑定'+e.city_id;
                        }
                        return '未绑定';
                    }
                }, {
                    "data": function(e) {
                        return '';
                        //return '<a class="btn btn-warning btn-xs" title="列表" data-toggle="tooltip"  target="_blank"  href="/admin/video/list?city_id=' + e.id + '">列表</a>';

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

                //类型说明弹出框 
                $('#from_detail-help').popover({
                    //trigger: 'hover',
                    placement: 'top',
                    html: true,
                    title: '类型种类：',
                    content: '1: "城市", 2: "古镇", 3: "乡村", 4: "海边", 5: "沙漠", 6: "山峰", 7: "峡谷", 8: "冰川", 9: "湖泊", 10: "河流", 11: "温泉", 12: "瀑布", 13: "草原", 14: "湿地", 15: "自然保护区",'
                             +'16: "公园", 17: "展馆", 18: "历史建筑", 19: "现代建筑", 20: "历史遗址", 21: "宗教场所", 22: "观景台", 23: "陵墓", 24: "学校", 25: "故居", 26: "纪念碑", 27: "其他", 28: "购物娱乐", 29: "休闲度假"'
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
