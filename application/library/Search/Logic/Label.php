<?php
class Search_Logic_Label extends Base_Logic{
    
    /**
     * 获取搜索标记的景点数
     * @param integer $labelId
     * @return integer
     */
    public function getLabeledNum($labelId){
        $listSearchLabel = new Search_List_Label();
        $listSearchLabel->setFilter(array('label_id' => $labelId));
        $listSearchLabel->setPagesize(PHP_INT_MAX);
        $arrRet = $listSearchLabel->toArray();
        return $arrRet['total'];
    }
    
    /**
     * 为城市或景点添加上搜索标签
     * @param integer $objId
     * @param integer $type
     * @param integer $labelId
     * @return boolean
     */
    public function addLabel($objId, $type, $labelId){
        $objSearchLabel = new Search_Object_Label();
        $objSearchLabel->objId   = $objId;
        $objSearchLabel->type    = $type;
        $objSearchLabel->labelId = $labelId;
        return $objSearchLabel->save();
    }
    
    /**
     * 删除搜索标签
     * @param integer $objId
     * @param integer $type
     * @param integer $labelId
     * @return boolean
     */
    public function delLabel($objId, $type, $labelId){
        $objSearchLabel = new Search_Object_Label();
        $objSearchLabel->fetch(array('obj_id' => $objId,'type'=>$type,'label_id'=>$labelId));
        return $objSearchLabel->remove();
    }
}