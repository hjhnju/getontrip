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
                    "url": "/admin/sightapi/situationList",
                    "type": "POST",
                    "data": function(d) {
                        //添加额外的参数传给服务器
                        d.params = {};
                        if ($("#form-sight").attr('data-sight_id')) {
                            d.params.id = $("#form-sight").attr('data-sight_id');
                        }
                        if ($("#form-city").attr('data-city_id')) {
                            d.params.city_id = $("#form-city").attr('data-city_id');
                        }
                        if ($('#form-user_id').attr("checked")) {
                            d.params.create_user = $('#form-user_id').val();
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
                }, {
                    "targets": [1, 2, 3],
                    "width": 60
                }, {
                    "targets": [5],
                    "width": 130
                }, {
                    "targets": [6, 7],
                    "width": 90
                }],
                "columns": [{
                    "data": "id"
                }, {
                    "data": "name"
                }, {
                    "data": function(e) {
                        if (e.status == 1) {
                            return '未发布<button type="button" class="btn btn-primary btn-xs publish" title="发布" data-toggle="tooltip" ><i class="fa fa-check-square-o"></i></button>';
                        } else {
                            return '已发布<button type="button" class="btn btn-warning btn-xs cel-publish" title="取消发布" data-toggle="tooltip" ><i class="fa fa-close"></i></button>';
                        }
                    }
                }, {
                    "data": function(e) {
                        return e.city_name + '/' + e.city_id;
                    }
                }, {
                    "data": function(e) {
                        var tagStr = '';
                        var classifyTag = e.tagList.classifyTag;
                        var generalTag = e.tagList.generalTag;
                        var normalTag = e.tagList.normalTag;

                        for (var i = 0; i < classifyTag.length; i++) {
                            tagStr = tagStr + '<span class="label label-success">' + classifyTag[i].name + '(' + classifyTag[i].topic_num + ')</span>';
                        };
                        for (var i = 0; i < generalTag.length; i++) {
                            tagStr = tagStr + '<span class="label label-warning">' + generalTag[i].name + '(' + generalTag[i].topic_num + ')</span>';
                        };
                        for (var i = 0; i < normalTag.length; i++) {
                            tagStr = tagStr + '<span class="label label-default">' + normalTag[i].name + '(' + normalTag[i].topic_num + ')</span>';
                        };
                        return tagStr + '<button class="btn btn-warning btn-xs add-generalTag" title="添加通用标签" data-toggle="tooltip"><i class="fa fa-plus"></i></button>';
                    }
                }, {
                    "data": function(e) {
                        return '共' + e.topicCount + '个<br/>' + '<a class="btn btn-success btn-xs" title="创建" data-toggle="tooltip" target="_blank" href="/admin/topic/edit?action=add&sight_id=' + e.id + '">创建</a><a class="btn btn-primary btn-xs" title="筛选" data-toggle="tooltip"  target="_blank"  href="/admin/topic/filter?sight_id=' + e.id + '">筛选</a><a class="btn btn-warning btn-xs" title="列表" data-toggle="tooltip"  target="_blank"  href="/admin/topic/list?sight_id=' + e.id + '">列表</a>';
                    }
                }, {
                    "data": function(e) {
                        if (e.keywordlist) {
                            for (var i = 0; i < e.keywordlist.length; i++) {

                            }
                        }
                        return '共' + e.keywordCount + '个<br/>' + '<a class="btn btn-success btn-xs" title="创建" data-toggle="tooltip" target="_blank" href="/admin/keyword/edit?action=add&sight_id=' + e.id + '">创建</a><a class="btn btn-warning btn-xs" title="列表" data-toggle="tooltip"  target="_blank"  href="/admin/keyword/list?sight_id=' + e.id + '">列表</a>';

                    }
                }, {
                    "data": function(e) {
                        return '共' + e.book_num + '个<br/>' + '<a class="btn btn-warning btn-xs" title="列表" data-toggle="tooltip"  target="_blank"  href="/admin/book/list?sight_id=' + e.id + '">列表</a>';

                    }
                }, {
                    "data": function(e) {
                        return '共' + e.video_num + '个<br/>' + '<a class="btn btn-warning btn-xs" title="列表" data-toggle="tooltip"  target="_blank"  href="/admin/video/list?sight_id=' + e.id + '">列表</a>';

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
                this.addGeneralTag();
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
                //点击打开通用标签列表框
                $('#editable button.add-generalTag').live('click', function(e) {
                    var nRow = $(this).parents('tr')[0];
                    var data = oTable.api().row(nRow).data();
                    var list = new Remoter('/admin/tagapi/getTagBySight');
                    $('#sight-id').val(data.id);
                    $('#sight-status').val(data.status);
                     
                    list.remote({
                        sightId: data.id
                    });
                    list.on('success', function(data) {
                        // var data=data.data;
                        var html = '';
                        for (var i = 0; i < data.length; i++) {
                            var checked = data[i].selected ? 'checked="checked"' : '';
                            html = html + '<label class="checkbox-inline">';
                            html = html + '   <input type="checkbox" name="tags" data-name="form-generaltag" id="" value="' + data[i].id + '"' + checked + ' />' + data[i].name;
                            html = html + '</label>';
                        };
                        $('#generalTag-checkbox').html(html);
                        //绑定Uniform
                        Metronic.initUniform($('input[data-name="form-generaltag"]'));

                        $('#generalTagModal').modal();
                    });
                });
                //添加通用标签操作
                $('#generalTagModal button.add-generalTag').live('click', function(e) {
                    e.preventDefault();
                    var nRow = $(this).parents('tr')[0];
                    var data = oTable.api().row(nRow).data();
                    var action;

                    /* var publish = new Remoter('/admin/sightapi/save');
                     publish.remote({
                         id: data.id,
                         tags: action
                     });
                     publish.on('success', function(data) {
                         //刷新当前页
                         oTable.fnRefresh();
                     });*/

                });
            },
            addGeneralTag: function() {
                $.validator.setDefaults({
                    submitHandler: function(data) {
                        //序列化表单  
                        var param = $("#generalTagModal #Form").serializeObject();
                        for (var i = 0; i < param.tags.length; i++) {
                            if (!$.isArray(param.tags)) {
                                param.tags=[(parseInt(param.tags))];
                                break;
                            };
                            param.tags[i]=parseInt(param.tags[i]);
                        };
                        $.ajax({
                            "url": "/admin/sightapi/save",
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
                                    $('#generalTagModal').modal('hide');
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

                        },
                        messages: {

                        }
                    });
                }
            }
        }

        /*
             过滤事件
        */
        var filter = function() {
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
