<?php
/**
 * 发现页逻辑层
 * @author huwei
 *
 */
class Find_Logic_Find{
    
    protected $modelGis;
    
    protected $logicSight;
    
    protected $logicCity;
    
    public function __construct(){
        $this->modelGis   = new GisModel();
        $this->logicSight = new Sight_Logic_Sight();
        $this->logicCity  = new City_Logic_City();
    }
    
    public function listFind($x,$y,$page,$pageSize){
        $logic      = new Topic_Logic_Topic();
        $ret        = $logic->getNewTopic('','',$page,$pageSize);
        foreach ($ret['list'] as $key => $val){
            $ret['list'][$key]['dist']  = $this->modelGis->getEarthDistanceToTopic($x,$y,$val['id']);
            $ret['list'][$key]['dist']  = strval(floor($ret['list'][$key]['dist']/1000));            
            $ret['list'][$key]['sight'] = '';
            $ret['list'][$key]['city']  = '';
            $sight                      = $this->logicSight->getSightByTopic($val['id'],1,PHP_INT_MAX);
            if(!empty($sight['list'])){
                $sightInfo = $this->logicSight->getSightById($sight['list'][0]['sight_id']);
                $ret['list'][$key]['sight'] = $sightInfo['name'];
                
                $cityInfo  = $this->logicCity->getCityById($sightInfo['city_id']);
                $ret['list'][$key]['city']  = $cityInfo['name'];
            }
        }
        return $ret;
    }
}