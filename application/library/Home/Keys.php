<?php
class Home_Keys {
    
    //用户的经度信息
    const SESSION_USER_X_NAME   = 'session_x';
    
    //用户的纬度信息
    const SESSION_USER_Y_NAME   = 'session_y';
   
    public static function getSessionXName(){
        return sprintf(self::SESSION_USER_X_NAME);
    }
    
    public static function getSessionYName(){
        return sprintf(self::SESSION_USER_Y_NAME);
    }
}
