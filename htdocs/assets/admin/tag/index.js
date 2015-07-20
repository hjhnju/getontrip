$(document).ready(function() {
    var newBtn = '<button type="button" class="btn btn-success btn-xs save"  title="保存" data-toggle="tooltip"><i class="fa fa-save"></i></button>' + '<button type="button" class="btn btn-danger btn-xs cancel"  title="取消" data-toggle="tooltip" data-mode="new"><i class="fa fa-remove"></i></button>';
    var saveBtn = '<button type="button" class="btn btn-success btn-xs save"  title="保存" data-toggle="tooltip"><i class="fa fa-save"></i></button>' + '<button type="button" class="btn btn-danger btn-xs cancel"  title="取消" data-toggle="tooltip"><i class="fa fa-remove"></i></button>';
    var editBtn = '<button type="button" class="btn btn-primary btn-xs edit" title="编辑" data-toggle="tooltip"><i class="fa fa-pencil"></i></button>' + '<button type="button" class="btn btn-danger btn-xs delete"  title="删除" data-toggle="tooltip"><i class="fa fa-trash-o "></i></button>';
    var oTable = $('#editable').dataTable({
        "serverSide": true, //分页，取数据等等的都放到服务端去
        "processing": true, //载入数据的时候是否显示“载入中”
        "pageLength": 10, //首次加载的数据条数  
        "searching": false, //是否开启本地分页
        "ajax": {
            "url": "/admin/Tagapi/list",
            "type": "POST",
            "data": {

            }
        },
        "columnDefs": [{
            "targets": [0],
            "visible": false,
            "searchable": false
        }],
        "columns": [{
            "data": "id"
        }, {
            "data": "name"
        }, {
            "data": function(e) {
                if (e.create_time) {
                    //默认是/Date(794851200000)/格式，需要显示成年月日方式
                    // return new Date(Number(e.create_time.replace(/\D/g, ''))).toLocaleDateString();
                    return new Date(Number(e.create_time)).toLocaleDateString();

                }
                return "空";
            }
        }, {
            "data": function(e) {
                if (e.update_time) {
                    return new Date(Number(e.update_time)).toLocaleDateString();

                }
                return "空";
            }
        }, {
            "data": function(e) {
                return editBtn;
            }
        }],
        "initComplete": function(setting, json) {
            //工具提示框
            //$('[data-toggle="tooltip"]').tooltip();
        }
    });



    $('#editable-new').click(function(e) {
        e.preventDefault();
        var aiNew = oTable.fnAddData({
            "id": '',
            "name": '',
            "create_time": '',
            "update_time": '',
            "opt": 'new'
        }, false);

        var nRow = oTable.fnGetNodes(aiNew[0]);
        oTable.prepend(nRow);
        editRow(oTable, nRow);
        nEditing = nRow;
    });

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
                alert('删除成功!');
                oTable.fnDeleteRow(nRow);
                oTable.fnDraw();
            }
        }); 
    });

    $('#editable button.cancel').live('click', function(e) {
        e.preventDefault();
        var nRow = $(this).parents('tr')[0];
        if ($(this).attr("data-mode") == "new") {
            oTable.fnDeleteRow(nRow, function() {
                nRow.remove();
            }, false);
        } else {
            restoreRow(oTable, nRow);
        }
    });
    $('#editable button.edit,#editable button.save').live('click', function(e) {
        e.preventDefault();

        var nRow = $(this).parents('tr')[0];
        var saveAction = $(this).hasClass('save');
        if (saveAction) {
            //保存
            saveRow(oTable, nRow);
        } else {
            //编辑
            editRow(oTable, nRow);
        }

    });


    function editRow(oTable, nRow) {
        var aData = oTable.fnGetData(nRow);
        var jqTds = $('>td', nRow);
        jqTds[0].innerHTML = '<input type="text" class="form-control small" value="' + aData["name"] + '">';
        jqTds[1].innerHTML = '';
        jqTds[2].innerHTML = '';
        jqTds[3].innerHTML = aData.opt == "new" ? newBtn : saveBtn;

    }

    function saveRow(oTable, nRow) {
        //var jqInputs = $('input', nRow);
        // oTable.fnUpdate(jqInputs[0].value, nRow, 1, false); 

        restoreRow(oTable, nRow);
        var data = oTable.api().row(nRow).data();

        $.ajax({
            "url": "/admin/Tagapi/save",
            "data": data,
            "type": "post",
            "error": function(e) {
                alert("服务器未正常响应，请重试");
            },
            "success": function(response) {
                if (response.status == 0) {

                    alert("保存成功！");
                    oTable.fnDraw();
                }
            }
        });
    }


    function restoreRow(oTable, nRow) {
        /*  var aData = oTable.fnGetData(nRow);
          var jqTds = $('>td', nRow);

          for (var i = 0, iLen = jqTds.length; i < iLen; i++) {
              oTable.fnUpdate(aData[i], nRow, i, false);
          }*/
        var tds = nRow.childNodes;
        $.each(tds, function(i, val) {
            var jqob = $(val);
            //把input变为字符串
            if (!jqob.has('button').length) {
                var txt = jqob.children("input").val();
                jqob.html(txt);
                oTable.api().cell(jqob).data(txt); //修改DataTables对象的数据
            } else {
                //把操作变成编辑按钮
                oTable.api().cell(jqob).data(newBtn);
            }
        });

    }
});
