<?php
class Hot_Type_Obj extends Base_Type{
        
    /**
     * 1 城市
     * @var integer
     */
    const CITY = 1;
    
    /**
     * 2 图文
     * @var integer
     */
    const IMAGETOPIC = 2;
    
    
    /**
     * 状态名
     * @var array
     */
    public static $names = array(
        self::CITY        => '城市',
        self::IMAGETOPIC  => '图文',
    );
}