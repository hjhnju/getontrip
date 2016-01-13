<?php
class Food_Api{
    
    /**
     * 接口1：Food_Api::getFoods($page,$pageSize,$arrParam = array())
     * 获取特产信息
     * @param integer $page,页码
     * @param integer $pageSize
     * @param array   $arrParam,过滤条件
     * @return array
     */
    public static function getFoods($page,$pageSize,$arrParam = array()){
        $logicFood = new Food_Logic_Food();
        return    $logicFood->getFoods($page,$pageSize,$arrParam);
    }
    
    /**
     * 接口2：Food_Api::getFoodNum($sighId)
     * 根据景点ID获取特产数量
     * @param integer $sighId
     * @param integer $status
     * @return number
     */
    public static function getFoodNum($sighId, $status = Food_Type_Status::PUBLISHED){
        $logicFood = new Food_Logic_Food();
        return $logicFood->getFoodNum($sighId, $status);
    }
    
    /**
     * 接口3:Food_Api::editFood($id, $arrParam)
     * 修改特产信息
     * @param integer $id
     * @param array $arrParam
     */
    public static function editFood($id, $arrParam){
        $logicFood = new Food_Logic_Food();
        return $logicFood->editFood($id, $arrParam);
    }
    
    /**
     * 接口4:Food_Api::delFood($id)
     * 删除特产
     * @param integer $id
     */
    public static function delFood($id){
        $logicFood = new Food_Logic_Food();
        return $logicFood->delFood($id);
    }
    
    /**
     * 接口5:Food_Api::addFood($arrParam)
     * 添加特产
     * @param array $arrParam,array('title'=>'xxx','sight_id'=>1,...)
     */
    public static function addFood($arrParam){
        $logicFood = new Food_Logic_Food();
        return $logicFood->addFood($arrParam);
    }
    
    /**
     * 接口6:Food_Api::getFoodInfo($id)
     * 根据ID获取特产信息
     * @param string $id
     * @return array
     */
    public static function getFoodInfo($id){
        $logicFood = new Food_Logic_Food();
        return $logicFood->getFoodByInfo($id);
    }
    
    /**
     * 接口7：Food_Api::changeWeight($sightId,$id,$to)
     * 修改某景点下的特产的权重
     * @param integer $id ID
     * @param integer $to 需要排的位置
     * @return boolean
     */
    public static function changeWeight($sightId,$id,$to){
        $logicFood = new Food_Logic_Food();
        return $logicFood->changeWeight($sightId,$id,$to);
    }
}