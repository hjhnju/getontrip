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
        $logic      = new Topic_Logic_Topic();
        $redis      = Base_Redis::getInstance();
        $ret   = $redis->get(Find_Keys::getFindKey($page));
        if(!empty($ret)){
            $ret = json_decode($ret,true);
        }else{
            $ret  = $logic->getNewTopic('',self::DEFAULT_DURING,$page,$pageSize);
            $data = json_encode($ret);
            $redis->setex(Find_Keys::getFindKey($page),self::REDIS_TIMEOUT,$data);
        }        
        foreach ($ret as $key => $val){
            $ret[$key]['dist']  = $this->modelGis->getEarthDistanceToTopic($x,$y,$val['id']);
            $ret[$key]['dist']  = strval(floor($ret[$key]['dist']/1000));            
            $ret[$key]['sight'] = '';
            $ret[$key]['city']  = '';
            $sight              = $this->logicSight->getSightByTopic($val['id'],1,PHP_INT_MAX);
            if(!empty($sight['list'])){
                $sightInfo = $this->logicSight->getSightById($sight['list'][0]['sight_id']);
                $ret[$key]['sight'] = $sightInfo['name'];                
                $cityInfo  = $this->logicCity->getCityById($sightInfo['city_id']);
                $ret[$key]['city']  = $cityInfo['name'];
            }
            $logicComment          = new Comment_Logic_Comment();
            $ret[$key]['comment']  = $logicComment->getTotalCommentNum($val['id']);
            unset($ret[$key]['visit']);
        }
        return $ret;
    }
}