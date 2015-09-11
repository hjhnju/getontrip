$(document).ready(function() {
    var validate = null;

    validations();
    bindEvents();

    function bindEvents() {
        //初始化编辑器
        $('#summernote').summernote({
            lang: "zh-CN",
            height: 300,
            toolbar: [
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
                if ($(file).attr("data-image") || $(file).attr("data-hash")) {
                    if (confirm("会从服务器删除图片，确定删除么 ?") == false) {
                        return false;
                    }
                    //删除图片
                    deleteImage(files);
                }
            },
            onChange: function(characters, editor, $editable) { 
                localStorage.landscapeContent = $('#summernote').code();
                if ($('#id').val()) {
                    $(window).bind('beforeunload', function() {
                        if (localStorage.landscapeContent) {
                            return '等等!!您输入的正文内容尚未保存！!';
                        }
                    });

                }
            }
        });

        //城市输入框自动完成
        $('#city_name').typeahead({
            display: 'name',
            val: 'id',
            ajax: {
                url: '/admin/cityapi/getCityList',
                triggerLength: 1
            },
            itemSelected: function(item, val, text) {
                /* item: the HTML element that was selected
                 val: value of the *val* property
                 text: value of the *display* property*/
                $("#city_name").val(text);
                $("#city_id").val(val);
            }
        });

        //定位地图模态框
        $('#position').click(function(e) {
            //判断城市和景点名称是否已经填写
            if (!$.trim($("#city_name").val()) || !$.trim($("#name").val())) {
                validate.settings.rules.xy.required = function() {
                    return !$.trim($("#city_name").val()) || $.trim($("#name").val());
                };
                $("#Form").submit();
                return false;
            }

            validate.settings.rules.xy.required = true;

            //打开模态框
            $('#mapModal').modal({
                // remote: '/admin/utils/map'
            });
            $("#txtSearch").val($.trim($("#name").val()));
            $("#cityName").val($.trim($("#city_name").val()));
            $('#mapModal .btn-search').click();
        });
        //模态框 点击确定之后立即触发该事件。
        $('#mapModal').delegate('.btn-submit', 'click', function(event) {
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
            $('#mapModal').modal('hide');
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

        //描述字数统计
        /*  $('#content').limitTextarea({
              maxNumber: 75,
              onOk: function() {
                  $('#describe').css('background-color', 'transparent');
                  $('#Form #submitBtn').attr('disabled', false);
              },
              onOver: function() {
                  $('#describe').css('background-color', 'lightpink');
                  $('#Form #submitBtn').attr('disabled', 'disabled');
              }
          });*/

        //点击发布或者保存按钮
        $('#Form button[type="submit"]').click(function(event) {

            action = $(this).attr('data-action');
            //先判断图片 
            if (action === 'PUBLISHED' && !$('#image').val()) {
                toastr.warning('发布之前必须上传图片');
                return false;
            }
        });
    }

    /*
       表单验证
     */
    function validations() {
        $.validator.setDefaults({
            submitHandler: function(data) {
                //序列化表单  
                var param = $("#Form").serializeObject();
                param.action = action;
                param.content = $("#summernote").code();
                var url = '';
                if (!$('#id').val()) {
                    url = '/admin/landscapeapi/add';
                } else {
                    url = "/admin/landscapeapi/save"
                }
                //按钮disabled
                $('#Form button[type="submit"]').btnDisable();
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
                            localStorage.image = '';
                            localStorage.landscapeContent = '';
                            alert('保存成功');
                            //$("button[name='reset']").click();
                            $('#Form button[type="submit"]').btnEnable();
                        }
                    }
                });

            }
        });
        // validate signup form on keyup and submit
        validate = $("#Form").validate({
            rules: {
                name: "required",
                city_name: "required",
                xy: {
                    required: true
                }
            },
            messages: {
                name: "景点名称不能为空！",
                city_name: "城市名称不能为空哦！",
                xy: "坐标不能为空"
            }
        });

        function btnStatus() {

        }
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
