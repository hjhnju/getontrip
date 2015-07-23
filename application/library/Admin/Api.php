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
       $logicAdmin = new Admin_Logic_Admin();
       return $logicAdmin->login($name, $password);
    } 
}