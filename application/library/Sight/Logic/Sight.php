<?php
class Sight_Logic_Sight{
    
    protected $modelSight;
    
    protected $logicTopic;
    
    const DEFAULT_HOT_PERIOD = "1 year ago";
    
    const ORDER_HOT = 1;
    
    const ORDER_NEW = 2;
    
    public function __construct(){
        $this->modelSight = new SightModel();
        $this->logicTopic = new Topic_Logic_Topic();
    }
    
    /**
     * 根据景点ID获取景点及话题信息，支持带标签筛选及热度的时间范围设置
     * @param integer $sightId
     * @param integer $page
     * @param integer $pageSize
     * @param string $strTags
     * @return array
     */
    public function getSightDetail($sightId,$page,$pageSize,$order,$strTags=''){
        $arrRet  = array();
        $redis   = Base_Redis::getInstance();
        if(self::ORDER_NEW == $order){
            $arrRet =  $this->logicTopic->getNewTopic($sightId,self::DEFAULT_HOT_PERIOD,$page,$pageSize,$strTags);
        }
        $arrTopic =  $this->logicTopic->getHotTopic($sightId,self::DEFAULT_HOT_PERIOD,PHP_INT_MAX,$strTags);
        $arrRet = array_slice($arrTopic,($page-1)*$pageSize,$page*$pageSize);
        
        foreach ($arrRet as $key => $val){
            $arrTags = array();
            $arrTemp = $redis->sGetMembers(Topic_Keys::getTopicTagKey($val['id']));
            foreach ($arrTemp as $id){
                $arrTags[] = $redis->hGet(Tag_Keys::getTagInfoKey(),$id);
            }
            $arrRet[$key]['tags'] = $arrTags;
            unset($arrRet[$key]['visit']);
        }   
        return $arrRet;
    }
    
    /**
     * 获取景点列表
     * @param integer $page
     * @param integer $pageSize
     * @param integer $cityId
     * @return array
     */
    public function getSightList($page,$pageSize,$cityId){
        $arrRet = array();
        if(empty($cityId)){
            $arrSight = $this->modelSight->getSightList($page,$pageSize);
        }else{
            $arrSight = $this->modelSight->getSightByCity($page,$pageSize,$cityId);
        }
        foreach ($arrSight as $index => $val){
            $arrRet[$index]['id']   = $val['id'];
            $arrRet[$index]['name'] = $val['name'];
        }
        return $arrRet;
    }
}