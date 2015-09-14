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

     /**
     * 重置登录密码
     * @return [type] [description]
     */
    public function setpwdAction()
    { 
        $userid=isset($_REQUEST['id'])?$_REQUEST['id']:''; 
        if (empty($userid)) {
            return $this->ajaxError(Base_RetCode::PARAM_ERROR);
        }
        //修改新密码
        $passwd   = Base_Util_Secure::encrypt('Asd123');
        $dbRet = Admin_Api::editAdmin($userid,array('passwd'=>$passwd));
        if ($dbRet) {  
            return $this->ajax(); 
        }
        return $this->ajaxError(); 
    }

    public function listAction()
    { 
         //第一条数据的起始位置，比如0代表第一条数据
        //
        $start =isset($_REQUEST['start'])?$_REQUEST['start']:0; 
        $pageSize = isset($_REQUEST['length'])?$_REQUEST['length']:PHP_INT_MAX; 
        $page = ($start/$pageSize)+1; 
         
        $arrParams = isset($_REQUEST['params'])?$_REQUEST['params']:array();

        $List = Admin_Api::listAdmin($page, $pageSize, $arrParams);;

        $tmpList=$List['list'];

        //添加城市名称
       
        if (count($tmpList)>0) { 
            for($i=0;$i<count($tmpList);$i++){ 

            //处理角色名称
            $tmpList[$i]['role_name'] = Admin_Type_Role::getTypeName($tmpList[$i]['role']); 
           }
        } 

        $List['list']=$tmpList; 
         
    
        $retList['recordsFiltered'] =$List['total'];
        $retList['recordsTotal'] = $List['total']; 
        $retList['data'] =$List['list'];
 
        return $this->ajax($retList);
    }
 

     /**
     * 编辑保存
     */
    function saveAction(){
        $id =isset($_REQUEST['id'])?$_REQUEST['id']:'';
        if($id==''){
            $dbRet=Admin_Api::addAdmin($_REQUEST);
        }
        else{ 
          $dbRet=Admin_Api::editAdmin($id,$_REQUEST);
        }
        if ($dbRet) {
            return $this->ajax();
        }
        return $this->ajaxError();
    }

    /**
     * 编辑保存
    */
    function delAction(){
        $id =isset($_REQUEST['id'])?$_REQUEST['id']:'';
        if($id==''){
           return $this->ajaxError();
        }
         
        $dbRet=Admin_Api::delAdmin($id,$id);
        
        if ($dbRet) {
            return $this->ajax();
        }
        return $this->ajaxError();
    }
}