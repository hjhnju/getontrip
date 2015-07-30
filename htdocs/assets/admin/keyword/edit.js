/*

 景点词条编辑
  author:fyy
 */
$(document).ready(function() {
    var validate = null;
    $.validator.setDefaults({
        submitHandler: function(data) {
            //序列化表单  
            var param = $("#Form").serializeObject();
            var url;
            if (!$('#id').val()) {
                url = "/admin/keywordapi/add";
            } else {
                url = "/admin/keywordapi/save"
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
                            $("button[name='reset']").click();
                        } else {
                            window.location.reload();
                        } 
                    }
                }
            });

        }
    });
    validations();

    //景点输入框自动完成
    $('#sight_name').typeahead({
        display: 'name',
        val: 'id',
        ajax: {
            url: '/admin/sightapi/getSightList',
            triggerLength: 1
        },
        itemSelected: function(item, val, text) {
            $("#sight_name").val(text);
            $("#sight_id").val(val);
        }
    });





    /*
       表单验证
     */
    function validations() {
        // validate signup form on keyup and submit
        validate = $("#Form").validate({
            rules: {
                name: "required",
                sight_name: "required",
                url: "required"
            },
            messages: {
                name: "名称不能为空！",
                sight_name: "景点名称不能为空哦！",
                url: "链接url不能为空!"
            }
        });
    }
});
