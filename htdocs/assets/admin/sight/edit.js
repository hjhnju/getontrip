$(document).ready(function() {
    var validate = null;
    $.validator.setDefaults({
        submitHandler: function(data) {
            //序列化表单  
            var param = $("#Form").serializeObject();
            var url;
            if(!$('#id').val()){
                url="/admin/sightapi/add";
            }else{
                 url="/admin/sightapi/save"
            }
            $.ajax({
                "url": url,
                "data": param,
                "type": "post",
                "dataType":"json",
                "error": function(e) {
                    alert("服务器未正常响应，请重试");
                },
                "success": function(response) {
                    if (response.status == 0) {
                        alert('保存成功');
                        $("button[name='reset']").click();
                        $('#imageView').html('');
                        $('#imageView').addClass('imageView');
                    }
                }
            });

        }
    });
    validations();

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
                return !$.trim($("#city_name").val()) && $.trim($("#name").val());
            };
            $("#Form").submit();
            return false;
        }

        validate.settings.rules.xy.required = true;

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

        validate.settings.rules.xy.required = true;

        //手工关闭模态框
        $('#myModal').modal('hide');
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

    //提交表单
    /* $("#submitBtn").click(function(event) {
     	$("#Form").submit();
     });*/

    /*
       表单验证
     */
    function validations() {
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
    }
});
