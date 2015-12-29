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
                            d.params.city_id = $("#form-city").attr('data-city_id');
                        }
                        if ($("#form-type").val()) {
                            d.params.type = $("#form-type").val();
                        }
                        if ($('#form-is_china').val()) {
                            d.params.is_china = $('#form-is_china').val();
                        }
                        if ($("#form-status").val()) {
                            d.params.status = $.trim($("#form-status").val());
                        } 
                        if ($("#form-level").val()) {
                            d.params.level = $.trim($("#form-level").val());
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
                    "data": function(e) {
                        if (e.is_landscape) {
                            return '是';
                        }
                        return '否';
                    }
                }, {
                    "data": "continent"
                }, {
                    "data": "country"
                }, {
                    "data": function (e) {
                        if (e.province) {
                            return e.province;
                        }
                        return '--'
                    }
                }, {
                    "data":  function(e) {
                        if (e.city_id) {
                            return '<a data-city_id="'+e.city_id+'" class="changeCity" data-city_id="'+e.city_id+'">'+e.city+'</a>';
                        } 
                    }
                },  {
                    "data": "weight"
                },  {
                    "data": "status_name"
                }, {
                    "data": function(e) {
                        if (e.status==0) {
                           return '<button class="btn btn-warning btn-xs save" title="加入景点库" data-toggle="tooltip"  data-id=' + e.id + '" data-action="CONFIRMED">加入景点库</button>'
                                  +'<button class="btn btn-warning btn-xs save" title="加入推荐" data-toggle="tooltip"  data-id=' + e.id + '" data-action="NEEDCONFIRM">加入推荐</button>';
                        
                        } else if (e.status==1) {
                           return '<button class="btn btn-warning btn-xs save" title="加入景点库" data-toggle="tooltip"  data-id=' + e.id + '" data-action="CONFIRMED">加入景点库</button>'
                                + '<button class="btn btn-warning btn-xs save" title="取消推荐" data-toggle="tooltip"  data-id=' + e.id + '" data-action="NOTNEED">取消推荐</button>';
                        
                        }else {
                           return '';
                        
                        }
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
                    placement: 'bottom',
                    html: true,
                    title: '可选类型：',
                    content: '<span style="width:250px;display: block;">1: "城市", 2: "古镇", 3: "乡村", 4: "海边", 5: "沙漠", 6: "山峰", 7: "峡谷", 8: "冰川", 9: "湖泊", 10: "河流", 11: "温泉", 12: "瀑布", 13: "草原", 14: "湿地", 15: "自然保护区",'
                             +'16: "公园", 17: "展馆", 18: "历史建筑", 19: "现代建筑", 20: "历史遗址", 21: "宗教场所", 22: "观景台", 23: "陵墓", 24: "学校", 25: "故居", 26: "纪念碑", 27: "其他", 28: "购物娱乐", 29: "休闲度假"</span>'
                });

                //发布操作
                $('#editable button.save').live('click', function(e) {
                    e.preventDefault();
                    var nRow = $(this).parents('tr')[0];
                    var data = oTable.api().row(nRow).data();
                    action = $(this).attr('data-action');

                    var publish = new Remoter('/admin/sightapi/addToSight');
                    publish.remote({
                        id: data.id,
                        action: action
                    });
                    publish.on('success', function(data) {
                        if (data.bizError) {
                            toastr.warning(data.statusInfo);
                        }else{ 
                            toastr.success('保存成功');
                        }
                        //刷新当前页
                        oTable.fnRefresh();
                        
                    });

                });

                $('#editable a.changeCity').live('click', function(e) {
                     e.preventDefault();
                     $('#form-city').val($(this).text());
                     $('#form-city').attr('data-city_id',$(this).attr('data-city_id'));
                     api.ajax.reload();
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

            //景点框后的清除按钮，清除所选的景点
            $('#clear-city').click(function(event) {
                $("#form-city").val('');
                $("#form-city").attr('data-city_id', '');
                //触发dt的重新加载数据的方法
                api.ajax.reload();
            });
 

            $('#form-status,#form-level').change(function(event) {
                //触发dt的重新加载数据的方法
                api.ajax.reload();
            });

            $('#form-is_china').change(function(event) {
                if ($(this).val()=='0') {
                   //国外隐藏级别
                    api.columns([2]).visible(false);
                    api.columns([4,5]).visible(true, true);
                }else{
                    //国内隐藏大洲，国家 
                    api.columns([2]).visible(true);
                    api.columns([4,5]).visible(false, false);
                }
                api.columns.adjust().draw( false ); // adjust column sizing and redraw

                //触发dt的重新加载数据的方法
                api.ajax.reload();
            });

            //状态下拉列表 
            $('#form-status,#form-is_china,#form-level').selectpicker();

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
