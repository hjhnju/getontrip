$(document).ready(function() {
   	$(document).keydown(function(event){ 
		if(event.keyCode == 13){ //绑定回车
		$('#loginBtn').click(); //自动触发登录按钮
		}
	}); 
    $('#loginBtn').click(function(event) { 
        $.ajax({
            "url": "/admin/AdminUserapi/login",
            "data": {
                name: $('input[name="name"]').val(),
                password: $('input[name="passwd"]').val()
            },
            "error": function(e) {
                alert("服务器未正常响应，请重试");
            },
            "success": function(response) {
                if (response.status == 0) {
                    window.location.href = "/admin";
                } else {
                    Login.alert(response.statusInfo);
                }
            }
        });
    });
});
