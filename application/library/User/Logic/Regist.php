<?php
/**
 * 用户注册逻辑层
 * @author huwei
 *
 */
class User_Logic_Regist extends Base_Logic{
    
    const USER_NAME_PREFIX = '途知';
    
    public function __construct(){
    
    }
    
    public function regist($email,$passwd){
        $ret = User_Logic_Validate::check(User_Logic_Validate::REG_EMAIL, $email);
        if(!$ret){
            return User_RetCode::EMAIL_FORMAT_WRONG;
        }
        $ret = User_Logic_Validate::check(User_Logic_Validate::REG_PASSWD, $passwd);
        if(!$ret){
            return User_RetCode::PASSWD_FORMAT_WRONG;
        }
        $objUser = new User_Object_User();
        $objUser->fetch(array('email' => $email));
        if(!empty($objUser->id)){
            return User_RetCode::EMAIL_EXSIT;
        }
        
        $objUser = new User_Object_User();
        $passwd  = Base_Util_Secure::encrypt($passwd);
        $objUser->email  = $email;
        $objUser->passwd = $passwd;
        $ret = $objUser->save();
        $objUser->nickName = self::USER_NAME_PREFIX.Base_Util_Secure::encryptUserId($objUser->id);
        $objUser->save();
        if($ret){
            Yaf_Session::getInstance()->set(User_Keys::getLoginUserKey(), $objUser->id);
            return User_RetCode::SUCCESS;
        }
        return User_RetCode::UNKNOWN_ERROR;
    }   
    
    public function changeUserName($str){
        $num     = 1;
        $pattern = '/(\d)+$/';
        preg_match($pattern, $str, $matches);
        if(empty($matches[0])){
            $str .= $num;
        }else{
            $str  = str_replace($matches[0], "", $str);
            $str = $str.($matches[0]+$num);
        }
        return $str;
    }
}