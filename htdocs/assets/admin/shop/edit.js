/*
  视频编辑  
  fyy 2015.7.21
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
                this.init_image();
                this.init_map();
                this.init_sight();
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
                            localStorage.shopimage = res.data.image;
                        },
                        error: function(data, status, e) {
                            alert(status.statusInfo);
                        }
                    })
                });

                //确保图片上传后存上
                $(window).bind('beforeunload', function() {
                    if (localStorage.shopimage) {
                        return '等等!!你刚刚上传了图片，不点最下面的保存就丢了!';
                    }
                });
            },
            init_sight: function() {
                sight_id_array = [];
                $('#sight_alert span button').each(function() {
                    sight_id_array.push(Number($(this).attr('data-id')));
                });
                //景点输入框自动完成
                $('#sight_name').typeahead({
                    display: 'name',
                    val: 'id',
                    ajax: {
                        url: '/admin/sightapi/getSightList',
                        triggerLength: 1
                    },
                    itemSelected: function(item, val, text) {
                        $("#sight_name").val('');
                        //先判断是否已经选择
                        var valnum = Number(val);
                        if (!sight_id_array.in_array(valnum)) {
                            sight_id_array.push(valnum);
                            //添加到右侧选框中 
                            $('#sight_alert').append('<span class="badge badge-sm label-danger" role="badge">' + text + '<button type="button" class="close" data-id="' + val + '"><span class="fa fa-remove"></span></button></span>');
                        }
                    }
                });

                //景点框删除景点
                $('#sight_alert').delegate('.close', 'click', function(event) {
                    var val = Number($(this).attr('data-id'));
                    sight_id_array.splice($.inArray(val, sight_id_array), 1);
                    $(this).parent().remove();
                });

            },
            
            init_map: function() {
                //定位地图模态框
                $('#position').click(function(e) {
                    //判断城市和景点名称是否已经填写

                    validate.settings.rules.xy.required = true;

                    //打开模态框
                    $('#mapModal').modal({
                        //remote: '/admin/utils/map'
                    });
                    $("#txtSearch").val($.trim($("#addr").val()));
                    $('#mapModal .btn-search').click();
                });

                // 模态框从远端的数据源加载完数据之后触发该事件
                $('#mapModal').on('loaded.bs.modal', function(e) {
                    $("#txtSearch").val($.trim($("#addr").val()));
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
            },
            
            init_others: function() {
                $('.dpYears').datepicker({
                    autoclose: true
                });

                $('#url').blur(function(event) {
                    $('#view-link').attr('href',$(this).val());
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

                    //特殊处理景点
                    param.sight_id = sight_id_array;

                    //param.type = Number(param.type);
                    param.action = action;

                    var url = '';
                    if (!$('#id').val()) {
                        url = '/admin/shopapi/add';
                    } else {
                        url = "/admin/shopapi/save"
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
                            $('#Form button[type="submit"]').btnEnable();
                            if (response.status == 0) {
                                localStorage.shopimage = '';
                                toastr.success('保存成功');
                                  if (url.indexOf('add') >= 0) {
                                    //resetForm();
                                    window.location.href = '/admin/shop/edit?action=edit&id=' + response.data;
                                } else {
                                    window.location.reload();
                                }
                            } else {
                                alert(response.statusInfo);
                            }
                        }
                    });

                }
            });
            // validate signup form on keyup and submit
            validate = $("#Form").validate({
                rules: {
                    title: "required",
                    url:'required',
                    xy:'required',
                    len:{
                        required : function(){
                            return (action === 'PUBLISHED')
                        }
                    }
                },
                messages: {
                    title: "视频名称不能为空哦！",
                    url: "链接不能为空！",
                    len:{
                        required : '时长不能为空！'
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
