<?php
class Sight_Logic_Sight{
    
    protected $modelGis;
    
    public function __construct(){
        $this->modelGis = new GisModel();
    }
    
    /**
     * 根据景点ID获取景点及话题信息
     * @param unknown $sightId
     * @return multitype:
     */
    public function getSightFullInfo($sightId){
        $arrRet = $this->modelGis->getSightById($sightId);
        if(!empty($arrRet)){
            $redis = Base_Redis::getInstance();
            $redis->hGet('','');
        }
        return array();
    }
}