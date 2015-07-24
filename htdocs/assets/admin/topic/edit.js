/*
  话题编辑  
  fyy 2015.7.21
 */
$(document).ready(function() {
    var validate = null;
    var sight_id_array = [];
    var tag_id_array = [];

    bindEvents();
    validations();




    /**
     * 绑定事件
     *  
     */
    function bindEvents() {
        //初始化编辑器
        $('#summernote').summernote({
            lang: "zh-CN",
            height: 300,
            toolbar: [
                //[groupname, [button list]] 
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['font', ['strikethrough']],
                ['fontsize', ['fontsize']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['height', ['height']],
                ['insert', ['hr', 'link', 'picture']],
                ['view', ['fullscreen', 'codeview']]
            ]
        });


        //表单提交事件
        $.validator.setDefaults({
            submitHandler: function(data) {
                //序列化表单  
                var param = $("#Form").serializeObject();

                //特殊处理景点和标签 
                sight_id_array = [];
                $('#sight_alert span button').each(function() {
                    sight_id_array.push(Number($(this).attr('data-id')));
                });

                if (sight_id_array.length == 0) {
                    toastr.warning('至少选择一个景点吧');
                    return false;
                }

                tag_id_array = [];
                $('#tags option:selected').each(function() {
                    tag_id_array.push(Number($(this).val()));
                });
                param.tags = tag_id_array;
                param.sights = sight_id_array;
                param.content = $("#summernote").code();
                //已发布的状态
                param.status = 5;

                var url;
                if (!$('#id').val()) {
                    url = "/admin/topicapi/add";
                } else {
                    url = "/admin/topicapi/save"
                }
                $.ajax({
                    "url": url,
                    "data": param,
                    "type": "post",
                    "dataType": "json",
                    "error": function(e) {
                        alert("服务器未正常响应，请重试");
                    },
                    "success": function(response) {
                        if (response.status == 0) {
                            toastr.success('保存成功');
                            if (url.indexOf('add') >= 0) {
                                resetForm();
                            } else {
                                window.location.reload();
                            }

                        }
                    }
                });

            }
        });

        //景点输入框自动完成
        $('#sight_name').typeahead({
            display: 'name',
            val: 'id',
            ajax: {
                url: '/admin/sightapi/getSightList',
                triggerLength: 1
            },
            itemSelected: function(item, val, text) {
                $("#sight_name").val('');
                sight_id_array.push(Number(val));
                //添加到右侧选框中 
                $('#sight_alert').append('<span class="badge badge-sm label-danger" role="badge">' + text + '<button type="button" class="close" data-id="' + val + '"><span class="fa fa-remove"></span></button></span>');
            }
        });

        //景点框删除景点
        $('#sight_alert').delegate('.close', 'click', function(event) {
            var val = Number($(this).attr('data-id'));
            sight_id_array.splice($.inArray(val, sight_id_array), 1);
            $(this).parent().remove();
        });

        //标签选择
        $('#tags').multiSelect({
            selectableHeader: '<span class="label label-primary">标签库</span>',
            selectionHeader: '<span class="label label-success">已选标签</span>'
        });

        //上传图片，得到url
        $("#upload-img").click(function(event) {
            $.ajaxFileUpload({
                url: '/upload/pic',
                secureuri: false,
                fileElementId: 'imageBtn',
                dataType: 'json',
                success: function(res, status) { //当文件上传成功后，需要向数据库中插入数据
                    $('#image').val(res.data.hash);
                    $('#imageView').html('<img src="' + res.data.url + '" style="width:186px;height:140px;"/>');
                    $('#imageView').removeClass('imageView');
                },
                error: function(data, status, e) {
                    alert(status.statusInfo);
                }
            })
        });

        //搜索来源下拉列表 
        $('#form-from').selectpicker();

        //定位地图模态框
        $('#position').click(function(e) {
            //打开模态框
            $('#myModal').modal({
                remote: '/admin/utils/map'
            });
        });

        // 模态框从远端的数据源加载完数据之后触发该事件
        $('#myModal').on('loaded.bs.modal', function(e) {
            $("#txtSearch").val($.trim($("#name").val()));
            $("#cityName").val($.trim($("#city_name").val()));
            $('#myModal .btn-search').click();
        });

        //模态框 点击确定之后立即触发该事件。
        $('#myModal').delegate('.btn-submit', 'click', function(event) {
            var valXY = $.trim($("#txtCoordinate").val());
            if (!valXY) {
                alert('还没有选定坐标呢');
                return;
            }
            var arrayXy = valXY.split(',');
            $("#x").val(arrayXy[0]);
            $("#y").val(arrayXy[1]);
            $("#xy").val(valXY);


            //手工关闭模态框
            $('#myModal').modal('hide');
        });


    }

    /*
       表单验证
     */
    function validations() {
        // validate signup form on keyup and submit
        validate = $("#Form").validate({
            rules: {
                title: "required"
            },
            messages: {
                title: "这可是主标题，不能为空呀！"
            }
        });
    }

    //重置表单
    function resetForm() {
        $("button[name='reset']").click();
        $('#imageView').html('');
        $('#imageView').addClass('imageView');
        sight_id_array = [];
        tag_id_array = [];
        $('#sight_alert').html('');
        $('#ms-tags .ms-selection li').click();

    }
});
