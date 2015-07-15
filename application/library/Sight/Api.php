<?php
class Sight_Api{
    
    /**
     * 接口1：Sight_Api::getSightList
     * @return array
     */
    public static function getSightList(){
        $model = new SightModel();
        $arr = $model->getSightList();
        return $arr;
    }
    
    /**
     * 接口2：Sight_Api::getSightById
     * @param integer $sightId
     * @return array
     */
    public static function getSightById($sightId){
        $model = new SightModel();
        $arr = $model->getSightById($sightId);
        return $arr;        
    }
    
    /**
     * 接口3：Sight_Api::getSightByCity
     * @param integer $cityId
     * @return array
     */
    public static function getSightByCity($cityId){
        $model = new SightModel();
        $arr = $model->getSightByCity($cityId);
        return $arr;
    }
    
    /**
     * 接口4：Sight_Api::editSight
     * @param integer $sightId
     * @param array $_updateData: array('describe' =>'xxx','name' => 'xxx');
     * @return integer:更新影响的行数，返回非零值正确
     */
    public static function editSight($sightId,$_updateData){
        $model = new SightModel();
        $ret = $model->eddSight($sightId, $_updateData);
        return $ret;
    }
    
    /**
     * 接口5：Sight_Api::addSight
     * @param array $arrInfo:array('name' => 'xxx','level' => 'xxx');
     * @return integer:更新影响的行数，返回非零值正确
     */
    public static function addSight($arrInfo){
        $model = new SightModel();
        $ret = $model->addNewSight($arrInfo);
        return $ret;
    }
}