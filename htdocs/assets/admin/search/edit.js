/*

 搜索标签编辑
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

        var change = function(type) {
            location.href = "/admin/search/edit?type=" + type;
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
                        "url": '/admin/searchapi/add',
                        "data": param,
                        "type": "post",
                        "dataType": "json",
                        "error": function(e) {
                            alert("服务器未正常响应，请重试");
                        },
                        "success": function(response) {
                            if (response.status == 0) {
                                toastr.success('添加成功');
                            }
                        }
                    });

                }
            });
            // validate signup form on keyup and submit
            validate = $("#Form").validate({
                rules: {
                    name: {
                        'required': true
                    }
                },
                messages: {
                    name: {
                        'required': '标题不能为空！'
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
