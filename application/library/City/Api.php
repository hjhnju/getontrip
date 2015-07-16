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
    public static function getCityInfo($page, $pageSize){
        $listCity = new City_List_City();
        $listCity->setFilterString("`cityid` = 0 and `provinceid` != 0");
        $listCity->setPage($page);
        $listCity->setPagesize($pageSize);
        $arrCity = $listCity->toArray();
        foreach ($arrCity['list'] as $key => $val){
            $city = City_Api::getCityById($val['pid']);
            $arrCity['list'][$key]['pidname'] = $city['name'];
        }
        return $arrCity['list'];
    }
    
    /**
     * 接口2：City_Api::getCityById($cityId)
     * 根据城市ID获取城市信息
     * @param integer $cityId
     * @return array
     */
    public static function getCityById($cityId){
        $objCity = new City_Object_City();
        $objCity->fetch(array('id' => $cityId));
        $ret = $objCity->toArray();
        $city = City_Api::getCityById($ret['pid']);
        $ret['pidname'] = $city['name'];
        return $ret;
    }
    
    /**
     * 接口3：City_Api::editCity($cityId,$arrInfo)
     * 修改城市信息
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
     * 接口4：City_Api::addCity($arrInfo)
     * 添加新的城市
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
    
    /**
     * 接口5：City_Api::queryCity($arrInfo,$page,$pageSize)
     * @param array $arrInfo:过滤条件，如:array("status"=>1);
     * @param integer $page
     * @param integer $pageSize
     * @return array
     */
    public static function queryCity($arrInfo,$page,$pageSize){
        $listCity = new City_List_City();
        $strFileter = "`cityid` = 0 and `provinceid` != 0";
        foreach ($arrInfo as $key => $val){
            $strFileter .=" and `".$key."` = $val";
        }
        $listCity->setFilterString("$strFileter");
        $listCity->setPage($page);
        $listCity->setPagesize($pageSize);
        $arrCity = $listCity->toArray();
        foreach ($arrCity['list'] as $key => $val){
            $city = City_Api::getCityById($val['pid']);
            $arrCity['list'][$key]['pidname'] = $city['name'];
        }
        return $arrCity['list'];
    }
}
