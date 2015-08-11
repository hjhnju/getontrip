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
     * 接口1：/api/topic/detail
     * 话题详情页接口
     * @param integer topicId，话题ID
     * @param string deviceId，用户的设备ID（因为要统计UV）
     * @return json
     */
    public function detailAction() {
        $topicId    = isset($_REQUEST['topicId'])?intval($_REQUEST['topicId']):'';
        $deviceId   = isset($_REQUEST['deviceId'])?trim($_REQUEST['deviceId']):'';
        if(empty($deviceId) || empty($topicId)){
            return $this->ajaxError(Base_RetCode::PARAM_ERROR,Base_RetCode::getMsg(Base_RetCode::PARAM_ERROR));
        }        
        $logic      = new Topic_Logic_Topic();
        $ret        = $logic->getTopicDetail($topicId,$deviceId);
        $this->ajax($ret);
    }  

    /**
     * 接口2：获取话题列表 /api/topic/list
     * @param integer page
     * @param integer pageSize
     * @param integer sight，景点ID
     * @param integer order，排序次序，1：热门，2：最近更新，默认可以不传
     * @param string  tags，标签ID串，逗号分隔
     * @return json
     */
    public function listAction() {
        $x         = isset($_REQUEST['x'])?doubleval($_REQUEST['x']):'';
        $y         = isset($_REQUEST['y'])?doubleval($_REQUEST['y']):'';
        $city      = isset($_REQUEST['city'])?intval($_REQUEST['city']):2;
        $page      = isset($_REQUEST['page'])?intval($_REQUEST['page']):1;
        $pageSize  = isset($_REQUEST['pageSize'])?$_REQUEST['pageSize']:self::PAGESIZE;
        $order     = isset($_REQUEST['order'])?intval($_REQUEST['order']):'';
        $sight     = isset($_REQUEST['sight'])?intval($_REQUEST['sight']):'';
        $strTags   = isset($_REQUEST['tags'])?trim($_REQUEST['tags']):'';
        if(empty($sight)){
            return $this->ajaxError(Base_RetCode::PARAM_ERROR,Base_RetCode::getMsg(Base_RetCode::PARAM_ERROR));
        }
        if(empty($x) || empty($y)){
            $logicCity = new City_Logic_City();
            $arr       = $logicCity->getCityLoc($city);
            $x         = $arr['x'];
            $y         = $arr['y'];
        }
        $logic  = new Home_Logic_List();
        $ret    = $logic->getFilterSight($page,$pageSize,$order,$sight,$strTags);
        $this->ajax($ret);
    }
}
