/*

 消息编辑
  author:fyy
 */
$(document).ready(function() {
    var Edit = function() {

        var validate = null;
        var action = '';
        /**
         * 绑定事件
         *  
         */
        var bindEvents = {
            init: function() {
                //点击保存或者确认并保存按钮
                $('#Form button[type="submit"]').click(function(event) {
                    action = $(this).attr('data-action');
                });
                //标题字数统计
                $('#title').limitTextarea({
                    maxNumber: 16,
                    infoId: 'titleinfo',
                    onOk: function() {
                        $('#title').css('background-color', 'transparent');
                    },
                    onOver: function() {
                        $('#title').css('background-color', 'lightpink');
                    }
                });
                //内容字数统计
                $('#content').limitTextarea({
                    maxNumber: 78,
                    onOk: function() {
                        $('#content').css('background-color', 'transparent');
                    },
                    onOver: function() {
                        $('#content').css('background-color', 'lightpink');
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

                    $.ajax({
                        "url": '/admin/msgapi/' + action,
                        "data": param,
                        "type": "post",
                        "dataType": "json",
                        "error": function(e) {
                            alert("服务器未正常响应，请重试");
                        },
                        "success": function(response) {
                            if (response.status == 0) {
                                toastr.success('发送成功');
                            }
                        }
                    });

                }
            });
            // validate signup form on keyup and submit
            validate = $("#Form").validate({
                rules: {
                    title: {
                        'required': true,
                        'maxlength': 16
                    },
                    content: {
                        'required': true,
                        'maxlength': 78
                    }
                },
                messages: {
                    title: {
                        'required': '标题不能为空！',
                        'maxlength': $.validator.format("最多输入{0}个字")
                    },
                    content: {
                        'required': '内容不能为空哦！',
                        'maxlength': $.validator.format("最多输入{0}个字")
                    }

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
