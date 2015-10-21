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
        $objLogin = new User_Object_Third(); 
        $objLogin->fetch(array('open_id' => $openId,'auth_type' => $type));
        $arr = $objLogin->toArray();
        if(empty($arr)){            
            $objLogin->openId    = $openId;
            $objLogin->userId    = User_Api::getCurrentUser($type);
            $objLogin->authType  = $type;
            $objLogin->loginTime = time();
            $ret = $objLogin->save();
            $userid              = $objLogin->userId;
        }else{
            $userid = $arr['user_id']; 
        }
        Yaf_Session::getInstance()->set(User_Keys::getLoginUserKey(), $userid);
        return strval($userid);           
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
