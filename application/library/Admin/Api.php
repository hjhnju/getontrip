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
    
    /**
     * 接口2：Admin_Api::listAdmin($page, $pageSize, $arrParams = array())
     * 根据条件查询管理员信息
     * @param integer $page
     * @param integer $pageSize
     * @param array $arrParams
     * @return array
     */
    public static function listAdmin($page, $pageSize, $arrParams = array()){
        $logicAdmin = new Admin_Logic_Admin();
        return $logicAdmin->listAdmin($page, $pageSize, $arrParams);
    }
    
    /**
     * 接口3：Admin_Api::getAdminById($adminId)
     * 根据ID查询管理员信息详情
     * @param integer $adminId
     * @return array
     */
    public static function getAdminById($adminId){
        $logicAdmin = new Admin_Logic_Admin();
        return $logicAdmin->getAdminById($adminId);
    }
    
    
    /**
     * 接口4：Admin_Api::addAdmin($arrParams)
     * 添加管理员
     * @param array $arrParams
     * @return boolean
     */
    public static function addAdmin($arrParams){
        $logicAdmin = new Admin_Logic_Admin();
        return $logicAdmin->addAdmin($arrParams);
    }
    
    /**
     * 接口5：Admin_Api::delAdmin($adminId)
     * 删除管理员
     * @param integer $adminId
     * @return boolean
     */
    public static function delAdmin($adminId){
        $logicAdmin = new Admin_Logic_Admin();
        return $logicAdmin->delAdmin($adminId);
    }
    
    /**
     * 接口6：Admin_Api::editAdmin($adminId,$arrParams)
     * 编辑管理员信息
     * @param integer $adminId
     * @param array $arrParams
     * @return boolean
     */
    public static function editAdmin($adminId,$arrParams){
        $logicAdmin = new Admin_Logic_Admin();
        return $logicAdmin->editAdmin($adminId, $arrParams);
    }
    
    /**
     * 接口7：Admin_Api::checkPasswd($adminId, $passwd)
     * 检验用户密码是否正确
     * @param integer $adminId
     * @param string $passwd
     * @return boolean
     */
    public static function checkPasswd($adminId, $passwd){
        $logicAdmin = new Admin_Logic_Admin();
        return $logicAdmin->checkPasswd($adminId, $passwd);
    }
    
    /**
     * 接口8：Admin_Api::checkLogin()
     * 检验用户是否登录
     * @return string userid|''
     */
    public static function checkLogin(){
        $logicAdmin = new Admin_Logic_Admin();
        return $logicAdmin->checkLogin();
    }
}