<?php
/**
 * 发现页逻辑层
 * @author huwei
 *
 */
class Find_Logic_Find{
    
    //默认的最近天数
    const DEFAULT_DURING = 7;
    
    //缓存过期时间一小时
    const REDIS_TIMEOUT  = 3600;
    
    protected $modelGis;
    
    protected $logicSight;
    
    protected $logicCity;
    
    public function __construct(){
        $this->modelGis   = new GisModel();
        $this->logicSight = new Sight_Logic_Sight();
        $this->logicCity  = new City_Logic_City();
    }
    
    public function listFind($x,$y,$page,$pageSize){
        $logic   = new Topic_Logic_Topic();
        $ret     = $logic->getHotTopic('',self::DEFAULT_DURING,$page,$pageSize);
        foreach ($ret as $key => $val){
            $sightId = '';                   
            $ret[$key]['sight'] = '';
            $ret[$key]['city']  = '';
            $sight              = $this->logicSight->getSightByTopic($val['id'],1,1);
            if(!empty($sight['list'])){
                $sightInfo = $this->logicSight->getSightById($sight['list'][0]['sight_id']);
                $ret[$key]['sight'] = $sightInfo['name'];                
                $cityInfo  = $this->logicCity->getCityById($sightInfo['city_id']);
                $ret[$key]['city']  = $cityInfo['name'];
                $sightId = $sight['list'][0]['sight_id'];
            }
            if(!empty($val['x']) && !empty($val['y'])){
                $ret[$key]['dist']  = $this->modelGis->getEarthDistanceToTopic($x,$y,$val['id']);
            }else{            
                $ret[$key]['dist']  = $this->modelGis->getEarthDistanceToSight($x, $y, $sightId);
            }
            $ret[$key]['dist']  = Base_Util_Number::getDis($ret[$key]['dist']) ;
            $logicComment          = new Comment_Logic_Comment();
            $ret[$key]['comment']  = $logicComment->getTotalCommentNum($val['id']);
            unset($ret[$key]['visit']);
        }
        return $ret;
    }
}