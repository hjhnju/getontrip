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
     * @param string $source,可能是网址，也可以是页面内容
     * @param integer $type,类型
     * @return object 解析器对象
     */
    public static function getInstance($class,$source,$type=Spider_Type_Source::URL){
        $class = self::PREFIX.$class;
        $obj   = new $class($source,$type);
        return $obj;
    }
}