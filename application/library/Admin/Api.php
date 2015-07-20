<?php
/**
 * 对外的API接口
 */
class Admin_Api{
    
    /** 
     * 接口1：Admin_Api::login($name,$password)
     * 登录接口    
     * @param string $name,用户名
     * @param string $passwd,密码
     * @return boolean
     */
    public static function login($name,$password){
       $objAdmin = new Admin_Object_Admin();
       $objAdmin->fetch(array('name' => $name,'passwd' => $password));
       if(!empty($objAdmin->id)){
           $objAdmin->loginTime = time();
           Yaf_Session::getInstance()->set(User_Keys::getLoginUserKey(), $objAdmin->id);
           return $objAdmin->save();
       }       
       return false;
    }  
}