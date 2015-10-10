/*
  话题编辑  
  fyy 2015.7.21
 */
$(document).ready(function() {

    var validate = null;
    var sight_id_array = [];
    var tag_id_array = [];
    var status = 1;

    var List = function(argument) {
        /**
         * 绑定事件
         *  
         */
        var bindEvents = function() {

            //初始化编辑器
            $('#summernote').summernote({
                lang: "zh-CN",
                height: 300,
                toolbar: [
                    //[groupname, [button list]] 
                    //['style', ['bold', 'italic', 'underline', 'clear']],
                    //['font', ['strikethrough']],
                    //['fontsize', ['fontsize']],
                    //['color', ['color']],
                    //['para', ['ul', 'ol', 'paragraph']],
                    //['table', ['table']],
                    //['height', ['height']],
                    ['style', ['bold', 'clear']],
                    ['insert', ['hr', 'link', 'picture']],
                    ['view', ['codeview']]
                ],
                onInit: function() {
                    $('#summernote').code($('#content-text').html());
                },
                onImageUpload: function(files) {
                    sendFile(files[0]);
                },
                onMediaDelete: function(files) {
                    var file = files[0];
                    if ($(file).attr("data-image") || $(file).attr("data-hash")) {
                        if (confirm("会从服务器删除图片，确定删除么 ?") == false) {
                            return false;
                        }
                        //删除图片
                        deleteImage(files);
                    }
                },
                onChange: function(characters, editor, $editable) {
                    localStorage.topicContent = $('#summernote').code();
                    if ($('#id').val()) {
                        $(window).bind('beforeunload', function() {
                            if (localStorage.topicContent) {
                                return '等等!!您输入的正文内容尚未保存！!';
                            }
                        });

                    }
                }
            });

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

            //话题来源自动完成 
            $('#weixin-from,#from_name').typeahead({
                display: 'name',
                val: 'id',
                ajax: {
                    url: '/admin/sourceapi/getSourceList',
                    triggerLength: 1
                },
                itemSelected: function(item, val, text, element) {
                    /*$("#weixin-from").val(text);
                    $("#weixin-from_id").val(val);*/
                    element.attr('data-id', val);
                    element.val(text);
                }
            });
            //话题来源框删除来源
            $('#clear-from').click(function(event) {
                $('#from_name').attr('data-id', '');
                $('#from_name').val('');
            });

            //通用标签输入框自动完成
            $('#tag_name').typeahead({
                display: 'name',
                val: 'id',
                ajax: {
                    url: '/admin/tagapi/getGeneralTagList',
                    triggerLength: 1
                },
                itemSelected: function(item, val, text) {
                    $("#tag_name").val('');
                    //先判断是否已经选择
                    var valnum = Number(val);
                    if (!tag_id_array.in_array(valnum)) {
                        tag_id_array.push(valnum);
                        //添加到右侧选框中 
                        $('#tag_alert').append('<span class="badge badge-sm label-danger" role="badge">' + text + '<button type="button" class="close" data-id="' + val + '"><span class="fa fa-remove"></span></button></span>');
                    }
                }
            });

            //通用标签框删除景点
            $('#tag_alert').delegate('.close', 'click', function(event) {
                var val = Number($(this).attr('data-id'));
                sight_id_array.splice($.inArray(val, sight_id_array), 1);
                $(this).parent().remove();
            });

            //景点和通用标签切换
            /*$('input[name="sight_tag"]').click(function(event) {
                var type = $(this).val();
                $('.sight_tag .sight_tag').addClass('hide');
                $('.sight_tag div[data-type="' + type + '"]').removeClass('hide');
            });*/
            $('#sight_tag').selectpicker();
            $('#sight_tag').change(function(event) {
                var type = $(this).val();
                $('.sight_tag .sight_tag').addClass('hide');
                $('.sight_tag div[data-type="' + type + '"]').removeClass('hide');
            });


            //标签选择
            $('#tags').multiSelect({
                selectableHeader: '<span class="label label-primary">标签库</span>',
                selectionHeader: '<span class="label label-success">已选标签</span>'
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

            //定位地图模态框
            $('#position').click(function(e) {
                //打开模态框
                $('#mapModal').modal({
                    //remote: '/admin/utils/map'
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

            //点击发布或者保存按钮
            $('#Form button[type="submit"]').click(function(event) {
                status = $(this).attr('data-status');
                action = $(this).attr('data-action');
                //先判断图片 
                if (action === 'PUBLISHED' && !$('#image').val()) {
                    toastr.warning('发布之前必须上传图片');
                    return false;
                }
            });

            //搜索来源下拉列表 
            // $('#form-from').selectpicker();
            //选择不同的搜索来源
            $('#Form').delegate('input[data-name="form-from"]', 'click touchend', function(event) {
                $('input[data-name="form-from"]').attr('checked', false);
                $(this).attr('checked', 'ture');
                var type = $(this).attr('data-type');
                if ($(this).val() == "weixin") {
                    $('.weixin-from-input').show();
                    $('.from_name').hide();
                } else {
                    //$('.weixin-from-input').hide();
                    //$('.from_name').show();
                    $('#from-type').next().find('.dropdown-menu li[data-original-index="' + (type - 1) + '"] a').click();

                    $('#from_name').val($(this).attr('data-from_name'));
                    $('#from_name').attr('data-url', $('input[data-name="form-from"]:checked').val());
                    $('#from_name').attr('data-id', $('input[data-name="form-from"]:checked').attr('data-id'));
                }
            });
            //状态下拉列表 
            $('#from-type').selectpicker();
            $('#from-type').change(function(event) {
                var val = $(this).val();
                $('.openSource').attr('data-type', val);
                if (val === '3') {
                    $('.from_detail-input').show();
                } else {
                    $('.from_detail-input').hide();
                }
                /*  if (val === '1') {
                      $('.weixin-from-input').show();
                      $('.from_name').hide();
                      $('.from_detail-input').hide();
                  } else if (val === '2') {
                      $('.weixin-from-input').hide();
                      $('.from_name').show();
                      $('.from_detail-input').hide();
                  } else if (val === '3') {
                      $('.weixin-from-input').show();
                      $('.from_name').hide();
                      $('.from_detail-input').show();
                  }*/
            });

            //点击打开来源创建模态框
            $('.openSource').click(function(event) {
                event.preventDefault();
                var type = $(this).attr('data-type');
                $('#source-type').val(type);
                if (type == "1") {
                    //微信公众号
                    $('#source label[for="source-name"]').text('公众号名称*');
                    $('#source-name').val('');
                    $('#source .source-url').hide();
                    $('#source-url').val('mp.weixin.qq.com');
                } else if (type == "2") {
                    $('#source label[for="source-name"]').text('网站名称*');
                    $('#source-name').val('');
                    $('#source .source-url').show();
                    $('#source-url').val('');
                } else if (type == "3") {
                    //期刊专著
                    $('#source label[for="source-name"]').text('期刊专著名称*');
                    $('#source-name').val('');
                    $('#source .source-url').hide();
                    $('#source-url').val('');
                }
                $('#source-type').val(type);
                //打开模态框
                $('#myModal').modal();
            });

            //点击创建话题来源
            $('#addSource-btn').click(function(event) {
                if (!$('#source-name').val()) {
                    toastr.warning('名称不能为空');
                    return false;
                }
                if ($('#source-type').val() === '2' && !$('#source-url').val()) {
                    toastr.warning('url不能为空');
                    return false;
                }
                $.ajax({
                    "url": "/admin/Sourceapi/addAndReturn",
                    "data": {
                        name: $('#source-name').val(),
                        url: $('#source-url').val(),
                        type: $('#source-type').val()
                    },
                    "async": false,
                    "error": function(e) {
                        alert("服务器未正常响应，请重试");
                    },
                    "success": function(response) {
                        if (response.status != 0) {
                            alert(response.statusInfo);
                        } else {
                            //添加创建的来源 并选中
                            var data = response.data;

                            $('#from_name').attr('data-id', data.id);
                            $('#from_name').attr('data-url', data.url);
                            $('#from_name').val(data.name);

                            //手工关闭模态框
                            $('#myModal').modal('hide');
                            document.getElementById("source").reset();
                        }
                    }
                });
            });

            //点击显示标签创建
            $('.addTag').click(function(event) {
                event.preventDefault();
                $('#addTag').show();
            });
            //点击隐藏标签创建
            $('#hideTag-btn').click(function(event) {
                event.preventDefault();
                $('#addTag').hide();
            });
            //点击创建新标签
            $('#addTag-btn').click(function(event) {
                if (!$('#tag_name').val()) {
                    toastr.warning('标签名称不能为空！');
                    return false;
                }
                var data = {
                    name: $('#tag_name').val()
                };
                $.ajax({
                    "url": "/admin/Tagapi/save",
                    "data": data,
                    "type": "post",
                    "error": function(e) {
                        alert("服务器未正常响应，请重试");
                    },
                    "success": function(response) {
                        if (response.status == 0) {
                            var data = response.data;
                            $('#div-tag label:last').after('<label class="checkbox-inline"><input type="checkbox" name="form-tag" data-name="form-tag" id="" value="' + data.id + '" >' + data.name + '</label>');
                            $('input[data-name="form-tag"]').attr('checked', false);
                            $('#div-tag input:last').attr('checked', 'ture');
                            //绑定Uniform
                            Metronic.initUniform($('input[data-name="form-tag"]'));

                            //隐藏输入框
                            $('#addTag').hide();
                        }
                    }
                });
            });

            //复制链接 
            var client = new ZeroClipboard(document.getElementById("copy-button"));
            client.on("ready", function(readyEvent) {
                // alert( "ZeroClipboard SWF is ready!" ); 
                client.on("aftercopy", function(event) {
                    // `this` === `client`
                    // `event.target` === the element that was clicked
                    /*event.target.style.display = "none";
                    alert("Copied text to clipboard: " + event.data["text/plain"]);*/
                    alert('复制成功！');
                });
            });

            //标题字数统计
            $('#title').limitTextarea({
                maxNumber: 28,
                theme: 'tips',
                infoStr: '推荐28字以内，'
            });
            //副标题字数统计
            $('#subtitle').limitTextarea({
                maxNumber: 16,
                theme: 'tips',
                infoStr: '推荐16字以内，',
                infoId: 'subtitleinfo'
            });

            //详细来源说明弹出框 
            $('#from_detail-help').popover({
                //trigger: 'hover',
                placement: 'top',
                html: true,
                title: '无来源网址的“来源标注”情况',
                content: '(1)来源为期刊: 姓名，文章名，杂志名+期数 <br>(2)来源为专著: 姓名，专著名'
            });
        }

        /*
       表单验证
       */
        var validations = function() {
            //表单提交事件
            $.validator.setDefaults({
                submitHandler: function(data) {
                    //序列化表单  
                    var param = $("#Form").serializeObject();

                    //特殊处理景点 
                    sight_id_array = [];
                    if ($('#sight_tag').val() == 'sight') {
                        $('#sight_alert span button').each(function() {
                            sight_id_array.push(Number($(this).attr('data-id')));
                        });
                        if (sight_id_array.length == 0) {
                            toastr.warning('至少选择一个景点吧');
                            return false;
                        }

                    }

                    //特殊处理通用标签 
                    tag_id_array = [];
                    if ($('#sight_tag').val() == 'tag') {
                        $('input[data-name="form-generaltag"]:checked').each(function() {
                            tag_id_array.push(Number($(this).val()));
                        });
                        if (tag_id_array.length == 0) {
                            toastr.warning('至少选择一个通用标签吧');
                            return false;
                        }
                    }
                    //分类和普通标签
                    $('input[data-name="form-tag"]:checked').each(function() {
                        tag_id_array.push(Number($(this).val()));
                    });
                    param.tags = tag_id_array;
                    param.sights = sight_id_array;
                    param.from = from = $('#from_name').attr('data-id');
                    param.content = $("#summernote").code();

                    //发布的状态
                    //param.status = status;
                    param.action = action;

                    var url;
                    if (!$('#id').val()) {
                        url = "/admin/topicapi/add";
                    } else {
                        url = "/admin/topicapi/save"
                    }
                    //按钮disabled
                    $('#Form button[type="submit"]').btnDisable();
                    //解除绑定
                    $(window).unbind('beforeunload');
                    window.onbeforeunload = null;
 
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
                                toastr.success('保存成功');
                                localStorage.topicContent = '';
                                localStorage.image = '';
                                if (url.indexOf('add') >= 0) {
                                    //resetForm();
                                    window.location.href = '/admin/topic/edit?action=edit&id=' + response.data;
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
                    title: "required"
                },
                messages: {
                    title: "这可是主标题，不能为空呀！"
                }
            });
        }

        //重置表单
        var resetForm = function() {
            $("button[name='reset']").click();
            $('#imageView').html('');
            $('#imageView').addClass('imageView');
            sight_id_array = [];
            tag_id_array = [];
            $('#sight_alert').html('');
            $('#ms-tags .ms-selection li').click();

        }

        /**
         * 上传图片
         * @param  {[type]} file      [description] 
         * @return {[type]}           [description]
         */
        var sendFile = function(file) {
            var filename = false;
            try {
                filename = file['name'];
            } catch (e) {
                filename = false;
            }
            if (!filename) {
                return;
            }
            //以上防止在图片在编辑器内拖拽引发第二次上传导致的提示错误 
            // $('#summernote').summernote('editor.insertImage', 'http://res.cloudinary.com/demo/image/upload/butterfly.jpg'); 
            $.ajaxFileUpload({
                url: '/upload/pic',
                secureuri: false,
                fileElementId: 'note-image-input',
                dataType: 'json',
                success: function(res, status) { //当文件上传成功后，需要向数据库中插入数据
                    //把图片放到编辑框中。editor.insertImage 是参数，写死。后面的http是网上的图片资源路径。  
                    //网上很多就是这一步出错。  
                    $('#summernote').summernote('editor.insertImage', res.data.url);
                    $('img[src="' + res.data.url + '"]').attr('data-image', res.data.image);
                    toastr.success('图片上传成功！');
                },
                error: function(data, status, e) {
                    alert("服务器未正常响应，请重试");
                }
            })
        }

        /**
         * 删除图片
         * @return {[type]} [description]
         */
        var deleteImage = function(file, editor, $editable) {
            var data = {
                image: getImgNameBySrc($(file).attr("src"))
            };
            $.ajax({
                "url": '/upload/delpic',
                "data": data,
                "type": "post",
                "dataType": "json",
                "error": function(e) {
                    alert("服务器未正常响应，请重试");
                },
                "success": function(response) {
                    if (response.status == 0) {
                        toastr.success('删除成功');
                    }
                }
            });
        }

        var getImgNameBySrc = function(src) {
            var name = src.replace('/pic/', '');
            return name;
        }
        return {
            init: function() {
                bindEvents();
                validations();

            }
        }
    }

    new List().init();

});
