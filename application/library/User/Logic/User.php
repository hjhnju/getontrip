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
        $objUser->type     = User_Type_Login::NOT_IN;
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
     * @return array
     */
    public function getUserInfo($userId, $type){
        $objUser  = new User_Object_User();
        $objUser->fetch(array('id' => $userId));
        $arrRet   =  $objUser->toArray();
        if(empty($arrRet['nick_name']) && empty($arrRet['image'])){
            return array();
        }
        if(!empty($arrRet['image'])){
            $arrRet['image'] = Base_Image::getUrlByName($arrRet['image']);
        }
        return array(
            'nick_name' => isset($arrRet['nick_name'])?trim($arrRet['nick_name']):'',
            'image'     => isset($arrRet['image'])?trim($arrRet['image']):'',
            'sex'       => isset($arrRet['sex'])?trim(strval($arrRet['sex'])):'',
            'city'      => isset($arrRet['city'])?trim(strval($arrRet['city'])):'',
        );
    }
    
    /**
     * 修改用户信息
     * @param string $strParam
     * @return boolean
     */
    public function editUserInfo($userId, $type, $strParam, $file = ''){
        $objUser  = new User_Object_User();
        $objUser->fetch(array('id' => $userId,'type' => $type));
        $arrData  = explode(",",$strParam);
        foreach ($arrData as $val){
            $arrTemp = explode(":", $val);
            if(isset($arrTemp[0]) && isset($arrTemp[1])){
                $key           = $this->getprop($arrTemp[0]);                
                $objUser->$key = $arrTemp[1];
            }            
        }
        if(!empty($file)){            
            $ext = explode("/",$file['type']);
            if (!isset($ext[1])||!in_array($ext[1], array('jpg', 'gif', 'jpeg','png'))) {
                 return false;
            }
              
            $hash = md5(microtime(true));
            $hash = substr($hash, 8, 16);
            if(trim($ext[1]) == 'gif'){
                $filename = $hash . '.gif';
            }else{
                $filename = $hash . '.jpg';
            }        
            
            $oss = Oss_Adapter::getInstance();
            $res = $oss->writeFile($filename, $file['tmp_name']);
            if($res){
                if(!empty($objUser->image)){
                    $this->delPic($objUser->image);
                }
                $objUser->image = $filename;
            }else{
                return false;
            }
        }
        return $objUser->save();
    }
    
    /**
     * 添加用户信息,如果用户已经存在,则失败
     * @param string $userId
     * @param string $strParam
     * @return boolean
     */
    public function addUserInfo($userId, $type, $strParam){
        $objUser  = new User_Object_User();
        $objUser->fetch(array('id' => $userId,'type' => $type));
        if(!empty($objUser->nickName) || !empty($objUser->image)){
            return false;
        }
        $arrData  = explode(",",$strParam);
        foreach ($arrData as $val){
            $arrTemp = explode(":", $val);
            if(isset($arrTemp[0]) && isset($arrTemp[1])){
                $key           = $this->getprop($arrTemp[0]);
                if($key == 'image'){
                    $objUser->$key = $this->uploadPic($arrTemp[1]);
                }else{
                    $objUser->$key = $arrTemp[1];
                }
            }
        }
        return $objUser->save();
    }
    
    /**
     * 获取用户信息列表
     * @param integer $page
     * @param integer $pageSize
     * @param array $arrParams
     * @return array
     */
    public function getUserList($page,$pageSize,$arrParams = array()){
        $listUser = new User_List_User();
        $listUser->setPage($page);
        $listUser->setPagesize($pageSize);
        if(!empty($arrParams)){
            $listUser->setFilter($arrParams);
        }
        $arrRet =  $listUser->toArray();
        foreach ($arrRet['list'] as $key => $val){
            $objLogin = new User_Object_Third();
            $objLogin->fetch(array('id' => $val['id']));
            if(!empty($objLogin->id)){
                $arrRet['list'][$key]['logintime'] = $objLogin->loginTime;
            }else{
                $arrRet['list'][$key]['logintime'] = '';
            }
        }
        return $arrRet;
    }
    
    /**
     * 根据用户ID获取用户信息
     * @param unknown $userId
     * @return Ambigous <multitype:, multitype:NULL >
     */
    public function getUserById($userId){
        $objUser = new User_Object_User();
        $objUser->fetch(array('id' => $userId));
        $ret = $objUser->toArray();
        if(!empty($ret)){ 
            $objLogin = new User_Object_Third();
            $objLogin->fetch(array('id' => $ret['id']));
            if(!empty($objLogin->id)){
                $ret['logintime'] = $objLogin->loginTime;
            }else{
                $ret['logintime'] = '';
            }    
        }
        return $ret;   
    }
    
    /**
     * 上传用户图像
     * @param string $data，图像二进制数据
     * @param string $type,图像后缀名
     * @see Base_Logic::uploadPic()
     */
    public function uploadUserAvatar($data,$type){
        $hash = md5(microtime(true));
        $hash = substr($hash, 8, 16);
        $filename = $hash .'.'.$type;   
        
        $oss = Oss_Adapter::getInstance();
        $res = $oss->writeFile($filename, $data);
        if ($res) {
            $filename;
        }
        return '';
    }
    
    public function getCurrentUser($type = ''){
        $logic = new User_Logic_Third();
        return $logic->checkLogin();
    }
    
    public function createUser(){
        $objUser = new User_Object_User();
        $objUser->deviceId = isset($_COOKIE[User_Keys::getDeviceIdKey()])?trim($_COOKIE[User_Keys::getDeviceIdKey()]):'';
        $objUser->save();        
        $user   = Base_Util_Secure::encryptForUuap(Base_Util_Secure::PASSWD_KEY, $objUser->id);
        setcookie(User_Keys::getCurrentUserKey(),urlencode($user));
        return $objUser->id;
    }
}