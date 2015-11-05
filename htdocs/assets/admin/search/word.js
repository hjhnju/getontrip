/*

 搜索标签列表
  author:fyy
 */

$(document).ready(function() {
    var List = function() {
        var FORMATER = 'YYYY-MM-DD HH:mm:ss';
        /**
         * 初始化表格 
         */
        var initTable = function() {
            oTable = $('#editable').dataTable({
                "serverSide": true, //分页，取数据等等的都放到服务端去
                "processing": true, //载入数据的时候是否显示“载入中”
                "pageLength": 10, //首次加载的数据条数  
                "searching": false, //是否开启本地分页
                "ordering": false,
                "ajax": {
                    "url": "/admin/searchapi/wordList",
                    "type": "POST",
                    "data": function(d) {
                        //添加额外的参数传给服务器 
                        if ($("#search-id").val()) {
                            d.id = Number($.trim($("#search-id").val()));
                        }
                    }
                },
                "columnDefs": [ ],
                "columns": [{
                    "data": 'word'
                }, {
                    "data": 'statusName'
                }, {
                    "data": function(e) {
                        return '<button class="btn btn-primary btn-xs status" data-action="AUDITPASS"  title="通过审核" data-toggle="tooltip" >通过审核</button>'+ '<button class="btn btn-danger btn-xs status" data-action="AUDITFAILED" title="未通过审核" data-toggle="tooltip" >未通过审核</button>';

                    }
                }],
                "initComplete": function(setting, json) {
                    //工具提示框
                    //$('[data-toggle="tooltip"]').tooltip();
                }
            });
            api = oTable.api();
        }

        /**
         * 绑定事件
         *  
         */
        var bindEvents = {
            init: function() {
                this.addObj();
                //绑定draw事件
                $('#editable').on('draw.dt', function() {
                    //工具提示框
                    $('[data-toggle="tooltip"]').tooltip();
                }); 

                 

                //修改状态
                $('#editable').delegate('button.status', 'click', function(event) { 
                    var nRow = $(this).parents('tr')[0];
                    var data = oTable.api().row(nRow).data();
                    var change = new Remoter('/admin/searchapi/changeWordStatus');
                    change.remote({
                        word: data.word,
                        action: $(this).attr('data-action')
                    });
                    change.on('success', function(data) {
                        //刷新当前页
                        oTable.fnRefresh();
                    });
                });
  
            },
            addObj: function() {
                $.validator.setDefaults({
                    submitHandler: function(data) {
                        //序列化表单  
                        var param = $("#myModal #Form").serializeObject();
                        $.ajax({
                            "url": "/admin/searchapi/addLabel",
                            "data": param,
                            "type": "post",
                            "dataType": "json",
                            "error": function(e) {
                                alert("服务器未正常响应，请重试");
                            },
                            "success": function(response) {
                                if (response.status == 0) {
                                    toastr.success('保存成功');
                                    //手工关闭模态框
                                    $('#myModal').modal('hide');
                                    //刷新当前页
                                    oTable.fnRefresh();
                                } else {
                                    alert(response.statusInfo);
                                }
                            }
                        });

                    }
                });
                validations();



                /*
                  表单验证
                 */
                function validations() {
                    // validate signup form on keyup and submit
                    validate = $("#Form").validate({
                        rules: {
                            obj_id: "required"
                        },
                        messages: {
                            obj_id: "对象不能为空！"
                        }
                    });
                }
            }
        }

        /*
          过滤事件
         */
        var filter = function() {
            $('#form-status,#form-type').change(function(event) {
                //触发dt的重新加载数据的方法
                api.ajax.reload();
            });

            //选择标签
                $('#label_sortable').delegate('.click', 'click', function(event) {
                    var id = $(this).attr('data-id');
                    $("#search-id").val(id);
                    api.ajax.reload();
                });
        }


        /**
         * 打开详情
         * @param  {[type]} oTable [description]
         * @param  {[type]} nTr    [description]
         * @return {[type]}        [description]
         */
        var fnFormatDetails = function(oTable, nTr) {
            // return moment.unix(e.update_time).format(FORMATER);
            var aData = oTable.fnGetData(nTr);
            var sOut = '<table cellpadding="5" cellspacing="0" border="0" width="100%">';
            sOut += '<tr><td>消息内容：:' + aData.content + '</td></tr>';
            sOut += '</table>';
            return sOut;
        }
        return {
            init: function() {
                initTable()
                bindEvents.init();
                filter();
            }
        }
    }
    new List().init();

});
