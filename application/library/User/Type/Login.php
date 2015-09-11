<?php
/**
 * 第三方登录类型
 * @author huwei
 *
 */
class User_Type_Login extends Base_Type {
    
    /**
     * 0 NOT_IN
     * 未登录
     */
    const NOT_IN = 0;
    
    
    /**
     * 1 QQ
     * @var integer
     */
    const QQ = 1;
   
    /**
     * 2 微信 
     * @var string
     */
    const WEIXIN = 2; 

    /**
     * 3 微博
     * @var string
     */
    const WEIBO = 3;
    
    /**
     * 状态名
     * @var array
     */
    public static $names = array(
        self::QQ           => 'qq',  
        self::WEIXIN       => 'weixin',
        self::WEIBO        => 'weibo',
    );
    
    /**
     * @param  $authtype = 'weibo', 'qq', 'weixin'
     * @return intType
     */
    public static function getAuthType($authtype) {
        $authtype = strtolower($authtype);
        $type     = null;
        switch ($authtype) {
            case 'qq':
                $type = self::QQ;
                break;
            case 'weibo':
                $type = self::WEIBO;
                break;
            case 'weixin':
                $type = self::WEIXIN;
                break;
            default:
                # code...
                break;
        }
        return $type;
    } 
}