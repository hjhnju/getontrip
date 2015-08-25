/*

 景点词条编辑
  author:fyy
 */
$(document).ready(function() {
    var validate = null; 
    validations();
    bindEvents(); 

    function bindEvents() {
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

        //输入词条名称生成url
        $('#name').blur(function(event) {
            var name= $.trim($(this).val());
            if(name){ 
               $('#url').val('http://baike.baidu.com/item/'+name);
               $('#view-link').attr('href',$('#url').val());
            }
        });
        $('#url').blur(function(event) {
           var name= $.trim($(this).val());
            if(name){  
               $('#view-link').attr('href',$('#url').val());
            }
        });
        //点击保存或者确认并保存按钮
        $('#Form button[type="submit"]').click(function(event) {
             $('#status').val($(this).attr('data-status'));
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
                //param.status=$('#status').val();
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
                                //window.location.href='/admin/keyword/edit?action=edit&id='+response.data;
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
                name: "required",
                sight_name: "required" 
            },
            messages: {
                name: "名称不能为空！",
                sight_name: "景点名称不能为空哦！" 
                
            }
        });
    }
});
