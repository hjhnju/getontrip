$(document).ready(function() {
    var validate = null;

    validations();
    bindEvents();

    function bindEvents() {
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
            if(action==='PUBLISHED'&&!$('#image').val()){
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

        function btnStatus(){

        }
    }
});
