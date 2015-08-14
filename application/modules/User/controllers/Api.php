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
     * @param integer type,1:qq,2:weixin,3:weibo
     * @param string  deviceId
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
        return $this->ajax($ret);
    }

    /**
     * 接口2：/api/user/signOut
     * 退出登录接口
     * @return json
     */
    public function signOutAction(){
        $ret = $this->logicLogin->signOut();
        return $this->ajax($ret);
    }   
    
    /**
     * 接口3：/api/user/checkLogin
     * 检查用户是否登录
     * @return json
     */
    public function checkLoginAction(){
        $ret = $this->logicLogin->checkLogin();
        return $this->ajax($ret);
    }    
    
    /**
     * 接口4：/api/user/info
     * 用户信息获取接口
     * @param integer deviceId
     * @return json
     */
    public function infoAction() {
        $deviceId   = isset($_REQUEST['deviceId'])?trim($_REQUEST['deviceId']):'';
        if(empty($deviceId)){
            return $this->ajaxError(Base_RetCode::PARAM_ERROR,Base_RetCode::getMsg(Base_RetCode::PARAM_ERROR));
        }
        $ret        = $this->logicUser->getUserInfo($deviceId);
        $this->ajax($ret);
    }
    
    /**
     * 接口5：/api/user/edit
     * 用户信息修改接口
     * @param integer deviceId
     * @param string  param
     * @param array
     */
    public function editAction(){
        $deviceId   = isset($_REQUEST['deviceId'])?trim($_REQUEST['deviceId']):'';
        $strParam   = isset($_REQUEST['param'])?trim($_REQUEST['param']):'';
        if(empty($deviceId) || empty($strParam)){
            return $this->ajaxError(Base_RetCode::PARAM_ERROR,Base_RetCode::getMsg(Base_RetCode::PARAM_ERROR));
        }
        $ret        = $this->logicUser->editUserInfo($deviceId,$strParam);
        $this->ajax($ret);
    }
}
