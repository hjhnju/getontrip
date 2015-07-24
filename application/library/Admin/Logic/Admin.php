<?php
class Admin_Logic_Admin{
    
    public function __construct(){
        
    }
    

    /**
     * 登录接口
     * @param string $name,用户名
     * @param string $passwd,密码
     * @return boolean
     */
    public function login($name,$password){
        $objAdmin = new Admin_Object_Admin();
        $objAdmin->fetch(array('name' => $name,'passwd' => $password));
        if(!empty($objAdmin->id)){
            $objAdmin->loginTime = time();
            Yaf_Session::getInstance()->set(Admin_Keys::getLoginAdminKey(), $objAdmin->id);
            return $objAdmin->save();
        }
        return false;
    }
    
    public function getUserName($userId){
        $objAdmin = new Admin_Object_Admin();
        $objAdmin->fetch(array('id' => $userId));
        return $objAdmin->name;
    }
}