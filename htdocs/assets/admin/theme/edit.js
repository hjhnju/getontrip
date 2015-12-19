/*
  主题编辑  
  fyy 2015.7.21
 */
$(document).ready(function() {
    var validate = null;
    var landscape_id_array = [];
    var tag_id_array = [];
    var status = 1;
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
                ['para', ['paragraph']],
                ['style', ['bold', 'clear']],
                ['insert', ['hr', 'link', 'picture']],
                ['view', ['codeview']]
            ],
            onInit: function() {
                $('#summernote').code($('#content-text').html());
            },
            onImageUpload: function(files) {
                sendFile(files[0]);
            },
            onMediaDelete: function(files) {
                var file = files[0];
                if ($(file).attr('src').indexOf('/pic/')>=0) {
                    if (confirm("会从服务器删除图片，确定删除么 ?") == false) {
                        return false;
                    }
                    //删除图片
                    deleteImage(files);
                }
            },
            onChange: function(characters, editor, $editable) {
                localStorage.themeContent = $('#summernote').code();
                if ($('#id').val()) {
                    $(window).bind('beforeunload', function() {
                        if (localStorage.themeContent) {
                            return '等等!!您输入的正文内容尚未保存！!';
                        }
                    });

                }
            }
        });

        landscape_id_array = [];
        $('#landscape_alert span button').each(function() {
            landscape_id_array.push(Number($(this).attr('data-id')));
        });

        //景观输入框自动完成
        $('#landscape_name').typeahead({
            display: 'name',
            val: 'id',
            ajax: {
                url: '/admin/landscapeapi/getLandscapeList',
                triggerLength: 1
            },
            itemSelected: function(item, val, text) {
                $("#landscape_name").val('');
                //先判断是否已经选择
                var valnum = Number(val);
                if (!landscape_id_array.in_array(valnum)) {
                    landscape_id_array.push(Number(val));
                    //添加到右侧选框中 
                    $('#landscape_alert').append('<span class="badge badge-sm label-danger" role="badge">' + text + '<button type="button" class="close" data-id="' + val + '"><span class="fa fa-remove"></span></button></span>');
                }
            }
        });

        //景观框删除景观
        $('#landscape_alert').delegate('.close', 'click', function(event) {
            var val = Number($(this).attr('data-id'));
            landscape_id_array.splice($.inArray(val, landscape_id_array), 1);
            $(this).parent().remove();
        });

        //上传图片，得到url
        $("#upload-img").click(function(event) {
            $.ajaxFileUpload({
                url: '/upload/pic',
                secureuri: false,
                fileElementId: 'imageBtn',
                dataType: 'json',
                success: function(res, status) { //当文件上传成功后，需要向数据库中插入数据
                    $('#image').val(res.data.image);
                    $('#imageView').html('<img src="/pic/' + res.data.image.getNewImgByImg(190, 140, 'f') + '"  alt=""/>');
                    $('#imageView').removeClass('imageView');
                    $('#crop-img').removeClass('hidden');

                    localStorage.image = res.data.image;
                },
                error: function(data, status, e) {
                    alert(status.statusInfo);
                }
            })
        });

        //确保图片上传后存上
        $(window).bind('beforeunload', function() {
            if (localStorage.image) {
                return '等等!!你刚刚上传了图片，不点最下面的保存就丢了!';
            }
        });

        //点击发布或者保存按钮
        $('#Form button[type="submit"]').click(function(event) {
            status = $(this).attr('data-status');
            action = $(this).attr('data-action');
            //先判断图片 
            if (action === 'PUBLISHED' && !$('#image').val()) {
                toastr.warning('发布之前必须上传图片');
                return false;
            }
        });


        //标题字数统计
        $('#name').limitTextarea({
            maxNumber: 30,
            theme: 'tips',
            infoStr: '推荐30字以内，'
        });
        //副标题字数统计
        $('#title').limitTextarea({
            maxNumber: 20,
            theme: 'tips',
            infoStr: '推荐20字以内，',
            infoId: 'subtitleinfo'
        });

    }

    /*
       表单验证
     */
    function validations() {
        //表单提交事件
        $.validator.setDefaults({
            submitHandler: function(data) {
                //序列化表单  
                var param = $("#Form").serializeObject();

                //特殊处理景观和标签 
                landscape_id_array = [];
                $('#landscape_alert span button').each(function() {
                    landscape_id_array.push(Number($(this).attr('data-id')));
                });

                if (landscape_id_array.length == 0) {
                    toastr.warning('至少选择一个景观吧');
                    return false;
                }

                param.landscape = landscape_id_array;

                param.content = $("#summernote").code();

                //发布的状态 
                param.action = action;

                var url;
                if (!$('#id').val()) {
                    url = "/admin/themeapi/add";
                } else {
                    url = "/admin/themeapi/save"
                }
                //按钮disabled
                $('#Form button[type="submit"]').btnDisable();
                //解除绑定
                $(window).unbind('beforeunload');
                window.onbeforeunload = null;
                $.ajax({
                    "url": url,
                    "data": param,
                    "type": "post",
                    "dataType": "json",
                    "error": function(e) {
                        alert("服务器未正常响应，请重试");
                        $('#Form button[type="submit"]').btnEnable();
                    },
                    "success": function(response) {
                        if (response.status == 0) {
                            toastr.success('保存成功');
                            localStorage.themeContent = '';
                            localStorage.image = '';
                            if (url.indexOf('add') >= 0) {
                                window.location.href = '/admin/theme/edit?action=edit&id=' + response.data;
                            } else {
                                window.location.reload();
                            }

                        }
                    }
                });

            }
        });
        // validate signup form on keyup and submit
        validate = $("#Form").validate({
            rules: {
                name: "required"
            },
            messages: {
                name: "这可是主标题，不能为空呀！"
            }
        });
    }


    /**
     * 上传图片
     * @param  {[type]} file      [description] 
     * @return {[type]}           [description]
     */
    function sendFile(file) {
        var filename = false;
        try {
            filename = file['name'];
        } catch (e) {
            filename = false;
        }
        if (!filename) {
            return;
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
                $('img[src="' + res.data.url + '"]').attr('data-image', res.data.image);
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
            image: getImgNameBySrc($(file).attr("src"))
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

    function getImgNameBySrc(src) {
        var name = src.replace('/pic/', '');
        return name;
    }
});
