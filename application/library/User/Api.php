<?php
/**
 * 用户模块对外的API接口
 */
class User_Api{
    
    const LAST_TIME = 5;     //验证码过期时间,5分钟
    
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
     * 获取当前用户ID,
     * @return number|''
     */
    public static function getCurrentUser(){
        $logicUser = new User_Logic_User();
        return $logicUser->getCurrentUser();
    }
    
    /**
     * 接口4:User_Api::createUser()
     * 创建一个用户
     * @return number,用户ID
     */
    public static function createUser(){
        $logicUser = new User_Logic_User();
        return $logicUser->createUser();
    }
    
    public static function sendSmsCode($strPhone, $strType){
        if('dev' === ini_get('yaf.environ')){
            return true;
        }
        $srandNum = rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9);
        $arrArgs  = array($srandNum, self::LAST_TIME);
        $tplid    = Base_Config::getConfig('sms.tplid.vcode', CONF_PATH . '/sms.ini');
        $bResult  = Base_Sms::getInstance()->send($strPhone, $tplid[1], $arrArgs);
        if(!empty($bResult)){
            Base_Redis::getInstance()->setex(User_Keys::getSmsCodeKey($strPhone, $strType),
            60*(self::LAST_TIME), $srandNum);
            return true;
        }
        return false;
    }
    
    /**
     * 验证用户输入的短信验证码是否正确
     */
    public static function checkSmscode($strPhone, $strVeriCode, $strType){
        if('dev' === ini_get('yaf.environ')){
            return true;
        }
        $storeCode = Base_Redis::getInstance()->get(User_Keys::getSmsCodeKey($strPhone, $strType));
        if($strVeriCode === $storeCode){
            return true;
        }
        return false;
    }
}
