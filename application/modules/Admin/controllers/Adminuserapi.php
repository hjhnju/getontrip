<?php
/**
 * 城市管理相关操作
 * author :fyy
 */
class AdminuserapiController extends Base_Controller_Api{
      
     public function init() {
        parent::init();
    }

     /**
     * 后台登录
     *  
     */    
    public function loginAction(){  
         $homeUrl  = '/admin';
    	 $name =isset($_REQUEST['name'])?$_REQUEST['name']:''; 
    	 $password=isset($_REQUEST['password'])?$_REQUEST['password']:''; 
         $redirectUri   = isset($_REQUEST['redirectUri']) ? $_REQUEST['redirectUri'] : '';
         if(empty($name)){ 
             return $this->ajaxError(Admin_RetCode::USERNAME_EMPTY, Admin_RetCode::getMsg(Admin_RetCode::USERNAME_EMPTY));
         }
         if(empty($password)){ 
             return $this->ajaxError(Admin_RetCode::PASSWORD_EMPTY, Admin_RetCode::getMsg(Admin_RetCode::PASSWORD_EMPTY));
         }  
    	 $dbRet = Admin_Api::login($name,$password);
    	 if ($dbRet) { 
            if(!empty($redirectUri)){
               return $this->ajaxJump($redirectUri);
            }
    	    //return $this->ajax();
            return $this->ajaxJump($homeUrl); 
    	 } 
         return $this->ajaxError(Admin_RetCode::PASSWORD_WRONG, Admin_RetCode::getMsg(Admin_RetCode::PASSWORD_WRONG));

    }
    /**
     * 后台退出登录
     *  
     */    
    public function logoutAction(){  
         $logic   = new User_Logic_Login();
         $ret = $logic->signOut();
         $this->redirect('/admin/login');
    }
    
    /**
     * 修改登录密码
     * @return [type] [description]
     */
    public function changepwdAction()
    {
        $oldpasswd=isset($_REQUEST['oldpasswd'])?$_REQUEST['oldpasswd']:'';
        $passwd=isset($_REQUEST['passwd'])?$_REQUEST['passwd']:'';
        //判断是否登录 
        $logicUser = new User_Logic_Login();
        $userid = $logicUser->checkLogin();
 
        //判断原始密码是否正确
        $isOldPwd = Admin_Api::checkPasswd($userid, $oldpasswd);
        if(!$isOldPwd){
           return $this->ajaxError(400,'原密码不正确');
        }
        //修改新密码
        $passwd   = Base_Util_Secure::encrypt($passwd);
        $dbRet = Admin_Api::editAdmin($userid,array('passwd'=>$passwd));
        if ($dbRet) {  
            return $this->ajax(); 
        }
         return $this->ajaxError(); 
    }
}