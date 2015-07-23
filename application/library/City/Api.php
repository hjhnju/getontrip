<?php
/**
 * 城市API
 * @author huwei
 *
 */
class City_Api{
    
    /**
     * 接口1：City_Api::getCityInfo($page, $pageSize)
     * 获取城市信息
     * @param integer $page
     * @param integer $pageSize
     * @return array
     */
    public static function getCityInfo($page, $pageSize,$filter=''){
       $logicCity = new City_Logic_City();
       return $logicCity->getCityInfo($page, $pageSize,$filter);
    }
    
    /**
     * 接口2：City_Api::getCityById($cityId)
     * 根据城市ID获取城市信息
     * @param integer $cityId
     * @return array
     */
    public static function getCityById($cityId){
        $logicCity = new City_Logic_City();
        return $logicCity->getCityById($cityId);
    }
    
    /**
     * 接口3：City_Api::editCity($cityId,$arrInfo)
     * 修改城市信息
     * @param integer $cityId
     * @param array $arrInfo: array('pinyin' => 'xxx','x' => xxxx)
     * @return boolean
     */
    public static function editCity($cityId,$arrInfo){
        $logicCity = new City_Logic_City();
        return $logicCity->editCity($cityId, $arrInfo);
    }
    
    /**
     * 接口4：City_Api::addCity($arrInfo)
     * 添加新的城市
     * @param array $arrInfo : array('name' => 'xxx','cityid' => 'xxx')
     * @return boolean
     */
    public static function addCity($arrInfo){
        $logicCity = new City_Logic_City();
        return $logicCity->addCity($arrInfo);
    }
    
    /**
     * 接口5：City_Api::queryCity($arrInfo,$page,$pageSize)
     * 查询城市
     * @param array $arrInfo:过滤条件，如:array("status"=>1);
     * @param integer $page
     * @param integer $pageSize
     * @return array
     */
    public static function queryCity($arrInfo,$page,$pageSize){
        $logicCity = new City_Logic_City();
        return $logicCity->queryCity($arrInfo, $page, $pageSize);
    }
    
    /**
     * 接口6：City_Api::getProvinceList($page,$pageSize)
     * 获取省的信息列表
     * @param integer $page
     * @param integer $pageSize
     * @return array
     */
    public static function getProvinceList($page,$pageSize){
        $logicCity = new City_Logic_City();
        return $logicCity->getProvinceList($page, $pageSize);
    }
    
    /**
     * 接口7：City_Api::queryCityPrefix($str,$page,$pageSize)
     * 城市名前缀模糊查询
     * @param string $str
     * @param integer $page
     * @param integer $pageSize
     * @return array
     */
    public static function queryCityPrefix($str,$page,$pageSize){
        $logicCity = new City_Logic_City();
        return $logicCity->queryCityPrefix($str, $page, $pageSize);
    }
    
    /**
     * 接口8：City_Api::queryProvincePrefix($str,$page,$pageSize)
     * 省份名前缀模糊查询
     * @param string $str
     * @param integer $page
     * @param integer $pageSize
     * @return array
     */
    public static function queryProvincePrefix($str,$page,$pageSize){
        $logicCity = new City_Logic_City();
        return $logicCity->queryProvincePrefix($str, $page, $pageSize);
    }
}
