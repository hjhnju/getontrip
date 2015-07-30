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
                //处理来源
                var from = "";
                if ($('input[data-name="form-from"]:checked').val() == "weixin") {
                    from = $('#weixin-from_id').val();
                    if (!from) {
                        toastr.warning('请填写微信公众号！');
                        return false;
                    }
                } else {
                    from = $('input[data-name="form-from"]:checked').attr('data-id');
                }
                param.tags = tag_id_array;
                param.sights = sight_id_array;
                param.from =from;
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
                ['view', ['codeview']]
            ],
            onInit: function() {
                $('#summernote').code($('#content-text').html());
            },
            onImageUpload: function(files, editor, $editable) {
                sendFile(files[0], editor, $editable);
            },
            onMediaDelete: function(files, editor, $editable) {
                if (confirm("会从服务器删除图片，确定删除么 ?") == false) {
                    return false;
                }
                //删除图片
                deleteImage(files[0], editor, $editable);
            },
            onChange: function(characters, editor, $editable) {
               //alert(characters);
                   /* if ($(characters).is('img') || $(characters).find('img').is('img')) {
                        deleteImage(characters, editor, $editable);
                    } */
            }
          /*  ,
            onPaste: function(event) { 
               var layoutInfo = $.summernote.core.dom.makeLayoutInfo(event.currentTarget || event.target);
               var clipboardData = event.originalEvent.clipboardData;
               var content = clipboardData.getData(clipboardData.types[0]);
               layoutInfo.holder().summernote('pasteHTML', content);
               return;
            }*/
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

         //微信公众号自动完成 
        $('#weixin-from').typeahead({
            display: 'name',
            val: 'id',
            ajax: {
                url: '/admin/sourceapi/getSourceList',
                triggerLength: 1
            },
            itemSelected: function(item, val, text) {
                $("#weixin-from").val(text);
                $("#weixin-from_id").val(val);
            }
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
        // $('#form-from').selectpicker();
        //选择不同的搜索来源
        $('#Form').delegate('input[data-name="form-from"]', 'click touchend', function(event) {
            $('input[data-name="form-from"]').attr('checked', false);
            $(this).attr('checked', 'ture');
            if ($(this).val() == "weixin") {
                $('#weixin-from-input').show();
            } else {
                $('#weixin-from-input').hide();
            }
        });

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

    /**
     * 上传图片
     * @param  {[type]} file      [description]
     * @param  {[type]} editor    [description]
     * @param  {[type]} $editable [description]
     * @return {[type]}           [description]
     */
    function sendFile(file, editor, $editable) {
        $(".note-toolbar.btn-toolbar").append('正在上传图片');
        var filename = false;
        try {
            filename = file['name'];
        } catch (e) {
            filename = false;
        }
        if (!filename) {
            $(".note-alarm").remove();
        }
        //以上防止在图片在编辑器内拖拽引发第二次上传导致的提示错误 
        // $('#summernote').summernote('editor.insertImage', 'http://res.cloudinary.com/demo/image/upload/butterfly.jpg'); 
        $.ajaxFileUpload({
            url: '/upload/pic',
            secureuri: false,
            fileElementId: 'note-image-input',
            dataType: 'json',
            success: function(res, status) { //当文件上传成功后，需要向数据库中插入数据
                //把图片放到编辑框中。editor.insertImage 是参数，写死。后面的http是网上的图片资源路径。  
                //网上很多就是这一步出错。  
                $('#summernote').summernote('editor.insertImage', res.data.url);
                $('img[src="' + res.data.url + '"]').attr('data-hash', res.data.hash);
                toastr.success('图片上传成功！');
            },
            error: function(data, status, e) {
                alert("服务器未正常响应，请重试");
            }
        })
    }
    /**
     * 删除图片
     * @return {[type]} [description]
     */
    function deleteImage(file, editor, $editable) {
        var data = {
            hash: $(file).attr("data-hash")
        };
        $.ajax({
            "url": '/upload/delpic',
            "data": data,
            "type": "post",
            "dataType": "json",
            "error": function(e) {
                alert("服务器未正常响应，请重试");
            },
            "success": function(response) {
                if (response.status == 0) {
                    toastr.success('删除成功');
                }
            }
        });
    }
});
