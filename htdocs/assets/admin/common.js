 var Common = function() {

     var changepwd = function() {
         // 密码验证   
         jQuery.validator.addMethod("checkPasswd", function(value, element) {
             var testPwd = /^[a-zA-Z0-9!@#$%^&'\(\({}=+\-]{6,20}$/;
             return this.optional(element) || (testPwd.test(value));
         }, "密码只能为6-32位数字，字母及常用符号组成");

         //修改管理员登录密码
         $.validator.setDefaults({
             submitHandler: function(data) {
                 //序列化表单  
                 var param = $("#changepwd-form").serializeObject();
                 var changepwd = new Remoter('/admin/AdminUserapi/changepwd');
                 changepwd.remote(param);
                 changepwd.on('success', function(data) {
                     toastr.success('密码修改成功！');
                     $('#pwdModal').modal('hide');
                 });

             }
         });
         // validate signup form on keyup and submit
         validate = $("#changepwd-form").validate({
             rules: {
                 oldpasswd: "required",
                 passwd: {
                     required: true,
                     checkPasswd: true
                 },
                 passwd2: {
                     required: true,
                     equalTo: "#passwd"
                 }
             },
             messages: {
                 oldpasswd: "原密码不能为空！",
                 passwd: {
                     required: "新密码不能为空哦！",
                     //checkPasswd: "密码只能为6-32位数字，字母及常用符号组成"
                 },
                 passwd2: {
                     required: "两次密码必须一致！",
                     equalTo: "两次密码必须一致！"
                 }
             }
         });

         //新密码格式验证

     }

     var startTime = function(argument) {
         //当前时间 
         var today = new Date();
         var h = today.getHours();
         var m = today.getMinutes();
         var s = today.getSeconds();
         // add a zero in front of numbers<10
         m = checkTime(m);
         s = checkTime(s);
         document.getElementById('smalltime').innerHTML = h + ":" + m + ":" + s;
         t = setTimeout(function() {
             startTime()
         }, 500);

         function checkTime(i) {
             if (i < 10) {
                 i = "0" + i;
             }
             return i;
         }
     }
     return {
         init: function() {
             this.bindEvents();
         },
         bindEvents: function() {
             changepwd();
             startTime();
         }

     }
 }()
