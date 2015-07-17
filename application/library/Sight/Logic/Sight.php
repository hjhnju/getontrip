<?php
class Sight_Logic_Sight{
    
    protected $modelSight;
    
    protected $logicTopic;
    
    const DEFAULT_HOT_PERIOD = "1 year ago";
    
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
    public function getSightDetail($sightId,$page,$pageSize,$strTags=''){
        $arrRet   = $this->modelSight->getSightById($sightId);
        $arrTopic =  $this->logicTopic->getHotTopic($sightId,self::DEFAULT_HOT_PERIOD,PHP_INT_MAX);
        if(!empty($strTags)){
            $redis = new Base_Redis();
            $arrTags = explode(",",$strTags);
            foreach ($arrTags as $tag){
                $temp        = $redis->sMembers(Tag_Keys::getTagTopic($tag));
                $arrTopicInc = array_merge($arrTopicInc,$temp);
            }       
            $arrTopicInc = array_unique($arrTopicInc);
            foreach ($arrTopic as $key => $value){
                if(!in_array($value['id'],$arrTopicInc)){
                    unset($arrTopic[$key]);
                }
            }
        }
        $arrRet->topic = array_slice($arrTopic,($page-1)*$pageSize,$page*$pageSize);
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