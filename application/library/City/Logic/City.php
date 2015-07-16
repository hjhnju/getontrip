<?php
class City_Logic_City{
    
    protected $_model;
    
    public function __construct(){
        $this->_model      = new GisModel();
    }
    
    /**
     * 获取城市信息列表
     * @param integer $page
     * @param integer $pageSize
     * @return array
     */
    public function getCityList($page, $pageSize){
        $listCity = new City_List_City();
        $listCity->setFilterString("`cityid` = 0 and `provinceid` != 0");
        $listCity->setOrder("pinyin asc");
        $listCity->setPage($page);
        $listCity->setPagesize($pageSize);
        return $listCity->toArray();
    }
    
    public function getCityDetail($city,$page,$pageSize,$strTags){
        
    }
    
    /**
     * 根据城市ID获取其经纬度
     * @param integer $cityId
     * @return array
     */
    public function getCityLoc($cityId){
        $objCity = new City_Object_City();
        $objCity->fetch(array('id' => $cityId));
        if(!empty($objCity->id)){
            return array(
                'x' => $objCity->x,
                'y' => $objCity->y,
            );
        }
        return array();
    }
}