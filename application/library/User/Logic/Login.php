<?php
/**
 * 登录Logic层
 * 
 */
class User_Logic_Login { 
    
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
     * @return boolean
     */ 
    public function setLogin($openId,$type,$deviceId){
        $objLogin = new User_Object_Login();        
        $objLogin->fetch(array('open_id' => $openId,'auth_type' => $type));
        $arr = $objLogin->toArray();
        if(empty($arr)){
            $objUser = new User_Object_User();
            $objUser->deviceId = $deviceId;
            $objUser->save();            
            $objLogin->openId   = $openId;
            $objLogin->userId   = $objUser->id;
            $objLogin->authType = $type;
            $userid             = $objUser->id;
        }else{
            $userid = $arr['user_id']; 
        }
        $objLogin->loginTime = time();
        $objLogin->save();        
        $ret = Yaf_Session::getInstance()->set(User_Keys::getLoginUserKey(), $userid);
        return $ret;           
    }
    
    /**
     * 用户退出登陆
     * @return boolean
     */
    public function signOut(){
        //正常登录session删除
        $ret = Yaf_Session::getInstance()->del(User_Keys::getLoginUserKey());
        
        //三方登录session删除
        Yaf_Session::getInstance()->del(User_Keys::getAuthTypeKey());
        Yaf_Session::getInstance()->del(User_Keys::getOpenidKey());
        //$obj = new User_Object_Record();  应该要做条记录
        return $ret;
    }
}
