<?php
/**
 * 用户信息逻辑层
 * @author huwei
 *
 */
class User_Logic_User extends Base_Logic{
    
    public function __construct(){
    
    }
    
    /**
     * 根据设备ID获取用户ID
     * @param string $device_id
     * @return number
     */
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
    
    /**
     * 获取用户名
     * @param integer $userId
     * @return string
     */
    public function getUserName($userId){
        $objUser  = new User_Object_User();
        $objUser->fetch(array('id' => $userId));
        return $objUser->nickName;
    }
    
    public function getUserAvatar($userId){
        $objUser  = new User_Object_User();
        $objUser->fetch(array('id' => $userId));
        return Base_Image::getUrlByName($objUser->image);
    }
    
    /**
     * 获取用户信息
     * @param string $deviceId
     * @return array
     */
    public function getUserInfo($deviceId){
        $objUser  = new User_Object_User();
        $objUser->fetch(array('device_id' => $deviceId));
        return $objUser->toArray();
    }
    
    /**
     * 修改用户信息
     * @param string $deviceId
     * @param string $strParam
     * @return boolean
     */
    public function editUserInfo($deviceId,$strParam){
        $objUser  = new User_Object_User();
        $objUser->fetch(array('device_id' => $deviceId));
        $arrData  = implode(",",$strParam);
        foreach ($arrData as $val){
            $arrTemp = implode(":", $val);
            if(isset($val[0]) && isset($val[1])){
                $key           = $this->getprop($arrTemp[0]);
                $objUser->$key = $arrTemp[1];
            }            
        }
        return $objUser->save();
    }
}