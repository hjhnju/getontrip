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
    public function getUserInfo($userId){
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
     * @param array $arrParam
     * @return boolean
     */
    public function editUserInfo($userId,$arrParam, $file = ''){
        $objUser  = new User_Object_User();
        $objUser->fetch(array('id' => $userId));
        foreach ($arrParam as $key => $val){
            if($val !== "-1"){
                $key           = $this->getprop($key);     
                $objUser->$key = trim($val);
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
     * @param array $arrParam
     * @return boolean
     */
    public function addUserInfo($userId,$arrParam){
        $objUser  = new User_Object_User();
        $objUser->fetch(array('id' => $userId));
        if(isset($objUser->image) && !empty($objUser->image)){
            $this->delPic($objUser->image);
        }
        foreach ($arrParam as $key => $val){
            if(!empty($val)){
                $key           = $this->getprop($key);
                if($key == 'image'){
                    $objUser->$key = $this->uploadPic($val);
                }elseif($key == 'nickName'){
                    if($this->checkName($val)){
                        $logicRegist = new User_Logic_Regist();
                        $objUser->nickName = $logicRegist->changeUserName($val);
                    }else{
                        $objUser->nickName = $val;
                    }                    
                }else{
                    $objUser->$key = trim($val);
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
    
    public function getCurrentUser(){
        $logic = new User_Logic_Third();
        return $logic->checkLogin();
    }
    
    public function createUser(){
        $objUser = new User_Object_User();
        $objUser->deviceId = isset($_COOKIE[User_Keys::getDeviceIdKey()])?trim($_COOKIE[User_Keys::getDeviceIdKey()]):'';
        $objUser->save();        
        //$user   = Base_Util_Secure::encryptForUuap(Base_Util_Secure::PASSWD_KEY, $objUser->id);
        //setcookie(User_Keys::getCurrentUserKey(),urlencode($user));
        return $objUser->id;
    }
    
    private function makeRand($length="6"){//密码生成函数
        $str1    = "abcdefghijklmnopqrstuvwxyz";
        $str2    = "0123456789";
        $result = "";
        for($i=0;$i<$length/2;$i++){
            $num[$i] = rand(0,25);
            $result .= $str1[$num[$i]];
        }
        for($i=0;$i<$length/2;$i++){
            $num[$i] = rand(0,9);
            $result .= $str2[$num[$i]];
        }
        return $result;
    }
    
    public function checkName($strName){
        $objUser = new User_Object_User();
        $objUser->fetch(array('nick_name' => $strName));
        if(!empty($objUser->id)){
            return true;
        }
        return false;
    }
    
    public function sendEmail($email){
        $ret = User_Logic_Validate::check(User_Logic_Validate::REG_EMAIL, $email);
        if(!$ret){
            return User_RetCode::EMAIL_FORMAT_WRONG;
        }      
        $objUser = new User_Object_User();
        $objUser->fetch(array('email' => $email));
        if(empty($objUser->passwd)){
            return User_RetCode::EMAIL_WRONG;
        }
        $passwd    = $this->makeRand();
        $objUser->passwd = Base_Util_Secure::encrypt($passwd);
        $objUser->save();
        ////发送一封邮件到email代表的邮箱中
        $to = strval($email);
        $subject = '重置邮箱密码';
        $body = "亲爱的%s，您好，你的邮箱密码被重置为%s，您可以用这个密码直接登录途知。<br><br>途知";
        $body = sprintf($body,$objUser->nickName,$passwd);
        $ret  = Base_Mailer::getInstance()->send($to, $subject, $body);
        if($ret){
            return User_RetCode::SUCCESS;
        }
        return User_RetCode::UNKNOWN_ERROR;
    }
}