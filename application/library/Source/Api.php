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
     * 接口4：Source_Api::searchSource($arrConf,$page,$pageSize)
     * 根据arrConf来进行查询，
     * @param array   $arrConf,array,eg:array('type'=>1,'name'="xx");
     * @param integer $page
     * @param integer $pageSize
     * @return array
     */
    public static function searchSource($arrConf,$page,$pageSize){
        $logicSource = new Source_Logic_Source();
        return $logicSource->searchSource($arrConf,$page, $pageSize);
    }
    
    /**
     * 接口5：Source_Api::getSourceByName($name)
     * 根据来源名获取来源信息
     * @param string $name
     * @return array
     */
    public static function getSourceByName($name){
        $logicSource = new Source_Logic_Source();
        return $logicSource->getSourceByName($name);
    }
    
    /**
     * 接口6：Source_Api::getHotSource()
     * 获取热门来源
     * @return array
     */
    public static function getHotSource(){
        $logicSource = new Source_Logic_Source();
        return $logicSource->getHotSource();
    }
    
    /**
     * 接口7：Source_Api::editSource($sourceId,$arrInfo)
     * 编辑一个来源
     * @param integer $sourceId
     * @param array $arrInfo
     * @return boolean
     */
    public static function editSource($sourceId,$arrInfo){
        $logicSource = new Source_Logic_Source();
        return $logicSource->editSource($sourceId, $arrInfo);
    }
    
    /**
     * 接口8：Source_Api::delSource($sourceId)
     * 删除来源，当来源已经有相关话题时不允许删除
     * @param integer $sourceId
     * @return boolean
     */
    public static function delSource($sourceId){
        $logicSource = new Source_Logic_Source();
        return $logicSource->delSource($sourceId);
    }
    
}