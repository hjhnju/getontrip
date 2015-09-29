<?php
class Tongji_Api{
    
    /**
     * 接口1：Tongji_Api::city($arrInfo, $page, $pageSize)
     * 城市信息统计
     * @param array $arrInfo
     * @param integer $page
     * @param integer $pageSize
     * @return array
     */
    public static function city($arrInfo, $page, $pageSize){
        $logic = new Tongji_Logic_Tongji();
        return $logic->city($arrInfo, $page, $pageSize);
    }
}