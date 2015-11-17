<?php
/**
 * 第三方登录Logic层
 * 
 */
class User_Logic_Third { 
    
    public function __construct(){
    }
    
    /**
     * 判断用户的登录状态
     * @return userid || false
     */
    public function checkLogin(){
        $userid = Yaf_Session::getInstance()->get(User_Keys::getLoginUserKey());
        if(!empty($userid)){
            $userid = intval($userid);
            return $userid;
        }
       return false;
    }

    /**
     * 设置用户的登陆状态
     * @return integer
     */ 
    public function setLogin($openId,$type){
        $ret      = User_RetCode::SUCCESS;
        $userid   = '';
        $objLogin = new User_Object_Third(); 
        $objLogin->fetch(array('open_id' => $openId,'auth_type' => $type));
        $arr = $objLogin->toArray();
        if(empty($arr)){            
            $objLogin->openId    = $openId;
            $objLogin->userId    = User_Api::createUser();
            $objLogin->authType  = $type;
            $objLogin->loginTime = time();
            $ret    = $objLogin->save();
            $userid = $objLogin->userId;
            $ret    = User_RetCode::NEED_INFO;
        }else{
            $userid = $arr['user_id'];
            $objUser = new User_Object_User();
            $objUser->fetch(array('id' => $userid));
            if(empty($objUser->nickName) && empty($objUser->city) && empty($objUser->image)){
                $ret = User_RetCode::NEED_INFO;
            }
        }
        Yaf_Session::getInstance()->set(User_Keys::getLoginUserKey(), $userid);
        return $ret;           
    }
    
    /**
     * 用户退出登陆
     * @return boolean
     */
    public function signOut(){
        //正常登录session删除
        if(false == Yaf_Session::getInstance()->del(User_Keys::getLoginUserKey())){
            return false;
        }
        return true;
    }
}
