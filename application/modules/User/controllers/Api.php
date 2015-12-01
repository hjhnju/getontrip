<?php
/**
 * 用户登录及信息修改相关操作
 */
class ApiController extends Base_Controller_Api{
    
    protected $logicLogin;
    
    protected $logicUser;
    
    protected $logicRegist;
    
    public function init(){
        //未登录不跳转
        $this->setNeedLogin(false);

        parent::init();
        
        $this->logicLogin  = new User_Logic_Third();
        
        $this->logicUser   = new User_Logic_User();
        
        $this->logicRegist = new User_Logic_Regist();
    } 
    
    /**
     * 接口1：/api/user/login
     * 第三方登录接口
     * @param string openId
     * @param integer type,第三方登录类型，1:qq,2:weixin,3:weibo
     * @return json
     * 第三方用户的登录态
     */
    public function loginAction(){
        $openId   = isset($_REQUEST['openId'])?trim($_REQUEST['openId']):'';
        $type     = isset($_REQUEST['type'])?intval($_REQUEST['type']):'';
        if(empty($openId) || empty($type)){
            return $this->ajaxError(User_RetCode::PARAM_ERROR,User_RetCode::getMsg(User_RetCode::PARAM_ERROR));
        }
        $ret      = $this->logicLogin->setThirdLogin($openId,$type);
        if($ret){
            return $this->ajaxError($ret,User_RetCode::getMsg($ret));
        }
        return $this->ajax($ret);
    }
    
    /**
     * 接口2：/api/user/login2
     * 邮箱登录接口
     * @param string email,邮箱
     * @param string passwd,密码
     * @return json
     * 普通的用户的登录态
     */
    public function login2Action(){
        $email   = isset($_REQUEST['email'])?trim($_REQUEST['email']):'';
        $passwd  = isset($_REQUEST['passwd'])?trim($_REQUEST['passwd']):'';
        if(empty($email) || empty($passwd)){
            return $this->ajaxError(User_RetCode::PARAM_ERROR,User_RetCode::getMsg(User_RetCode::PARAM_ERROR));
        }
        $ret     = $this->logicLogin->setNormalLogin($email,$passwd);
        if($ret){
            return $this->ajaxError($ret,User_RetCode::getMsg($ret));
        }
        return $this->ajax($ret);
    }

    /**
     * 接口3：/api/user/signOut
     * 退出登录接口
     * @return json
     */
    public function signOutAction(){
        $ret = $this->logicLogin->signOut();
        return $this->ajax(strval($ret));
    }      
    
    /**
     * 接口4：/api/user/getinfo
     * 用户信息获取接口
     * @param integer type,第三方登录类型，1:qq,2:weixin,3:weibo
     * @return json
     */
    public function getinfoAction() {
        $userId   = User_Api::getCurrentUser();
        if(empty($userId)){
            return $this->ajaxError(User_RetCode::SESSION_NOT_LOGIN,User_RetCode::getMsg(User_RetCode::SESSION_NOT_LOGIN));
        }
        $ret      = $this->logicUser->getUserInfo($userId);
        $this->ajax($ret);
    }
    
    /**
     * 接口5：/api/user/addinfo
     * 用户信息添加接口
     * @param integer userid，用户ID
     * @param string  nick_name,昵称
     * @param string  image,图像
     * @param integer sex,性别: 0男性,1:女性,2表示还不确定
     * @param string  city,城市
     * @return json
     */
    public function addinfoAction() {
        $userId     = User_Api::getCurrentUser();
        if(empty($userId)){
            return $this->ajaxError(User_RetCode::SESSION_NOT_LOGIN,User_RetCode::getMsg(User_RetCode::SESSION_NOT_LOGIN));
        }
        $image      = isset($_REQUEST['image'])?trim($_REQUEST['image']):'';
        $name       = isset($_REQUEST['nick_name'])?trim($_REQUEST['nick_name']):'';
        $sex        = isset($_REQUEST['sex'])?intval($_REQUEST['sex']):'';
        $city       = isset($_REQUEST['city'])?trim($_REQUEST['city']):'';
        $arrParam = array(
            'nick_name' => $name,
            'image'     => $image,
            'sex'       => $sex,
            'city'      => $city,
        );
        $ret        = $this->logicUser->addUserInfo($userId, $arrParam);
        if(!$ret){
            return $this->ajaxError(User_RetCode::UNKNOWN_ERROR,User_RetCode::getMsg(User_RetCode::UNKNOWN_ERROR));
        }
        return $this->ajax();
    }
    
    /**
     * 接口6：/api/user/editinfo
     * 用户信息修改接口
     * @param integer type,第三方登录类型，1:qq,2:weixin,3:weibo
     * @param string  nick_name,昵称
     * @param integer sex,性别: 0男性,1:女性,2表示还不确定
     * @param string  city,城市
     * @param file  file,上传的图像文件
     * @return json
     */
    public function editinfoAction(){
        $userId     = User_Api::getCurrentUser();
        if(empty($userId)){
            return $this->ajaxError(User_RetCode::SESSION_NOT_LOGIN,User_RetCode::getMsg(User_RetCode::SESSION_NOT_LOGIN));
        }
        $name       = isset($_REQUEST['nick_name'])?trim($_REQUEST['nick_name']):'';
        $sex        = isset($_REQUEST['sex'])?trim($_REQUEST['sex']):'';
        $city       = isset($_REQUEST['city'])?trim($_REQUEST['city']):'';
        $file       = isset($_FILES['file'])?$_FILES['file']:'';
        if(!empty($name)){
            $logicUser = new User_Logic_User();
            $ret = $logicUser->checkName($name);
            if($ret){
                return $this->ajaxError(User_RetCode::USERNAME_EXIST,User_RetCode::getMsg(User_RetCode::USERNAME_EXIST));
            }
        }
        $arrParam = array(
            'nick_name' => $name,
            'sex'       => $sex,
            'city'      => $city,
        );
        $ret        = $this->logicUser->editUserInfo($userId,$arrParam, $file);
        if($ret){
            return $this->ajax();
        }
        return $this->ajaxError();
    }
    
    /**
     * 接口7：/api/user/regist
     * 用户注册接口
     * @param string email,邮箱
     * @param string passwd,密码
     * @return json
     */
    public function registAction(){
        $email  = isset($_REQUEST['email'])?trim($_REQUEST['email']):'';
        $passwd = isset($_REQUEST['passwd'])?trim($_REQUEST['passwd']):'';
        $ret    = $this->logicRegist->regist($email,$passwd);
        if(!$ret){
            return $this->ajax();
        }
        return $this->ajaxError($ret,User_RetCode::getMsg($ret));
    }
    
    /**
     * 接口8：/api/user/sendPasswdEmail
     * 发送密码重置邮件
     * @param string email,邮箱
     * @return json
     */
    public function sendPasswdEmailAction(){
        $email  = isset($_REQUEST['email'])?trim($_REQUEST['email']):'';
        $ret    = $this->logicUser->sendEmail($email);
        if(!$ret){
           return $this->ajax();
        }
        return $this->ajaxError($ret,User_RetCode::getMsg($ret));
    }
}
