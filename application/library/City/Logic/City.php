<?php
class City_Logic_City{
    public function __construct(){
        
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
}