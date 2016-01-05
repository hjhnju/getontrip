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
                    "url": "/admin/searchapi/list",
                    "type": "POST",
                    "data": function(d) {
                        //添加额外的参数传给服务器 
                        if ($("#search-id").val()) {
                            d.id = Number($.trim($("#search-id").val()));
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
                    "data": 'id'
                }, {
                    "data": 'name'
                }, {
                    "data": 'typename'
                }, {
                    "data": 'obj_id'
                }, {
                    "data": 'obj'
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
                        return '<button type="button" class="btn btn-success btn-xs addObj"  title="添加对象" data-toggle="tooltip" data-type="' + e.type + '"><i class="fa fa-buysellads"></i></button>' + '<button class="btn btn-danger btn-xs delLabel" title="删除" data-toggle="tooltip" > <i class="fa fa-remove"></i></button>';

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
                this.addObj();
                //绑定draw事件
                $('#editable').on('draw.dt', function() {
                    //工具提示框
                    $('[data-toggle="tooltip"]').tooltip();
                });

                //状态下拉列表 
                $('#form-status,#form-type').selectpicker();

                //删除标签
                $('#label_sortable').delegate('.close', 'click', function(event) {
                    if (confirm("确定删除搜索标签么？删除后不可恢复！") == false) {
                        return false;
                    }
                    var id = $(this).attr('data-id');
                    var publish = new Remoter('/admin/searchapi/delTag');
                    publish.remote({
                        tagId: id,
                    });
                    publish.on('success', function(data) { 
                       var $li = $('#label_sortable li[data-id="'+data+'"]'); 
                       $li.remove();
                    });
                }); 
                
              //标签
                $('.click').live('click', function(event) {
                	var id   = $(this).attr('data-id');
                	var type = $(this).attr('data-type');
                    $('#obj-id').val(id);
                    $('#obj-type').val(type);
                }); 

                //点击打开添加对象模态框
                $(".addObj").live('click', function(event) {
                    //var nRow = $(this).parents('tr')[0];
                    //var data = oTable.api().row(nRow).data();
                    //data.id   = $('#obj-id').attr('value');
                    //data.type = $('#obj-type').attr('value');
                    var data = new Object();
                    data.id   = Number($('#obj-id').attr('value'));
                    data.type = Number($('#obj-type').attr('value'));
                    
                    $('#type').val(data.type);
                    $('#id').val(data.id);
                    switch (data.type) {
                        case 1:
                            {
                                $('#add-city').hide();
                                $('#add-sight').show();
                                break;
                            }
                        case 2:
                            {
                                $('#add-city').show();
                                $('#add-sight').hide();
                                break;
                            }
                        default:
                            {
                                $('#add-city').show();
                                $('#add-sight').show();
                                break;
                            }
                    }
                    var listLabel = new Remoter('/admin/searchapi/listLabel');
                    listLabel.remote({
                        id: data.id
                    });                   
                    listLabel.on('success', function(data) {
                        data = data.data.length ? data.data[0]:!1;
                        if (!data) return;

                        $('#myModal input[name="sight_id"]').attr("checked", false);
                        $('#myModal input[name="city_id"]').attr("checked", false);

                        for (var i = 0; i < data.objs.length; i++) {
                            $('#myModal input[value="' + data.objs[i].id + '"]:checkbox').attr("checked", true);
                            $('#myModal input[value="' + data.objs[i].id + '"]:checkbox').parent().attr('class', 'checked');
                        };
                        $('#myModal').modal();
                    });

                });

                //上传图片，得到url
                $('#add-image').click(function(event) {
                    $('#uploadpicModal .modal-title').html('设置搜索推荐顶部图片');
                    $('#uploadpicModal').modal();
                });
                
                $('#myModal').on('hide.bs.modal', function () {
                	$('#myModal input[name="sight_id"]').attr("checked", false);
                    $('#myModal input[name="city_id"]').attr("checked", false);
                 });

                $("#upload-img").click(function(event) {
                    imageHtml = $('.fileupload-preview').html();
                    $.ajaxFileUpload({
                        url: '/upload/pic?filename=' + $('#form-filename').val(),
                        secureuri: false,
                        fileElementId: 'imageBtn',
                        dataType: 'json',
                        success: function(res, status) {
                            $('#image').val(res.data.image);
                            $('#imageView').html(imageHtml);
                            $('#imageView').removeClass('imageView');
                        },
                        error: function(data, status, e) {
                            alert(status.statusInfo);
                        }
                    })
                });

                //删除标签关联的对象
                $('#editable').delegate('button.delLabel', 'click', function(event) {
                    if (confirm("确定删除么？删除后不可恢复！") == false) {
                        return false;
                    }
                    var nRow = $(this).parents('tr')[0];
                    var data = oTable.api().row(nRow).data();
                    var del = new Remoter('/admin/searchapi/delLabel');
                    del.remote({
                        labelId: data.id,
                        objId: data.obj_id
                    });
                    del.on('success', function(data) {
                        //刷新当前页
                        oTable.fnRefresh();
                    });
                });

                //修改排序操作   
                $("#label_sortable").sortable({
                    //revert: true,
                    start: function(d, li) {
                        oldNum = $(li.item).index() + 1
                    },
                    stop: function(d, li) {
                        newNum = $(li.item).index() + 1
                        if (oldNum === newNum) {
                            return;
                        }
                        changeWeight($(li.item).attr('data-id'), newNum);
                    }
                });

                function changeWeight(id, to) {
                    $.ajax({
                        "url": "/admin/searchapi/changeWeight",
                        "data": {
                            id: id,
                            to: to
                        },
                        "type": "post",
                        "error": function(e) {
                            alert("服务器未正常响应，请重试");
                        },
                        "success": function(response) {
                            toastr.success('排序成功');
                        }
                    });
                }
            },
            addObj: function() {
                $.validator.setDefaults({
                    submitHandler: function(data) {
                        //序列化表单  
                        var param = $("#myModal #Form").serializeObject();
                        $.ajax({
                            "url": "/admin/searchapi/addLabel",
                            "data": param,
                            "type": "post",
                            "dataType": "json",
                            "error": function(e) {
                                alert("服务器未正常响应，请重试");
                            },
                            "success": function(response) {
                                if (response.status == 0) {
                                    toastr.success('保存成功');
                                    //手工关闭模态框
                                    $('#myModal').modal('hide');
                                    //刷新当前页
                                    oTable.fnRefresh();
                                } else {
                                    alert(response.statusInfo);
                                }
                            }
                        });

                    }
                });
                validations();



                /*
                  表单验证
                 */
                function validations() {
                    // validate signup form on keyup and submit
                    validate = $("#Form").validate({
                        rules: {
                            obj_id: "required"
                        },
                        messages: {
                            obj_id: "对象不能为空！"
                        }
                    });
                }
            }
        }

        /*
          过滤事件
         */
        var filter = function() {
            $('#form-status,#form-type').change(function(event) {
                //触发dt的重新加载数据的方法
                api.ajax.reload();
            });

            //选择标签
                $('#label_sortable').delegate('.click', 'click', function(event) {
                    var id = $(this).attr('data-id');
                    $("#search-id").val(id);
                    api.ajax.reload();
                });
        }


        /**
         * 打开详情
         * @param  {[type]} oTable [description]
         * @param  {[type]} nTr    [description]
         * @return {[type]}        [description]
         */
        var fnFormatDetails = function(oTable, nTr) {
            // return moment.unix(e.update_time).format(FORMATER);
            var aData = oTable.fnGetData(nTr);
            var sOut = '<table cellpadding="5" cellspacing="0" border="0" width="100%">';
            sOut += '<tr><td>消息内容：:' + aData.content + '</td></tr>';
            sOut += '</table>';
            return sOut;
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
