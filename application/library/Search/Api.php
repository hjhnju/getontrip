<?php
class Search_Api{
    
    /**
     * 接口1:Search_Api::addLabel($arrObjs, $type, $labelId)
     * 为搜索标签关联上景点或城市
     * @param array $arrObjs
     * @param integer $type
     * @param integer $labelId
     * @return boolean
     */
    public static function addLabel($arrObjs, $type, $labelId){
        $logic = new Search_Logic_Label();
        return $logic->addLabel($arrObjs, $type, $labelId);
    }
    
    /**
     * 接口2:Search_Api::delLabel($labelId, $objId)
     * 删除搜索标签
     * @param integer $objId
     * @param integer $labelId
     * @return boolean
     */
    public static function delLabel($labelId, $objId){
        $logic = new Search_Logic_Label();    
        return $logic->delLabel($labelId, $objId);
    }
    
    /**
     * 接口3:Search_Api::listLabel($page, $pageSize, $arrInfo = array())
     * 查询搜索标签列表
     * @param integer $page
     * @param integer $pageSize
     * @param array $arrInfo
     * @return array
     */
    public static function listLabel($page, $pageSize, $arrInfo = array()){
        $logic = new Search_Logic_Label();
        return $logic->listLabel($page, $pageSize, $arrInfo);
    }
    
    /**
     * 接口4:Search_Api::getLabel($labelId, $page, $pageSize)
     * 获取某个搜索标签信息
     * @param integer $labelId
     * @param integer $page,标签数据的页码
     * @param integer $pageSize,标签对应对象数据的页面大小
     */
    public static function getLabel($labelId, $page, $pageSize){
        $logic = new Search_Logic_Label();
        return $logic->getLabel($labelId, $page, $pageSize);
    }
    
    /**
     * 接口5:Search_Api::addNewTag($name,$type = '', $arrObjIds = array())
     * 添加搜索标签
     * @param string $name
     * @param string $type,1:景点搜索标签;2:城市搜索标签
     * @param array $arrObjIds
     * @return boolean
     */
    public static function addNewTag($name,$type = '', $arrObjIds = array()){
        $logic = new Search_Logic_Label();
        return $logic->addNewTag($name, $type, $arrObjIds);
    }
    
    /**
     * 接口6:Search_Api::getQueryWords($page, $pageSize, $arrConf = array())
     * @param integer $page
     * @param integer $pageSize
     * @param array $arrConf
     * @return array
     */
    public static function getQueryWords($page, $pageSize, $arrConf = array()){
        $logic = new Search_Logic_Word();
        return $logic->getQueryWords($page, $pageSize, $arrConf);
    }
    
    /**
     * 接口7:Search_Api::editQueryWordStatus($word, $status)
     * @param string $word
     * @param integer $status
     * @return boolean
     */
    public static function editQueryWordStatus($word, $status){
        $logic = new Search_Logic_Word();
        return $logic->editQueryWordStatus($word, $status);
    }
}