<?php
class Wechat_Xjd extends Wechat_Base{
	//定义该类的实例对象
	public static $_objInstance;
    //存储所有的配置变量
    static $_config;
	/**
	 * 返回类的实例对象
	 * @return 类的实例
	 */
	public static function getInstance() {
        if (!self::$_objInstance) {
            $config = self::getConfig();
            self::$_objInstance = new self($config['basic']);
        }
        return self::$_objInstance;
    }
    /**
     * 实例化父类
     * @param 微信需要的参数
     */
    public function __construct($config){
    	parent::__construct($config);
    }
    /**
     * 获取需要的配置变量
     * @return null
     */
    public static function getConfig(){
        if(empty(self::$_config)) {
                self::$_config = array(
                'basic' => Base_Config::getConfig('wechat', CONF_PATH . '/wechat.ini'),
                'pay'   => Base_Config::getConfig('pay', CONF_PATH . '/wechat.ini'),
            );
        }
        return self::$_config;
    }
}