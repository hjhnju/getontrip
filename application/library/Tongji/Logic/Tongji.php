<?php
class Tongji_Logic_Tongji extends Base_Logic{
    
    /**
     * 城市信息统计
     * @param array $arrInfo
     * @param integer $page
     * @param integer $pageSize
     * @return array
     */
    public function city($arrInfo, $page, $pageSize){
        $listCity   = new City_List_City();
        $logicCity  = new City_Logic_City();
        $logicSight = new Sight_Logic_Sight();        
        $listCity->setFilter($arrInfo);
        $listCity->setPage($page);
        $listCity->setPageSize($pageSize);
        $arrCity = $listCity->toArray();
        foreach ($arrCity['list'] as $key => $val){
            $topicNum = $logicCity->getTopicNum($val['id']);
            $sightNum = $logicSight->getSightsNum(array(),$val['id']);
            $arrCity['list'][$key]['info'] = sprintf("%d/%d",$topicNum,$sightNum);
            
            $objCityMeta = new City_Object_Meta();
            $objCityMeta->fetch(array('id' => $val['id']));
            $arrMeta = $objCityMeta->toArray();
            $arrCity['list'][$key] = array_merge($arrCity['list'][$key],$arrMeta);
        }
        return $arrCity;
    }
}