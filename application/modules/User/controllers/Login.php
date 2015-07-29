<?php
/**
 * 用户登录相关操作
 */
class LoginController extends Base_Controller_Page{
    
    public function init(){
        //未登录不跳转
        $this->setNeedLogin(false);

        parent::init();
    } 
  
    /**
     * /user/login
     * 作为redirect_uri，第三方回调
     * 
     * 获取open id，分为如下几步骤:
     * 1.拿到auth code,当用户点击授权后，将返回auth code
     * 2.拿到auth code后，首先检查access token是否存在，如果不存在执行3，存在执行4
     * 3.通过auth code获取access token
     * 4.通过access token拿到用户的openid
     * 
     * access_token缓存的用途是：使用api获取用户信息时需要access_token + openid
     * 所以access_token是缓存在userid或openid维度即可
     * @param string $code, authcode
     * @param string $state, rand num
     */
    public function indexAction(){
        $state    = trim($_REQUEST['state']);
        //TODO:check state
        $strAuthCode  = trim($_REQUEST['code']);
        if(empty($state) || empty($strAuthCode)){   //auth code
            return $this->ajaxError(User_RetCode::GET_AUTHCODE_FAIL,
                User_RetCode::getMsg(User_RetCode::GET_AUTHCODE_FAIL));    
        }

        //获取登陆类型qq|weibo|weixin
        $strType = Yaf_Session::getInstance()->get(User_Keys::getAuthTypeKey());
        //授权登录并保存openid
        $logic   = new User_Logic_Third();
        $openid  = $logic->login($strType, $strAuthCode);
        if(empty($openid)){
            return $this->ajaxError(User_RetCode::GET_OPENID_FAIL,
                User_RetCode::getMsg(User_RetCode::GET_OPENID_FAIL));
        }
        Yaf_Session::getInstance()->set(User_Keys::getOpenidKey(), $openid);        
    }

    /**
     * 标准退出登录过程
     * 状态返回0表示登出成功
     */
    public function signOutAction(){
        $logic   = new User_Logic_Login();
        $ret = $logic->signOut();
        $redirectUri = '/user/login';
        $this->redirect($redirectUri);
    }   
}
