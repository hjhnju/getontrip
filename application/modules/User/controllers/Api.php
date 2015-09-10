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
        
        $this->logicLogin = new User_Logic_Login();
        
        $this->logicUser  = new User_Logic_User();
    } 
    
    /**
     * 接口1：/api/user/login
     * 登录接口
     * @param integer openId
     * @param integer type,第三方登录类型，1:qq,2:weixin,3:weibo
     * @param string  deviceId，设备ID
     * @return json
     * 设置用户的登录态
     */
    public function loginAction(){
        $openId   = isset($_REQUEST['openId'])?intval($_REQUEST['openId']):'';
        $type     = isset($_REQUEST['type'])?intval($_REQUEST['type']):'';
        $deviceId = isset($_REQUEST['deviceId'])?trim($_REQUEST['deviceId']):'';
        if(empty($openId) || empty($type) || empty($deviceId)){
            return $this->ajaxError(Base_RetCode::PARAM_ERROR,Base_RetCode::getMsg(Base_RetCode::PARAM_ERROR));
        }
        $ret = $this->logicLogin->setLogin($openId,$type,$deviceId);
        if($ret){
            return $this->ajax();
        }
        return $this->ajaxError();
    }

    /**
     * 接口2：/api/user/signOut
     * 退出登录接口
     * @return json
     */
    public function signOutAction(){
        $ret = $this->logicLogin->signOut();
        if($ret){
            return $this->ajax();
        }
        return $this->ajaxError();
    }   
    
    /**
     * 接口3：/api/user/checkLogin
     * 检查用户是否登录
     * @return json
     */
    public function checkLoginAction(){
        $userId = $this->logicLogin->checkLogin();
        if($userId){
            return $this->ajax($userId);
        }
        return $this->ajaxError();
    }    
    
    /**
     * 接口4：/api/user/getinfo
     * 用户信息获取接口
     * @param integer userid，用户ID
     * @param integer type,第三方登录类型，1:qq,2:weixin,3:weibo
     * @return json
     */
    public function getinfoAction() {
        $userId   = isset($_REQUEST['userid'])?trim($_REQUEST['userid']):'';
        $type     = isset($_REQUEST['type'])?intval($_REQUEST['type']):'';
        if(empty($userId)){
            return $this->ajaxError(Base_RetCode::PARAM_ERROR,Base_RetCode::getMsg(Base_RetCode::PARAM_ERROR));
        }
        $ret      = $this->logicUser->getUserInfo($userId,$type);
        $this->ajax($ret);
    }
    
    /**
     * 接口5：/api/user/addinfo
     * 用户信息添加接口
     * @param integer userid，用户ID
     * @param string  param,eg: param=nick_name:aa,image:bb,sex:1
     * @return json
     */
    public function addinfoAction() {
        $userId     = isset($_REQUEST['userid'])?trim($_REQUEST['userid']):'';
        $strParam   = isset($_REQUEST['param'])?trim($_REQUEST['param']):'';
        if(empty($deviceId) || empty($strParam)){
            return $this->ajaxError(Base_RetCode::PARAM_ERROR,Base_RetCode::getMsg(Base_RetCode::PARAM_ERROR));
        }
        $ret        = $this->logicUser->addUserInfo($userId,$strParam);
        if($ret){
            return $this->ajax();
        }
        return $this->ajaxError();
    }
    
    /**
     * 接口6：/api/user/editinfo
     * 用户信息修改接口
     * @param integer userid
     * @param string  param,eg: param=nick_name:aa,image:bb,type:jpg,sex:1
     * @return json
     */
    public function editinfoAction(){
        $userId     = isset($_REQUEST['userid'])?trim($_REQUEST['userid']):'';
        $strParam   = isset($_REQUEST['param'])?trim($_REQUEST['param']):'';
        if(empty($deviceId) || empty($strParam)){
            return $this->ajaxError(Base_RetCode::PARAM_ERROR,Base_RetCode::getMsg(Base_RetCode::PARAM_ERROR));
        }
        $ret        = $this->logicUser->editUserInfo($userId,$strParam);
        if($ret){
            return $this->ajax();
        }
        return $this->ajaxError();
    }
}
