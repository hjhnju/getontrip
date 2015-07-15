<?php
/**
 * 城市API
 * @author huwei
 *
 */
class City_Api{
    
    /**
     * 接口1：City_Api::getCityInfo
     * 获取城市信息
     * @param integer $page
     * @param integer $pageSize
     * @return array
     */
    public static function getCityInfo($page, $pageSize){
        $listCity = new City_List_City();
        $listCity->setPage($page);
        $listCity->setPagesize($pageSize);
        return $listCity->toArray();
    }
    
    /**
     * 接口2：City_Api::getCityById
     * 根据城市ID获取城市信息
     * @param integer $cityId
     * @return array
     */
    public static function getCityById($cityId){
        $objCity = new City_Object_City();
        $objCity->fetch(array('id' => $cityId));
        $ret = $objCity->toArray();
        return $ret;
    }
    
    /**
     * 接口3：City_Api::editCity
     * @param integer $cityId
     * @param array $arrInfo: array('pinyin' => 'xxx','x' => xxxx)
     * @return boolean
     */
    public static function editCity($cityId,$arrInfo){
        $objCity = new City_Object_City();
        $objCity->fetch(array('id' => $cityId));
        if(empty($objCity->id)){
            return false;
        }
        foreach ($arrInfo as $key => $val){
            $objCity->$key = $val;
        }
        return $objCity->save();
    }
    
    /**
     * 接口4：City_Api::addCity
     * @param array $arrInfo : array('name' => 'xxx','cityid' => 'xxx')
     * @return boolean
     */
    public static function addCity($arrInfo){
        $objCity = new City_Object_City();
        foreach ($arrInfo as $key => $val){
            $objCity->$key = $val;
        }
        return $objCity->save();
    }
}
