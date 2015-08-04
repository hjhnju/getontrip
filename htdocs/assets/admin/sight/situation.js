/*
   景点编辑情况
   author:fyy
 */
$(document).ready(function() {
    var oTable = $('#editable').dataTable({
        "serverSide": true, //分页，取数据等等的都放到服务端去
        "processing": true, //载入数据的时候是否显示“载入中”
        "pageLength": 10, //首次加载的数据条数  
        "searching": false, //是否开启本地分页
        "ordering": false,
        "ajax": {
            "url": "/admin/sightapi/situationList",
            "type": "POST",
            "data": function(d) {
                //添加额外的参数传给服务器
                d.params = {};
                if ($("#form-sight").attr('data-sight_id')) {
                    d.params.id = $("#form-sight").attr('data-sight_id');
                }
            }
        },
        "columnDefs": [{
            "targets": [0, 1],
            "visible": false,
            "searchable": false
        }],
        "columns": [{
            "data": "id"
        }, {
            "data": 'city_id'
        }, {
            "data": "name"
        }, {
            "data": 'city_name'
        }, {
            "data": function(e) {
                return '共' + e.topicCount + '个' + '<a class="btn btn-success btn-xs" title="创建" data-toggle="tooltip" target="_blank" href="/admin/topic/edit?action=add&sight_id=' + e.id + '">创建</a><a class="btn btn-primary btn-xs" title="筛选" data-toggle="tooltip"  target="_blank"  href="/admin/topic/filter?sight_id=' + e.id + '">筛选</a><a class="btn btn-warning btn-xs" title="列表" data-toggle="tooltip"  target="_blank"  href="/admin/topic/list?sight_id=' + e.id + '">列表</a>';
            }
        }, {
            "data": function(e) {
                if (e.keywordlist) {
                    for (var i = 0; i < e.keywordlist.length; i++) {

                    }
                }
                return '共' + e.keywordCount + '个' + '<a class="btn btn-success btn-xs" title="创建" data-toggle="tooltip" target="_blank" href="/admin/keyword/edit?action=add&sight_id=' + e.id + '">创建</a><a class="btn btn-warning btn-xs" title="列表" data-toggle="tooltip"  target="_blank"  href="/admin/keyword/list?sight_id=' + e.id + '">列表</a>';

            }
        }],
        "initComplete": function(setting, json) {
            //工具提示框
            //$('[data-toggle="tooltip"]').tooltip();
        }
    });

    var api = oTable.api();
    bindEvents();
    filter();


    /*
     绑定事件
    */
    function bindEvents() {
        //绑定draw事件
        $('#editable').on('draw.dt', function() {
            //工具提示框
            $('[data-toggle="tooltip"]').tooltip();
        });


    }



    function filter() {
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
        /*        $('#form-city').typeahead({
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
                });*/
    }
  

});
