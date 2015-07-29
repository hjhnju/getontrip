<?php
/**
 * 网页解析器工厂类
 * @author huwei
 *
 */
class Spider_Factory{
    
    const PREFIX = 'Spider_Web_';
    
    /**
     * 获取解析器
     * @param string $class，解析器名称（类名）
     * @param string $url,解析的URL
     * @return object 解析器对象
     */
    public static function getInstance($class,$url,$type){
        $class = self::PREFIX.$class;
        $obj   = new $class($url,$type);
        return $obj;
    }
}