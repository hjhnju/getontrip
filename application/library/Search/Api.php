<?php
class Search_Api{
    
    /**
     * 接口1:Search_Api::addLabel($objId, $type, $labelId)
     * 为城市或景点添加上搜索标签
     * @param integer $objId
     * @param integer $type
     * @param integer $labelId
     * @return boolean
     */
    public static function addLabel($objId, $type, $labelId){
        $logic = new Search_Logic_Label();
        return $logic->addLabel($objId, $type, $labelId);
    }
    
    /**
     * 接口2:Search_Api::delLabel($objId, $type, $labelId)
     * 删除搜索标签
     * @param integer $objId
     * @param integer $type
     * @param integer $labelId
     * @return boolean
     */
    public static function delLabel($objId, $type, $labelId){
        $logic = new Search_Logic_Label();
        return $logic->delLabel($objId, $type, $labelId);
    }
}