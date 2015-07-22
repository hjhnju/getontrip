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
}