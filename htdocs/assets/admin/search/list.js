/*

 用户评论列表
  author:fyy
 */
    function del(labelId,objId){
    	//删除标签
        var del = new Remoter('/admin/searchapi/delLabel');
        del.remote({
          labelId: labelId,
          objId:objId
        });
        del.on('success', function(data) {
        //刷新当前页
        location.reload() ;
        });  
    }
$(document).ready(function() {
    var FORMATER = 'YYYY-MM-DD HH:mm:ss';
    var oTable = $('#editable').dataTable({
        "serverSide": true, //分页，取数据等等的都放到服务端去
        "processing": true, //载入数据的时候是否显示“载入中”
        "pageLength": 10, //首次加载的数据条数  
        "searching": false, //是否开启本地分页
        "ordering": false,
        "ajax": {
            "url": "/admin/searchapi/list",
            "type": "POST",
            "data": function(d) {
                //添加额外的参数传给服务器 
                if ($("#search-id").val()) {
                    d.id = Number($.trim($("#search-id").val()));
                }
            }
        },
        "columnDefs": [{
            "targets": [0],
            "visible": true,
            "searchable": false
        }, {
            "targets": [1],
            "orderable": false,
            "width": 20
        }],
        "columns": [ {
            "data": 'id'
        },{
            "data": 'name'
        },{
            "data": 'type'
        },{
            "data": 'obj_id'
        }, {
            "data": 'obj'
        },  {
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
                return '<button type="button" class="btn btn-success btn-xs addObj"  title="添加对象" data-toggle="tooltip"><i class="fa fa-buysellads"></i></button>'+'<a class="btn btn-danger btn-xs" title="删除" data-toggle="tooltip" onclick="del('+e.id+','+e.obj_id+')"> <i class="fa fa-remove"></i></a>';
                
            }
        }],
        "initComplete": function(setting, json) {
            //工具提示框
            //$('[data-toggle="tooltip"]').tooltip();
        }
    });

    var api = oTable.api();
    filters();
    bindEvents();
	addObj(type);

    function bindEvents() {
        //绑定draw事件
        $('#editable').on('draw.dt', function() {
            //工具提示框
            $('[data-toggle="tooltip"]').tooltip();
        });

        //状态下拉列表 
        $('#form-status,#form-type').selectpicker();

        //删除标签
        $('#sight_alert').delegate('.close', 'click', function(event) {
        	var id = $(this).attr('data-id');
        	var publish = new Remoter('/admin/searchapi/delTag');
            publish.remote({
            	tagId: id,
            });
            publish.on('success', function(data) {
                //刷新当前页
            	location.reload() ;
            });  
        });
        
        //选择标签
        $('#sight_alert').delegate('.click', 'click', function(event) {
        	var id = $(this).attr('data-id');
        	$("#search-id").val(id);
        	 api.ajax.reload();
         });

        //点击打开添加词条模态框
        $("#editable button.addObj").live('click', function(event) {
            var nRow = $(this).parents('tr')[0];
            var data = oTable.api().row(nRow).data();
            $('#name').val('');
            $('#type').val(event.type);
            $('#Form input').removeClass('error');
            $('#Form .error').hide();
            //打开模态框 
            $('#myModal').modal({});
        });

    }

    function addObj(type) {
        $.validator.setDefaults({
            submitHandler: function(data) {
                //序列化表单  
                var param = $("#Form").serializeObject();
                $.ajax({
                    "url": "/admin/searchapi/addLabel",
                    "data": param,
                    "type": "post",
                    "dataType": "json",
                    "error": function(e) {
                        alert("服务器未正常响应，请重试");
                    },
                    "success": function(response) {
                    	alert("服务器未正常响应，请重试");
                        if (response.status == 0) {
                            toastr.success('保存成功');
                            //手工关闭模态框
                            $('#myModal').modal('hide');
                        }
                    }
                });

            }
        });
    }
    
    function filters() {
        $('#form-status,#form-type').change(function(event) {
            //触发dt的重新加载数据的方法
            api.ajax.reload();
        });
    }

    /**
     * 打开详情
     * @param  {[type]} oTable [description]
     * @param  {[type]} nTr    [description]
     * @return {[type]}        [description]
     */
    function fnFormatDetails(oTable, nTr) {
        // return moment.unix(e.update_time).format(FORMATER);
        var aData = oTable.fnGetData(nTr);
        var sOut = '<table cellpadding="5" cellspacing="0" border="0" width="100%">';
        sOut += '<tr><td>消息内容：:' + aData.content+ '</td></tr>';
        sOut += '</table>';
        return sOut;
    }
});
