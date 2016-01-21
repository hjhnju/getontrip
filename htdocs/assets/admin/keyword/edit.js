/*

 景点词条编辑
  author:fyy
 */
$(document).ready(function() {
    var Edit = function() {

        var validate = null;
        /**
         * 绑定事件
         *  
         */
        var bindEvents = {
            init: function() {
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
                
              //上传音频，得到url
                $("#audioupload").live('change',function(e){
                	$(".aaa").detach();
                	$(this).after('<br class="aaa">');
                    $(this).after('<a href="#" class="btn btn-success fileupload-exists aaa" data-dismiss="fileupload" id="up-audio"><i class="fa fa-cloud-upload aaa"></i> 上传</a>');
                    $(this).after('<a href="#" class="btn btn-danger fileupload-exists aaa" data-dismiss="fileupload" id="rm-audio"><i class="fa fa-trash aaa"></i> 移除</a>');
                });
                
                //上传音频，得到url
                $("#up-audio").live('click',function(event) {
                    $.ajaxFileUpload({
                        url: '/upload/audio',
                        secureuri: false,
                        fileElementId: 'audioupload',
                        dataType: 'json',
                        success: function(res, status) { //当文件上传成功后，需要向数据库中插入数据
                            $('#audio').val(res.data.name);
                        	$('#label_audio').html(res.data.name);
                        	$('#audio_len').val(res.data.len);
                            localStorage.audio = res.data.name;
                        },
                        error: function(res) {
                            alert(res);
                        }
                    })
                });
                
                //确保图片上传后存上
                $(window).bind('beforeunload', function() {
                    if (localStorage.image) {
                        return '等等!!你刚刚上传了图片，不点最下面的保存就丢了!';
                    }
                });
            	

                //定位地图模态框
                $('#position').click(function(e) {
                    //打开模态框
                    $('#mapModal').modal({
                        //remote: '/admin/utils/map'
                    });
                    $("#txtSearch").val($.trim($("#name").val()));
                    $('#mapModal .btn-search').click();
                });
                
              //选择类型
                $('#sight_tag').selectpicker();
                $('#sight_tag').change(function(event) {
                    var type = $(this).val();
                    $('.sight_city .sight_tag').addClass('hide');
                    $('.sight_city .sight_tag[data-type="' + type + '"]').removeClass('hide');
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
                
                $('#city_name').typeahead({
                    display: 'name',
                    val: 'id',
                    ajax: {
                        url: '/admin/cityapi/getCityList',
                        triggerLength: 1
                    },
                    itemSelected: function(item, val, text) {
                        $("#city_name").val(text);
                        $("#sight_id").val(val);
                    }
                });

                //输入词条名称生成url
                $('#name').blur(function(event) {
                    var name = $.trim($(this).val());
                    if (name) {
                        $('#url').val('http://baike.baidu.com/item/' + name);
                        $('#view-link').attr('href', $('#url').val());
                    }
                });
                $('#url').blur(function(event) {
                    var name = $.trim($(this).val());
                    if (name) {
                        $('#view-link').attr('href', $('#url').val());
                    }
                });
                //点击保存或者确认并保存按钮
                $('#Form button[type="submit"]').click(function(event) {
                    $('#status').val($(this).attr('data-status'));
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
                    param.status=$('#status').val();
                    if($('#type').is(':checked')){
                    	param.type = 1;
                    }else{
                    	param.type = 0;
                    }
                    
                    if($('#sight_tag').val() == 'sight'){
                    	param.level = 2;
                    }else if($('#sight_tag').val() == 'city'){
                    	param.level = 1;
                    }
                    if($('#sight_id').val()){
                    	param.sight_id = $('#sight_id').val();
                    } 
                    
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
                    sight_name: "required",
                    xy: "required"
                },
                messages: {
                    name: "名称不能为空！",
                    sight_name: "景点名称不能为空哦！",
                    xy: "坐标不能为空"

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
