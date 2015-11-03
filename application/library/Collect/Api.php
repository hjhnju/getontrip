<?php
class Collect_Api{    
    
    /**
     * 接口1：Collect_Api::getCollectList($page, $pageSize, $arrInfo = array())
     * 获取收藏信息
     * @param integer $page
     * @param integer $pageSize
     * @param array   $arrInfo
     * @return array
     */
    public static function getCollectList($page, $pageSize, $arrInfo = array()){
        $logic = new Collect_Logic_Collect();
        return $logic->getCollectList($page, $pageSize, $arrInfo);
    }
} 