(function(window, document, undefined) {
    var newBtn = '<button type="button" class="btn btn-success btn-xs save"  title="保存" data-toggle="tooltip"><i class="fa fa-save"></i></button>' + '<button type="button" class="btn btn-danger btn-xs cancel"  title="取消" data-toggle="tooltip" data-mode="new"><i class="fa fa-remove"></i></button>';
    var saveBtn = '<button type="button" class="btn btn-success btn-xs save"  title="保存" data-toggle="tooltip"><i class="fa fa-save"></i></button>' + '<button type="button" class="btn btn-danger btn-xs cancel"  title="取消" data-toggle="tooltip"><i class="fa fa-remove"></i></button>';
    var editBtn = '<button type="button" class="btn btn-primary btn-xs edit" title="编辑" data-toggle="tooltip" data-toggle="modal"  data-target="#map-modal" href="/admin/utils/map" ><i class="fa fa-pencil"></i></button>';
    var currentRow = null;
    var oldXY = {};
    var oTable = $('#editable').dataTable({
        "serverSide": true, //分页，取数据等等的都放到服务端去
        "processing": true, //载入数据的时候是否显示“载入中”
        "pageLength": 10, //首次加载的数据条数  
        "searching": false,
        "ordering":false,
        "ajax": {
            "url": "/admin/cityapi/list",
            "type": "POST",
            "data": function ( d ) {
                   //添加额外的参数传给服务器
                   d.pid = $("#form-province").attr('data-pid');
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
            "data": "pidname"
        }, {
            "data": "name"
        }, {
            "data": "x"
        }, {
            "data": "y"
        }, {
            "data": function(e) {
                return editBtn;
            }
        }],
        "initComplete": function(setting, json) {

        }
    });
    
    var api=oTable.api();

    //绑定draw事件
    $('#editable').on('draw.dt', function() {
        //工具提示框
        $('[data-toggle="tooltip"]').tooltip();
    });

    // 模态框从远端的数据源加载完数据之后触发该事件
    $('#map-modal').on('loaded.bs.modal', function(e) {
        var data = oTable.api().row(currentRow).data();
        oldXY.x = data.x;
        oldXY.y = data.y;
        $("#txtSearch").val(data.name);
        $("#cityName").val(data.name);
        $('#map-modal .btn-search').click();
    });

    //模态框 点击确定之后立即触发该事件。
    $('#map-modal').delegate('.btn-submit', 'click', function(event) {
        var valXY = $.trim($("#txtCoordinate").val());
        if (!valXY) {
            alert('还没有选定坐标呢');
            return;
        }
        var arrayXy = valXY.split(',');
        var jqTds = $('>td', currentRow);
        jqTds[2].innerHTML = '<input type="text" value="' + arrayXy[0] + '" disabled="true"/>';
        jqTds[3].innerHTML = '<input type="text" value="' + arrayXy[1] + '" disabled="true"/>';
        jqTds[4].innerHTML = saveBtn;

        //工具提示框
        $('[data-toggle="tooltip"]').tooltip();
        
        //手工关闭模态框
        $('#map-modal').modal('hide');
    });

    $('#editable').delegate('button.edit', 'click', function(event) {
        currentRow = $(this).parents('tr')[0];
        //打开模态框
        $('#map-modal').modal({
            remote: '/admin/utils/map'
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
            oTable.fnReloadAjax(oTable.fnSettings());
        }
    });

    $('#editable button.save').live('click', function(e) {
        e.preventDefault();
        var nRow = $(this).parents('tr')[0];
        //保存
        saveRow(oTable, nRow);

    });


    //表格筛选开始
    //输入框自动完成
    $('#form-province').typeahead({
        display: 'name',
        val: 'id',
        ajax: {
            url: '/admin/cityapi/getProvinceList', 
            triggerLength: 1
        },
        itemSelected:function(item,val,text){ 
           /* item: the HTML element that was selected
            val: value of the *val* property
            text: value of the *display* property*/
            $("#form-province").val(text);
            $("#form-province").attr('data-pid',val);
            //触发dt的重新加载数据的方法
            api.ajax.reload();
        } 
    }); 
/*    $('#form-search').click(function(event) {
         //触发dt的重新加载数据的方法
           api.ajax.reload();
           //获取dt请求参数
           var args = api.ajax.params();
          // console.log("额外传到后台的参数值extra_search为："+args.pid);
    });*/
    //表格筛选结束
    function saveRow(oTable, nRow) {
        restoreRow(oTable, nRow);
        var data = oTable.api().row(nRow).data();
        $.ajax({
            "url": "/admin/Cityapi/save",
            "data": data,
            "type": "post",
            "error": function(e) {
                alert("服务器未正常响应，请重试");
            },
            "success": function(response) {
                if (response.status == 0) {
                    alert('保存成功');
                }
                //oTable.fnDraw();
            }
        });
    }


    function restoreRow(oTable, nRow) {
        var tds = nRow.childNodes;
        $.each(tds, function(i, val) {
            var jqob = $(val);
            //把input变为字符串
            if (jqob.has('input').length) {
                var txt = jqob.children("input").val();
                jqob.html(txt);
                oTable.api().cell(jqob).data(txt); //修改DataTables对象的数据
            } else if (jqob.has('button').length) {
                //把操作变成编辑按钮
                oTable.api().cell(jqob).data(editBtn);
            }
        });

    }
}(window, document));

 