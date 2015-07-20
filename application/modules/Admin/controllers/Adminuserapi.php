<?php
/**
 * 城市管理相关操作
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
    	 $name =isset($_REQUEST['name'])?$_REQUEST['name']:''; 
    	 $password=isset($_REQUEST['password'])?$_REQUEST['password']:''; 
         if(empty($name)){ 
             return $this->ajaxError(Admin_RetCode::USERNAME_EMPTY, Admin_RetCode::getMsg(Admin_RetCode::USERNAME_EMPTY));
         }
         if(empty($password)){ 
             return $this->ajaxError(Admin_RetCode::PASSWORD_EMPTY, Admin_RetCode::getMsg(Admin_RetCode::PASSWORD_EMPTY));
         }  
    	 $dbRet = Admin_Api::login($name,$password);
    	 if ($dbRet) {
    	     return $this->redirect('/admin');
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
}