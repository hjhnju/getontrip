/*
  景点编辑  
  fyy 2015.7.21
 */
$(document).ready(function() {

    var Edit = function() {

        var validate = null; 
        var action='';
        /**
         * 绑定事件
         *  
         */
        var bindEvents = {
            init: function() {
                this.init_image();
                this.init_isbn();
                this.init_others();
            },
            init_image: function() {
                //上传图片，得到url
                $("#upload-img").click(function(event) {
                    $.ajaxFileUpload({
                        url: '/upload/pic',
                        secureuri: false,
                        fileElementId: 'imageBtn',
                        dataType: 'json',
                        success: function(res, status) { //当文件上传成功后，需要向数据库中插入数据
                            $('#image').val(res.data.image);
                            $('#imageView').html('<img src="/pic/' + res.data.image.getNewImgByImg(190, 80, 'f') + '"  alt=""/>');
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
            },
            init_isbn: function() {
                //根据isbn抓取数据信息

            },
            init_others: function() {
                $('.dpYears').datepicker({
                    autoclose: true
                });

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
        }



        /*
           表单验证
           */
        var validations = function() {
            $.validator.setDefaults({
                submitHandler: function(data) {

                    //序列化表单  
                    var param = $("#Form").serializeObject();
 
                    param.action = action;
                    var url = '';
                    if (!$('#id').val()) {
                        url = '/admin/bookapi/add';
                    } else {
                        url = "/admin/bookapi/save"
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
                                toastr.success('保存成功');

                            } else {
                                alert(response.statusInfo);
                            }
                            $('#Form button[type="submit"]').btnEnable();
                        }
                    });

                }
            });
            // validate signup form on keyup and submit
            validate = $("#Form").validate({
                rules: {
                    title: "required",
                    isbn: {
                        required: true
                    },
                    image: {
                        required: (action === 'PUBLISHED')
                    }
                },
                messages: {
                    title: "书籍名称不能为空哦！",
                    isbn: "ISBN不能为空！",
                    image: '发布之前必须上传图片'
                }
            });

        }
        return {
            init: function() {
                validations();
                bindEvents.init();
            }
        }
    }
    new Edit().init();


});