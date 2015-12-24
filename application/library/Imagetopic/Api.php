<?php
class Imagetopic_Api{
    
    /**
     * 接口1：Imagetopic_Api::getImageTopicById($id)
     * 获取图文详情
     * @param integer $id
     * @return array
     */
    public static function getImageTopicById($id){
        $logic = new Imagetopic_Logic_Imagetopic();
        return $logic->getImageTopicById($id);
    }
    
    /**
     * 接口2：Imagetopic_Api::getImageTopicList($page, $pageSize, $arrInfo = array())
     * @param integer $page
     * @param integer $pageSize
     * @param array $arrInfo
     * @return array
     */
    public static function getImageTopicList($page, $pageSize, $arrInfo = array()){
        $logic = new Imagetopic_Logic_Imagetopic();
        return $logic->getImageTopicList($page, $pageSize, $arrInfo);
    }
}