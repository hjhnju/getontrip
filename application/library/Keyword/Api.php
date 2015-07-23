<?php
class Keyword_Api{
    
    /**
     * 接口1：Keyword_Api::queryKeywords($sight_id,$page,$pageSize)
     * 查询景点的词条信息
     * @param unknown $sight_id
     * @param unknown $page
     * @param unknown $pageSize
     * @return Ambigous <multitype:, multitype:number multitype: >
     */
    public static function queryKeywords($sight_id,$page,$pageSize){
        $logic = new Keyword_Logic_Keyword();
        return $logic->queryKeywords($sight_id, $page, $pageSize);
    }
    
    /**
     * 接口2：Keyword_Api::addKeyword($arrInfo)
     * 添加词条信息
     * @param array $arrInfo
     * @return boolean
     */
    public static function addKeyword($arrInfo){
        $logic = new Keyword_Logic_Keyword();
        return $logic->addKeywords($arrInfo);
    }
}