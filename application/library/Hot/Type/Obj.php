<?php
class Hot_Type_Obj extends Base_Type{
        
    /**
     * 1 城市
     * @var integer
     */
    const CITY = 1;
    
    
    /**
     * 状态名
     * @var array
     */
    public static $names = array(
        self::CITY        => '城市',
    );
}