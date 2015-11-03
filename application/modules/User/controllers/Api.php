<?php
/**
 * 用户登录及信息修改相关操作
 */
class ApiController extends Base_Controller_Page{
    
    protected $logicLogin;
    
    protected $logicUser;
    
    public function init(){
        //未登录不跳转
        $this->setNeedLogin(false);

        parent::init();
        
        $this->logicLogin = new User_Logic_Third();
        
        $this->logicUser  = new User_Logic_User();
    } 
    
    /**
     * 接口1：/api/user/login
     * 登录接口
     * @param string openId
     * @param integer type,第三方登录类型，1:qq,2:weixin,3:weibo
     * @return json
     * 设置用户的登录态
     */
    public function loginAction(){
        $openId   = isset($_REQUEST['openId'])?trim($_REQUEST['openId']):'';
        $type     = isset($_REQUEST['type'])?intval($_REQUEST['type']):'';
        if(empty($openId) || empty($type)){
            return $this->ajaxError(Base_RetCode::PARAM_ERROR,Base_RetCode::getMsg(Base_RetCode::PARAM_ERROR));
        }
        $ret = $this->logicLogin->setLogin($openId,$type);
        return $this->ajax($ret);
    }

    /**
     * 接口2：/api/user/signOut
     * 退出登录接口
     * @return json
     */
    public function signOutAction(){
        $ret = $this->logicLogin->signOut();
        return $this->ajax(strval($ret));
    }      
    
    /**
     * 接口3：/api/user/getinfo
     * 用户信息获取接口
     * @param integer type,第三方登录类型，1:qq,2:weixin,3:weibo
     * @return json
     */
    public function getinfoAction() {
        $userId   = User_Api::getCurrentUser();
        if(empty($userId)){
            return $this->ajaxError(User_RetCode::SESSION_NOT_LOGIN,User_RetCode::getMsg(User_RetCode::SESSION_NOT_LOGIN));
        }
        $type     = isset($_REQUEST['type'])?intval($_REQUEST['type']):'';
        if(empty($type)){
            return $this->ajaxError(Base_RetCode::PARAM_ERROR,Base_RetCode::getMsg(Base_RetCode::PARAM_ERROR));
        }
        $ret      = $this->logicUser->getUserInfo($userId, $type);
        $this->ajax($ret);
    }
    
    /**
     * 接口4：/api/user/addinfo
     * 用户信息添加接口
     * @param integer userid，用户ID
     * @param integer type,第三方登录类型，1:qq,2:weixin,3:weibo
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
        $type       = isset($_REQUEST['type'])?intval($_REQUEST['type']):'';
        $image      = isset($_REQUEST['image'])?trim($_REQUEST['image']):'';
        $name       = isset($_REQUEST['nick_name'])?trim($_REQUEST['nick_name']):'';
        $sex        = isset($_REQUEST['sex'])?intval($_REQUEST['sex']):'';
        $city       = isset($_REQUEST['city'])?trim($_REQUEST['city']):'';
        if(empty($type)){
            return $this->ajaxError(Base_RetCode::PARAM_ERROR,Base_RetCode::getMsg(Base_RetCode::PARAM_ERROR));
        }
        $arrParam = array(
            'nick_name' => $name,
            'image'     => $image,
            'sex'       => $sex,
            'city'      => $city,
        );
        $ret        = $this->logicUser->addUserInfo($userId, $type, $arrParam);
        return $this->ajax(strval($ret));
    }
    
    /**
     * 接口5：/api/user/editinfo
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
        $type       = isset($_REQUEST['type'])?intval($_REQUEST['type']):'';
        $name       = isset($_REQUEST['nick_name'])?trim($_REQUEST['nick_name']):'';
        $sex        = isset($_REQUEST['sex'])?intval($_REQUEST['sex']):'';
        $city       = isset($_REQUEST['city'])?trim($_REQUEST['city']):'';
        $file       = isset($_FILES['file'])?$_FILES['file']:'';
        if(empty($type)){
            return $this->ajaxError(Base_RetCode::PARAM_ERROR,Base_RetCode::getMsg(Base_RetCode::PARAM_ERROR));
        }
        $arrParam = array(
            'nick_name' => $name,
            'sex'       => $sex,
            'city'      => $city,
        );
        $ret        = $this->logicUser->editUserInfo($userId, $type, $arrParam, $file);
        return $this->ajax(strval($ret));
    }
    
    /**
     * 接口6：/api/user/checkLogin
     * 检查用户是否登录
     */
    public function checkLoginAction(){
        $ret = User_Api::getCurrentUser();
        return $this->ajax(strval($ret));
    }
}
