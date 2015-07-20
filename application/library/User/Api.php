<?php
/**
 * 对外的API接口
 */
class User_Api{
    
    /** 
     * 接口1：User_Api::login($name,$password)
     * 登录接口    
     * @param string $name,用户名
     * @param string $passwd,密码
     */
    public static function login($name,$password){
       $objAdmin = new Admin_Object_Admin();
       $objAdmin->fetch(array('name' => $name,'passwd' => $password));
       if(!empty($objAdmin->id)){
           $objAdmin->loginTime = time();
           return $objAdmin->save();
       }
       return false;
    }

  
}
