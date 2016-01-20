$(document).ready(function() {
    var Edit = function() {
        var validate = null;

        /**
         * 绑定事件
         *  
         */
        var bindEvents = {
            init: function() {
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
                    //打开模态框
                    $('#mapModal').modal({
                        //remote: '/admin/utils/map'
                    });
                    $("#txtSearch").val($.trim($("#name").val()));
                    $("#cityName").val($.trim($("#name").val()));
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

                    validate.settings.rules.xy.required = true;

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

 
                //点击发布或者保存按钮
                $('#Form button[type="submit"]').click(function(event) { 
                    action = $(this).attr('data-action');
                    //先判断图片 
                    if (action === 'PUBLISHED' && !$('#image').val()) {
                        toastr.warning('开通之前必须上传图片');
                        return false;
                    }
                });

                //确保图片上传后存上
                $(window).bind('beforeunload', function() {
                    if (localStorage.image) {
                        return '等等!!你刚刚上传了图片，不点最下面的保存就丢了!';
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
                    
                    tag_id_array = [];
                    $('input[data-name="form-generaltag"]:checked').each(function() {
                        tag_id_array.push(Number($(this).val()));
                    });

                    param.tags = tag_id_array;
                    
                    var url = '';
                    if (!$('#id').val()) {
                        url = '/admin/cityapi/add';
                    } else {
                        url = "/admin/cityapi/save"
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
                    name: "required",
                    xy: {
                        required: true
                    } 
                },
                messages: {
                    name: "城市名称不能为空哦！",
                    xy: "坐标不能为空" 
                }
            });

            function btnStatus() {

            }
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
