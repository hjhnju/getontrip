<?php
/**
 * 用户模块对外的API接口
 */
class User_Api{
    
    /**
     * 接口1：User_Api::getUserList($page,$pageSize,$arrParams = array())
     * 获取用户信息列表
     * @param integer $page
     * @param integer $pageSize
     * @param array $arrParams
     * @return array
     */
    public static function getUserList($page,$pageSize,$arrParams = array()){
       $logicUser = new User_Logic_User();
       return $logicUser->getUserList($page, $pageSize, $arrParams);
    }
    
    /**
     * 接口2：User_Api::getUserById($userId)
     * 根据用户ID获取用户信息
     * @param integer $userId
     * @return array
     */
    public static function getUserById($userId){
        $logicUser = new User_Logic_User();
        return $logicUser->getUserById($userId);  
    }
    
    /**
     * 接口3：User_Api::getCurrentUser()
     * 获取当前用户ID,用户可能登录了，也可能未登录。第一次则通过
     * 设备ID创建一个用户
     * @return number|''
     */
    public static function getCurrentUser(){
        $logicUser = new User_Logic_User();
        return $logicUser->getCurrentUser();
    }
}
