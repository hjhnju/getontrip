<?php
class Admin_Keys {

    const SESSION_LOGINUSER_KEY= 'login_user';   

    public static function getLoginAdminKey(){
    	return self::SESSION_LOGINUSER_KEY;
    }
}
