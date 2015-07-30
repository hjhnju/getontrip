<?php
class Wiki_Api{
    
    /**
     * 接口1：Wiki_Api::getWiki($sightId,$page,$pageSize)
     * 根据景点获取百科词条信息
     * @param integer $sightId
     * @param integer $page
     * @param integer $pageSize
     * @param integer $status,类型：1未发布，2已发布，3所有
     * @return array
     */
    public static function getWiki($sightId,$page,$pageSize,$status=Wiki_Type_Status::ALL){
        $logicWiki = new Wiki_Logic_Wiki();
        $arrWiki   = $logicWiki->getWikis($sightId, $page, $pageSize,$status);
        if(empty($arrWiki)){
            return $logicWiki->getWikiSource($sightId,$page,$pageSize);
        }
        return $arrWiki;
    }
    
    public static function editWiki($sightId,$keywordId,$arrInfo){
        $logicWiki  = new Wiki_Logic_Wiki();
    }
}