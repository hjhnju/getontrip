<?php
/**
 * 用户登录相关操作
 */
class LoginController extends Base_Controller_Page{
    
    protected $logicLogin;
    
    public function init(){
        //未登录不跳转
        $this->setNeedLogin(false);

        parent::init();
        
        $this->logicLogin = new User_Logic_Login();
    } 
    
    /**
     * 设置用户的登录态
     */
    public function indexAction(){
        $openId   = isset($_POST['openId'])?intval($_POST['openId']):'';
        $type     = isset($_POST['type'])?trim($_POST['type']):'';
        $deviceId = isset($_POST['deviceId'])?trim($_POST['deviceId']):'';
        $ret = $this->logicLogin->setLogin($openId,$type,$deviceId);
        return $this->ajax($ret);
    }

    /**
     * 标准退出登录过程
     * 状态返回0表示登出成功
     */
    public function signOutAction(){
        $ret = $this->logicLogin->signOut();
        $redirectUri = '/user/login';
        $this->redirect($redirectUri);
    }   
    
    /**
     * 检查用户是否登录
     */
    public function checkAction(){
        $ret = $this->logicLogin->checkLogin();
        return $this->ajax($ret);
    }    
}
