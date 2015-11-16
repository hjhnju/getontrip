<?php
/**
 * 话题详情页接口
 * @author huwei
 *
 */
class ApiController extends Base_Controller_Api {
    
    const PAGESIZE = 6;
    
    public function init() {
        $this->setNeedLogin(false);
        parent::init();        
    }
    
    /**
     * 接口1：/api/topic
     * 话题详情页接口
     * @param integer topicId，话题ID
     * @param integer sightId, 景点ID
     * @return json
     */
    public function indexAction() {
        $topicId    = isset($_REQUEST['topicId'])?intval($_REQUEST['topicId']):'';
        $sightId    = isset($_REQUEST['sightId'])?intval($_REQUEST['sightId']):'';
        if(empty($topicId)){
            return $this->ajaxError(Base_RetCode::PARAM_ERROR,Base_RetCode::getMsg(Base_RetCode::PARAM_ERROR));
        }        
        $logic      = new Topic_Logic_Topic();
        $ret        = $logic->getTopicDetail($topicId,$sightId);
        
        //增加访问统计
        $logicVist          = new Tongji_Logic_Visit();
        $logicVist->addVisit( Tongji_Type_Visit::TOPIC, $topicId);
        
        unset($ret['content']);  
        $this->ajaxDecode($ret);
    }  
}