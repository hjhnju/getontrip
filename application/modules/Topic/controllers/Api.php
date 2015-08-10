<?php
/**
 * 话题详情页
 * @author huwei
 *
 */
class DetailapiController extends Base_Controller_Api {
    
    const PAGESIZE = 6;
    
    public function init() {
        $this->setNeedLogin(false);
        parent::init();        
    }
    
    /**
     * 接口1：/topic/detail/
     * 话题详情页接口
     * @param integer page
     * @param integer pageSize
     * @param integer topicId，话题ID
     * @param string deviceId，用户的设备ID（因为要统计UV）
     * @return json
     */
    public function indexAction() {
        $page       = isset($_POST['page'])?intval($_POST['page']):1;
        $pageSize   = isset($_POST['pageSize'])?intval($_POST['page']):self::PAGESIZE;
        $topicId    = isset($_POST['topicId'])?intval($_POST['topicId']):'';
        $deviceId   = isset($_POST['deviceId'])?trim($_POST['deviceId']):'';
        $deviceId = 1;
        $topicId = 1;
        if(empty($deviceId) || empty($topicId)){
            return $this->ajaxError(Base_RetCode::PARAM_ERROR,Base_RetCode::getMsg(Base_RetCode::PARAM_ERROR));
        }        
        $logic      = new Topic_Logic_Topic();
        $ret        = $logic->getTopicDetail($topicId,$deviceId,$page,$pageSize);
        $this->ajax($ret);
    }   
}
