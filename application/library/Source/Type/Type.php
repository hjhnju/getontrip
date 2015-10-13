<?php
/**
 * 来源类型
 * @author huwei
 *
 */
class Source_Type_Type extends Base_Type {
    /**
     * 1 微信公众号
     * @var integer
     */
    const WEIXIN  = 1;  
    /**
     * 2 网站
     * @var integer
     */
    const WEB = 2;
    
    /**
     * 3 期刊杂志
     * @var integer
     */
    const MAGZINE = 3;
   
    
    /**
     * 来源名
     * @var array
     */
    public static $names = array(
        self::WEIXIN     => '微信公众号',
        self::WEB        => '网站',
        self::MAGZINE    => '期刊杂志',
    );
}