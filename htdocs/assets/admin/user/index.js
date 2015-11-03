(function(window, document, undefined) {
    var FORMATER = 'YYYY-MM-DD HH:mm:ss';
    var oTable = $('#editable').dataTable({
        "serverSide": true, //分页，取数据等等的都放到服务端去
        "processing": true, //载入数据的时候是否显示“载入中”
        "pageLength": 10, //首次加载的数据条数  
        "searching": false,
        "ordering": false,
        "ajax": {
            "url": "/admin/userapi/list",
            "type": "POST",
            "data": function(d) {
                //添加额外的参数传给服务器
                d.params = {};
                if ($("#form-device_id").val()) {
                    d.params.device_id = $.trim($("#form-device_id").val());
                }
                if ($("#form-nick_name").val()) {
                    d.params.nick_name = $.trim($("#form-nick_name").val());
                }
                if ($("#form-city").val()) {
                    d.params.city_id = Number($.trim($("#form-city").attr('data-city_id')));
                }
                if ($('#form-sex').val()) {
                    d.params.sex = $('#form-sex').val();
                }
            }
        },
        "columnDefs": [{
            "targets": [],
            "visible": false,
            "searchable": false
        }],
        "columns": [{
            "data": "id"
        }, {
            "data": "device_id"
        }, {
            "data": "nick_name"
        }, {
            "data": "city"
        }, {
            "data": 'sex_name'
        }, {
            "data": function(e) {
                if (e.image) {
                    return '<a href="/pic/' + e.image + '" target="_blank"><img alt="" src="/pic/' + e.image.getNewImgByImg(80, 22, 'f') + '"/></a>';
                }
                return "未上传";
            }
        },  {
            "data": function(e) {
                if (e.accept_msg) {
                    return '开启';
                }
                return "关闭";
            }
        }, {
            "data": function(e) {
                if (e.create_time) {
                    return moment.unix(e.create_time).format(FORMATER);
                }
                return "-";
            }
        }, {
            "data": function(e) {
                if (e.logintime) {
                    return moment.unix(e.logintime).format(FORMATER);
                }
                return "-";
            }
        }, {
            "data": function(e) {
                return '<a href="#" class="btn btn-primary btn-xs" title="查看" data-toggle="tooltip"><i class="fa fa-eye"></i></a>';
            }
        }],
        "initComplete": function(setting, json) {

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

    /*
      过滤事件
     */
    function filter() {
        //输入框自动完成
        $('#form-city').typeahead({
            display: 'name',
            val: 'id',
            ajax: {
                url: '/admin/cityapi/getCityList',
                triggerLength: 1
            },
            itemSelected: function(item, val, text, element) {
                element.val(text);
                element.attr('data-city_id', val);
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

        //输入内容点击回车查询
        $("#form-device_id,#form-nick_name").keydown(function(event) {
            if (event.keyCode == 13) {
                api.ajax.reload();
            }
        });

        //性别下拉列表 
        $('#form-sex').selectpicker();
        $('#form-sex').change(function(event) {
            //触发dt的重新加载数据的方法
            api.ajax.reload();
        });

    }
}(window, document));
