/*
  话题筛选器 
  fyy 2015.7.22
 */
$(document).ready(function() {
    var baidu = "http://www.baidu.com/s";
    var sogou = "http://weixin.sogou.com/weixin";

    var tagStr = '';
    var tag_idArray = [];
    var keywordStr = '';

    bindEvents();


    /**
     * 绑定事件
     *  
     */
    function bindEvents() {
        //工具提示框
        $('[data-toggle="tooltip"]').tooltip();


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
                    "url": "/admin/keywordsapi/getKeywordsBySightId",
                    "data": {
                        "sight_id": val
                    },
                    "type": "post",
                    "error": function(e) {
                        alert("服务器未正常响应，请重试");
                    },
                    "success": function(response) {
                        if (response.status == 0) {
                            var option;
                            var data = response.data;
                            for (var i = 0; i < data.length; i++) {
                                option = option + '<option value="' + data.url + '">' + data.name + '</option>';
                            }
                            $('#keywords').html(option);
                            $('#keywords').multiSelect({
                                selectableHeader: '<span class="label label-primary">景点词条库</span>',
                                selectionHeader: '<span class="label label-success">已选词条</span>',
                                itemSelected: function(item, val, text) {
                                    keywordsStr = '';
                                    $('#keywords option:selected').each(function() {
                                        keywordsStr = keywordsStr + ' ' + $(this).text();
                                    });
                                    setQuery();
                                }
                            });
                            $('#form-group-keywords').show();
                        }
                    }
                });

            }
        });

        //景点框后的清除按钮，清除所选的景点
        $('#clear-sight').click(function(event) {
            $("#sight_name").val('');
            $("#sight_id").val('');
            setQuery();
        });

        //标签选择
        $('#tags').multiSelect({
            selectableHeader: '<span class="label label-primary">标签库</span>',
            selectionHeader: '<span class="label label-success">已选标签</span>',
            itemSelected: function(item, val, text) {
                tagStr = '';
                tag_idArray = [];
                $('#tags option:selected').each(function() {
                    tagStr = tagStr + ' ' + $(this).text();
                    tag_idArray.push(Number($(this).val()));
                });
                setQuery();
            }
        });

        //选择不同的搜索来源
        $('#form-from').change(function(event) {
            if ($(this).val() == "1" || $(this).val() == "2") {
                $("#Form").attr('action', sogou);
            } else {
                $("#Form").attr('action', baidu);
            }
            setQuery();
        });
        //搜索来源下拉列表 
        $('#form-from').selectpicker();


        //搜索事件
        $('#search-btn,#search-btn2').click(function(event) {

            $("#Form").submit();
        });

        //点击创建话题
        $('#addTopic-btn').click(function(event) {
            //组装话题参数
            var params = {
                from: $('#form-from option:selected').attr('data-id'),
                sights: [Number($('#sight_id').val())],
                tags: tag_idArray,
                url: $('#url').val(),
                status:1 //未发布的状态
            } 
            $.ajax({
                "url": "/admin/topicapi/add",
                "data": params,
                "type": "post",
                "error": function(e) {
                    alert("服务器未正常响应，请重试");
                },
                "success": function(response) {
                    if (response.status == 0) {
                         toastr.success('创建成功了，再去编辑一下吧');
                         
                    }
                }
            });
        });
    }


    function setQuery() {

        /*    var keywords;
            $('#keywords option:selected').each(function() {
                keywords = keywords + ' ' + $(this).text();
            });*/
        var query = $("#sight_name").val() + ' ' + tagStr + ' ' + keywordStr;
        var action = $("#Form").attr('action');
        if (action == sogou) {
            $('#Type').attr('name', "type");
            $('#Type').val($('#form-from').val());
            $('#query').attr('name', 'query');
            $('#query').val(query);
        } else if (action == baidu) {
            $('#Type').attr('name', "tn");
            $('#Type').val('baidu');
            $('#query').attr('name', 'wd');
            $('#query').val('site:' + $('#form-from').val() + ' ' + query);
        }
    }


});
