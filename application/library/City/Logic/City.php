<?php
class City_Logic_City{
    
    const HOTPERIOD = '1 month ago';
    
    protected $_modeSight;
    
    public function __construct(){
        $this->_modeSight = new SightModel();
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
    
    /**
     * 根据城市ID获取城市信息，包含景点及话题信息，景点按话题热度排序
     * @param integer $cityId
     * @param integer $page
     * @param integer $pageSize
     * @return array
     */
    public function getCityDetail($cityId,$page,$pageSize){
        $arrHot     = array();
        $logicTopic = new Topic_Logic_Topic();
        $redis      = Base_Redis::getInstance();
        $ret        = City_Api::getCityById($cityId);
        $arrSight   = $this->_modeSight->getSightByCity($page, $pageSize, $cityId);
        foreach ($arrSight as $key => $val){
            $ret    = $redis->zRange(Sight_Keys::getSightTopicName($val['id']),0,-1);
            $hot    = 0;
            foreach ($ret as $topicId){
                $hot += $logicTopic->getTopicHotDegree($topicId, self::HOTPERIOD);
            }
            $arrHot[] = $hot;            
            $arrSight[$key]['topics'] = count($redis->zRange(Sight_Keys::getSightTopicName($val['id']),0,-1));
        }
        array_multisort($arrHot, SORT_DESC , $arrSight);
        $ret['sights'] = $arrSight;
        return $ret;
    }
}