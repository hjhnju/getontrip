<?php
class User_Keys {

    const SESSION_OPENID_KEY   = 'openid';

    const SESSION_AUTHTYPE_KEY = 'authtype';

    const SESSION_LOGINUSER_KEY= 'login_user';

    const ACCESS_TOKEN_KEY     = 'user_openid_%s';
    
    const OPEN_INFO_KEY        = 'openinfo_%s_%s';
   

    public static function getOpenidKey(){
        return self::SESSION_OPENID_KEY;
    }

    public static function getAuthTypeKey(){
    	return self::SESSION_AUTHTYPE_KEY;
    }

    public static function getLoginUserKey(){
    	return self::SESSION_LOGINUSER_KEY;
    }

    public static function getAccessTokenKey($openid){
        return sprintf(self::ACCESS_TOKEN_KEY, $openid);
    }

    public static function getOpenInfoKey($authtype, $openid){
        return sprintf(self::OPEN_INFO_KEY, $authtype, $openid);
    }
}
