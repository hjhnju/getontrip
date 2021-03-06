$(document).ready(function() {
    var List = function() {
        var FORMATER = 'YYYY-MM-DD HH:mm:ss';
        var newBtn = '<button type="button" class="btn btn-success btn-xs save"  title="保存" data-toggle="tooltip"><i class="fa fa-save"></i></button>' + '<button type="button" class="btn btn-danger btn-xs cancel"  title="取消" data-toggle="tooltip" data-mode="new"><i class="fa fa-remove"></i></button>';
        var saveBtn = '<button type="button" class="btn btn-success btn-xs save"  title="保存" data-toggle="tooltip"><i class="fa fa-save"></i></button>' + '<button type="button" class="btn btn-danger btn-xs cancel"  title="取消" data-toggle="tooltip"><i class="fa fa-remove"></i></button>';
        var editBtn = '<button type="button" class="btn btn-primary btn-xs edit" title="编辑" data-toggle="tooltip"><i class="fa fa-pencil"></i></button>' + '<button type="button" class="btn btn-danger btn-xs delete"  title="删除" data-toggle="tooltip"><i class="fa fa-trash-o "></i></button>';
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
                    "url": "/admin/Tagapi/list",
                    "type": "POST",
                    "data": function(d) {
                        //添加额外的参数传给服务器
                        d.params = {};
                        if ($('#form-type').val()) {
                            d.params.type = $('#form-type').val();
                        }
                    }
                },
                "columnDefs": [{
                    "targets": [],
                    "visible": false,
                    "searchable": false
                }],
                "aoColumns": [

                    {
                        "mDataProp": "id"
                    }, {
                        "mDataProp": "name"
                    }, {
                        "mDataProp": "type",
                        "fnCreatedCell": function(nTd, sData, oData, iRow, iCol) {
                            $(nTd).html(oData.type_name);
                            $(nTd).addClass('selectTd').attr('id', 'td_' + sData + '_' + oData.type);
                        }
                    }, {
                        "mDataProp": "create_time",
                        "fnCreatedCell": function(nTd, sData, oData, iRow, iCol) {
                            if (oData.create_time) {
                                $(nTd).html(moment.unix(oData.create_time).format(FORMATER));
                            }
                            return "空";
                        }
                    }, {
                        "mDataProp": "update_time",
                        "fnCreatedCell": function(nTd, sData, oData, iRow, iCol) {
                            if (oData.update_time) {
                                $(nTd).html(moment.unix(oData.update_time).format(FORMATER));
                            }
                            return "空";
                        }
                    }, {
                        "mDataProp": "update_time",
                        "fnCreatedCell": function(nTd, sData, oData, iRow, iCol) {
                            $(nTd).html(editBtn);
                        }
                    },
                ],
                /*"columns": [{
                    "data": "id"
                }, {
                    "data": "name"
                }, {
                    "data": function(e) { 
                        if (e.type) {
                            return e.type_name+'/'+e.type;
                            $(nTd).html(oData.type_name);
                            $(nTd).addClass('selectTd').attr('id', 'td_' + sData + '_' + oData.type);
                        }
                    }
                }, {
                    "data": function(e) {
                        if (e.create_time) {
                            return moment.unix(e.create_time).format(FORMATER);
                        }
                        return "空";
                    }
                }, {
                    "data": function(e) {
                        if (e.update_time) {
                            return moment.unix(e.update_time).format(FORMATER);

                        }
                        return "空";
                    }
                }, {
                    "data": function(e) {
                        return editBtn;
                    }
                }],*/
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
                //状态下拉列表 
                $('#form-type').selectpicker();
               // $('#tag-type').selectpicker(); 


                //添加标签
                $('#editable-new').click(function(e) {
                    e.preventDefault();
                    var aiNew = oTable.fnAddData({
                        "id": '',
                        "name": '',
                        "type": '',
                        "create_time": '',
                        "update_time": '',
                        "opt": 'new'
                    }, false);

                    var nRow = oTable.fnGetNodes(aiNew[0]);
                    oTable.prepend(nRow);
                    bindEvents.editRow(oTable, nRow);
                    nEditing = nRow;
                });

                //删除标签
                $('#editable button.delete').live('click', function(e) {
                    e.preventDefault();
                    if (confirm("确定删除 ?") == false) {
                        return;
                    }
                    var nRow = $(this).parents('tr')[0];
                    var data = oTable.api().row(nRow).data();
                    $.ajax({
                        "url": "/admin/Tagapi/del",
                        "data": data,
                        "type": "post",
                        "error": function(e) {
                            alert("服务器未正常响应，请重试");
                        },
                        "success": function(response) {
                            if (response.status == 0) {
                                toastr.success('删除成功');
                                 //刷新当前页
                                oTable.fnRefresh();
                            }
                        }
                    });
                });

                //取消编辑标签
                $('#editable button.cancel').live('click', function(e) {
                    e.preventDefault();
                    var nRow = $(this).parents('tr')[0];
                    if ($(this).attr("data-mode") == "new") {
                        oTable.fnDeleteRow(nRow, function() {
                            nRow.remove();
                        }, false);
                    } else {
                        bindEvents.restoreRow(oTable, nRow);
                    }
                });

                //编辑标签
                $('#editable button.edit,#editable button.save').live('click', function(e) {
                    e.preventDefault();

                    var nRow = $(this).parents('tr')[0];
                    var saveAction = $(this).hasClass('save');
                    if (saveAction) {
                        //保存
                        bindEvents.saveRow(oTable, nRow);
                    } else {
                        //编辑
                        bindEvents.editRow(oTable, nRow);
                    }

                });
            },
            editRow: function(oTable, nRow) {
                var aData = oTable.fnGetData(nRow);
                var jqTds = $('>td', nRow);
                $select=$($('#tag-type').html()); 
                $select.attr('data-typeid',aData["type"]); 

                jqTds[0].innerHTML = aData["id"];
                jqTds[1].innerHTML = '<input type="text" class="form-control small" value="' + aData["name"] + '">';
                //jqTds[2].innerHTML = ' <select name="type" class="tag-type">' + '<option value="1">普通标签</option>' + '<option value="2">通用标签</option>' + '<option value="3">分类标签</option>' + '<option value="4">搜索标签</option>' + '</select>';
                jqTds[2].innerHTML = $select[0].outerHTML;
                jqTds[3].innerHTML = '';
                jqTds[4].innerHTML = '';
                jqTds[5].innerHTML = aData.opt == "new" ? newBtn : saveBtn;

                $('select[data-typeid="'+aData["type"]+'"]').val(aData["type"]);
                $('select[data-typeid="'+aData["type"]+'"]').selectpicker();  

            },

            saveRow: function(oTable, nRow) {
                //var jqInputs = $('input', nRow);
                // oTable.fnUpdate(jqInputs[0].value, nRow, 1, false); 

                bindEvents.restoreRow(oTable, nRow);
                var data = oTable.api().row(nRow).data();

                if (!data.type) {
                     toastr.warning('必须选择一个标签类型！');
                     return;
                };

                $.ajax({
                    "url": "/admin/Tagapi/save",
                    "data": {
                        'id': data.id ? data.id : '',
                        'name': data.name,
                        'type_name': data.type_name,
                        'type': data.type
                    },
                    "type": "post",
                    "error": function(e) {
                        alert("服务器未正常响应，请重试");
                    },
                    "success": function(response) {
                        if (response.status == 0) {
                            toastr.success('保存成功');
                            //刷新当前页
                            oTable.fnRefresh();
                        }
                    }
                });
            },


            restoreRow: function(oTable, nRow) {
                /*  var aData = oTable.fnGetData(nRow);
                  var jqTds = $('>td', nRow);

                  for (var i = 0, iLen = jqTds.length; i < iLen; i++) {
                      oTable.fnUpdate(aData[i], nRow, i, false);
                  }*/
                var tds = nRow.childNodes;
                $.each(tds, function(i, val) {
                    var jqob = $(val);
                    //把input变为字符串
                    if (!jqob.has('button').length && !jqob.has('select').length) {
                        var txt = jqob.children("input").val();
                        jqob.html(txt);
                        oTable.api().cell(jqob).data(txt); //修改DataTables对象的数据
                    } else if (jqob.has('select').length > 0) {
                        var val = jqob.children("select").val();
                        var txt = jqob.children("select").find("option:selected").text();
                        jqob.html(txt);

                        oTable.api().row(nRow).data().type = val;
                        oTable.api().row(nRow).data().type_name = txt;
                        //oTable.api().row(nRow).data(data); //修改DataTables对象的数据 
                    } else {
                        //把操作变成编辑按钮
                        oTable.api().cell(jqob).data(newBtn);
                    }
                });

            }
        }

        /*
             过滤事件
        */
        var filter = function() {
            $('#form-type').change(function(event) {
                //触发dt的重新加载数据的方法
                api.ajax.reload();
            });
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
