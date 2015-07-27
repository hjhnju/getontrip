<?php
class Source_Api{    
    
    /**
     * 接口1：Source_Api::listSource($page,$pageSize)
     * 获取来源列表
     * @param integer $page
     * @param integer $pageSize
     * @return array
     */
    public static function listSource($page,$pageSize){
        $logicSource = new Source_Logic_Source();
        return $logicSource->listSource($page, $pageSize);
    }
    
    /**
     * 接口2：Source_Api::addSource($arrInfo)
     * 添加一个来源
     * @param array $arrInfo
     * @return boolean
     */
    public static function addSource($arrInfo){
        $logicSource = new Source_Logic_Source();
        return $logicSource->addSource($arrInfo);
    }
    
    /**
     * 接口3：Source_Api::getSourceInfo($id)
     * 根据id获取来源信息
     * @param integer $id
     * @return array
     */
    public static function getSourceInfo($id){
        $logicSource = new Source_Logic_Source();
        return $logicSource->getSourceInfo($id);
    }
    
    /**
     * 接口4：Source_Api::searchSource($query,$page,$pageSize)
     * @param string $query
     * @param integer $page
     * @param integer $pageSize
     * @return array
     */
    public static function searchSource($query,$page,$pageSize){
        $logicSource = new Source_Logic_Source();
        return $logicSource->searchSource($query, $page, $pageSize);
    }
}