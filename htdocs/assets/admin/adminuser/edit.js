(function(window, document, undefined) {
    var Edit = function() { 

        /**
         * 表单验证 
         */
        var validations = function() {
            $.validator.setDefaults({
                submitHandler: function(data) {
                    //序列化表单  
                    var param = $("#Form").serializeObject();  
                    var editAdmin = new Remoter('/admin/AdminUserapi/save');
                    editAdmin.remote(param);
                    editAdmin.on('success', function(data) { 
                        toastr.success('保存成功');
                    });

                }
            });
            // validate signup form on keyup and submit
            validate = $("#Form").validate({
                rules: {
                    name: "required",
                    role: "required"
                },
                messages: {
                    name: "名称不能为空！",
                    role: "角色不能为空哦！"

                }
            });
        }

        /*
              绑定事件
         */
        var bindEvents = function() {
            //角色下拉列表 
           $('#form-role').selectpicker();

        }


        return {
            init: function() {
                validations();
                bindEvents();
            }
        }
    }

    new Edit().init();

}(window, document));
