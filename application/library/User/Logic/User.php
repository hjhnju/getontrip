<?php
/**
 * 用户信息逻辑层
 * @author huwei
 *
 */
class User_Logic_User{
    
    public function __construct(){
    
    }
    
    public function getUserId($device_id){
        $objUser = new User_Object_User();
        $objUser->fetch(array('device_id' => $device_id));
        if(!empty($objUser->id)){
            return $objUser->id;
        }
        $objUser->deviceId = $device_id;
        $objUser->save();
        return $objUser->id;
    }
    
    public function getUserName($userId){
        $objUser  = new User_Object_User();
        $objUser->fetch(array('id' => $userId));
        return $objUser->nickName;
    }
}