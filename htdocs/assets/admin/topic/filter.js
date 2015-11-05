/*
  话题筛选器 
  fyy 2015.7.22
 */
$(document).ready(function() {
    var Filter = function() {
        var baidu = "http://www.baidu.com/s";
        var sogou = "http://weixin.sogou.com/weixin";

        var tagStr = '';
        var tag_idArray = [];
        var keywordStr = '';
        var keyword_idArray = [];
        var site_array = {};
        var site_array_weixin = [];
        var site_array_site = [];


        /**
         * 绑定事件
         *  
         */
        var bindEvents = {
            init: function() {
                this.init_sight();
                this.init_tag();
                this.init_keyword();
                this.init_from();
                this.init_others();
            },
            init_sight: function() {
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
                        setQuery();
                        //搜索景点所属的词条 
                        $.ajax({
                            "url": "/admin/keywordapi/getKeywordsBySightId",
                            "data": {
                                "sight_id": val
                            },
                            "type": "post",
                            "error": function(e) {
                                alert("服务器未正常响应，请重试");
                            },
                            "success": function(response) {
                                if (response.status == 0) {
                                    var option = '';
                                    var datas = response.data;
                                    if (datas.length != 0) {
                                        for (var i = 0; i < datas.length; i++) {
                                            var data = datas[i];
                                            option = option + '<label class="checkbox-inline">';
                                            option = option + '<input type="checkbox" id="" data-name="form-keyword" value="' + data.id + '" data-text="' + data.name + '"/>' + data.name;
                                            option = option + ' </label>';
                                        }
                                        $('#keywords').html(option);
                                        $('#form-group-keywords').show();
                                        //绑定Uniform
                                        Metronic.initUniform($('input[data-name="form-keyword"]'));
                                    } else {
                                        $('#keywords').html('');
                                        $('#form-group-keywords').hide();
                                    }
                                }
                            }
                        });

                    }
                });

                //景点框后的清除按钮，清除所选的景点
                $('#clear-sight').click(function(event) {
                    $("#sight_name").val('');
                    $("#sight_id").val('');
                    $('#keywords').html('');
                    $('#form-group-keywords').hide();
                    setQuery();
                });

            },
            init_tag: function() {
                //选择标签
                $('input[data-name="form-tag"]').change(function(event) {
                    //处理所选的标签
                    tagStr = '';
                    tag_idArray = [];
                    $('input[data-name="form-tag"]:checked').each(function() {
                        tagStr = tagStr + ' ' + $(this).attr('data-text');
                        tag_idArray.push(Number($(this).val()));
                    });
                    setQuery();
                });
            },
            init_keyword: function() {
                //选择keyword词条
                $('#Form').delegate('input[data-name="form-keyword"]', 'change', function(event) {
                    keywordStr = '';
                    keyword_idArray = [];
                    $('input[data-name="form-keyword"]:checked').each(function() {
                        keywordStr = keywordStr + ' ' + $(this).attr('data-text');
                        keyword_idArray.push(Number($(this).val()));
                    });
                    setQuery();
                });
            },
            init_from: function() {
                //选择不同的搜索来源
                $('#Form').delegate('input[data-name="form-from"]', 'click touchend', function(event) {
                    /*$('input[data-name="form-from"]').attr('checked', false);
                    $(this).attr('checked', 'ture');*/
                    if ($(this).val() == "weixin") {
                        $("#Form").attr('action', sogou);
                        //$('#weixin-from-input').show();
                        $('#from_name').parent().parent().parent().hide();
                    } else {
                        $("#Form").attr('action', baidu);
                        //$('#weixin-from-input').hide();
                        $('#from_name').val($(this).attr('data-from_name'));
                        $('#from_name').attr('data-url', $('input[data-name="form-from"]:checked').val());
                        $('#from_name').attr('data-id', $('input[data-name="form-from"]:checked').attr('data-id'));
                        $('#from_name').parent().parent().parent().show();

                    }
                    setQuery();
                });

                //选择不同的搜索来源 (分组)
                $('#Form').delegate('input[data-name="form-from"]', 'click touchend', function(event) {

                    var url = $(this).val();
                    var type = $(this).attr('data-type');
                    setSite(url, type);
                });

                //来源框后的清除按钮，清除所选的景点
                $('#clear-from').click(function(event) {
                    $("#from_name").val('');
                    $("#from_name").attr('data-url', '');
                    $("#from_name").attr('data-id', '');
                    setQuery();
                });
                //微信公众号自动完成 
                $('#weixin-from,#from_name').typeahead({
                    display: 'name',
                    val: 'url',
                    data: ['id'],
                    ajax: {
                        url: '/admin/sourceapi/getSourceList',
                        triggerLength: 1
                    },
                    itemSelected: function(item, val, text, element) {
                        element.attr('data-url', val);
                        element.attr('data-id', item.attr('data-id'));
                        element.val(text);
                        setQuery();
                    }
                });



                //点击打开来源创建模态框
                $('.openSource').click(function(event) {
                    event.preventDefault();
                    var type = $(this).attr('data-type');
                    $('#source-type').val(type);
                    if (type == "1") {
                        //微信公众号
                        $('#source label[for="source-name"]').text('公众号名称:');
                        $('#source-name').val($('#weixin-from').val());
                        $('#source .source-url').hide();
                        $('#source-url').val('mp.weixin.qq.com');
                    } else {
                        $('#source label[for="source-name"]').text('来源名称:');
                        $('#source-name').val('');
                        $('#source .source-url').show();
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
                    if (!$('#source-url').val()) {
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
                                if (data.type == 1) {
                                    //公众号
                                    $('#weixin-from').attr('data-id', data.id);
                                    $('#weixin-from').val(data.name);
                                } else {
                                    /* $('#div-from label:last').after('<label class="radio-inline"><input type="radio" name="form-from" data-name="form-from" id="" value="' + data.url + '" >' + data.name + '</label>');
                                     $('input[data-name="form-from"]').attr('checked', false);
                                     var last=$('#div-from input:last');
                                     last.attr('checked', 'ture');
                                     last.attr({
                                         'checked': 'ture',
                                         'data-from_name': data.name,
                                         'data-type':  data.type ,
                                         'data-id':  data.id 
                                     });
                                     last.click();
                                     //绑定Uniform
                                     Metronic.initUniform($('input[data-name="form-from"]'));*/

                                    $('#from_name').attr('data-id', data.id);
                                    $('#from_name').attr('data-url', data.url);
                                    $('#from_name').val(data.name);


                                }
                                //手工关闭模态框
                                $('#myModal').modal('hide');
                                document.getElementById("source").reset();
                            }
                        }
                    });
                });
            },
            init_others: function() {
                //工具提示框
                $('[data-toggle="tooltip"]').tooltip();
                //搜索事件
                $('#search-btn').click(function(event) {
                    //$("#Form").submit();
                    var query = setQuery();
                    for (var i = 0; i < site_array_weixin.length; i++) {
                        window.open(sogou + '?type=2&query=' + query, "_blank");
                    };
                    for (var i = 0; i < site_array_site.length; i++) {
                        window.open(baidu + '?tn=baidu&wd=site:' + site_array_site[i] + ' ' + query + '-(日游)', "_blank");
                    };
                });



                //点击创建话题
                $('#addTopic-btn').click(function(event) {
                    var sight = $('#sight_id').val();
                    if (!sight) {
                        toastr.warning('请选择一个景点！');
                        return false;
                    }
                    var url = $('#url').val();
                    if (!url) {
                        toastr.warning('请填写原文链接！');
                        return false;
                    }
                    var from = "";
                    /* if ($('input[data-name="form-from"]:checked').val() == "weixin") {
                         from = $('#weixin-from').attr('data-id');
                         if (!from) {
                             toastr.warning('请填写微信公众号！');
                             return false;
                         }
                     } else {
                         from = $('#from_name').attr('data-id');
                     }*/
                    fromDomain = getFromDomain(url);
                    if (fromDomain == 'mp.weixin.qq.com') {
                        from = $('#weixin-from').attr('data-id');
                        if (!from) {
                            toastr.warning('请填写微信公众号！');
                            return false;
                        }
                    } else {
                        from = $('#from-div input[value="' + fromDomain + '"]').attr('data-id');
                    }


                    //组装话题参数
                    var params = {
                        //from_url: $('input[data-name="form-from"]:checked').val(),
                        from: from,
                        sights: [Number(sight)],
                        tags: tag_idArray,
                        url: url,
                        status: 1 //未发布的状态
                    }

                    //按钮disabled
                    $(this).attr('disabled', 'disabled');
                    $(this).html('<i class="fa fa-spinner fa-pulse"></i>保存中，请稍后');
                    $.ajax({
                        "url": "/admin/topicapi/addByFilter",
                        "data": params,
                        "type": "post",
                        "dataType": 'json',
                        "error": function(XMLHttpRequest, textStatus, errorThrown) {
                            $('#addTopic-btn').attr('disabled', false);
                            $('#addTopic-btn').html('添加并创建话题');
                            alert(XMLHttpRequest.responseText);
                            // alert("服务器未正常响应，请重试");
                        },
                        "success": function(response) {
                            if (response.status == 0) {
                                document.getElementById("Form").reset();
                                toastr.success('创建成功了，再去新页面编辑一下吧');
                                $('#addTopic-btn').attr('disabled', false);
                                $('#addTopic-btn').html('添加并创建话题');

                                $('#openWin').attr('href', '/admin/topic/edit?action=edit&id=' + response.data);
                                $('#openWin')[0].click();
                            }
                        }
                    });
                });
            }
        }


        var setQuery = function() {
            var query = $("#sight_name").val() + ' ' + tagStr + ' ' + keywordStr + ' ';
            var action = $("#Form").attr('action');
            var site = '';
            if (action == sogou) {
                $('#Type').attr('name', "type");
                $('#Type').val('2');
                $('#query').attr('name', 'query');
                $('#query').val(query);
            } else if (action == baidu) {
                $('#Type').attr('name', "tn");
                $('#Type').val('baidu');
                $('#query').attr('name', 'wd');
                //$('#from_name').attr('data-url')?site='site:'+$('#from_name').attr('data-url')+' ':'';
                $('#query').val(query + '-(日游)');

                /*if ($('#query').val().getBytes() > 76) {
                    alert('请控制关键词在38个汉字以内(一个汉字相当于两个字母或数字)');
                }*/
            }
            return query;
        }

        var setSite = function(url, type) {
            switch (type) {
                case '1':
                    site_array_weixin.push(url);
                    break;
                case '2':
                    site_array_site.push(url);
                    break;
            }

        }

        /********************************************************************  
         **  
         **比较通用的正则表达式，捕获url各个部分。  
         **注意各部分基本上都包含了相应的符号，例如端口号如果捕获成功，那就是':80'  
         **函数返回一个正则表达式捕获数组。  
         **注意，现在获得的是一个数组，所以需要通过arr[i]的方式引用。  
         **正则表达式所有的匹配说明::  
         **$0  
         **整个url本身。如果$0==null，那就是我的正则有意外，未捕获的可能。  
         **有一种未捕获的情况已经被发现，那就是域名后面没有以'/'结尾，如：'http://localhost'  
         **但是经过我的测试，IE和firefox会自动把域名后面加上'/'的。  
         **$1-$4  协议，域名，端口号，还有最重要的路径path！  
         **$5-$7  文件名，锚点(#top)，query参数(?id=55)  
         **  
         *********************************************************************/
        var getFromDomain = function(url) {
            //如果加上/g参数，那么只返回$0匹配。也就是说arr.length = 0   
            var re = /(\w+):\/\/([^\:|\/]+)(\:\d*)?(.*\/)([^#|\?|\n]+)?(#.*)?(\?.*)?/i;
            //re.exec(url);   
            var arr = url.match(re);
            var domain = arr[2];
            if (domain == 'mp.weixin.qq.com') {
                return domain;
            }
            var strarr = domain.split('.');
            return strarr[strarr.length - 2] + '.' + strarr[strarr.length - 1];
        }


        return {
            init: function() {
                bindEvents.init();
                setQuery();
            }
        }
    }

    new Filter().init();

});
