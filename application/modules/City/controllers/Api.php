<?php
/**
 * 城市信息
 * @author huwei
 *
 */
class ApiController extends Base_Controller_Page {
    
    const PAGE_SIZE = 8;
    
    const INDEX_TOPIC_NUM = 4;
    
    protected $_logicCity;
    
    public function init() {
        $this->_logicCity = new City_Logic_City();
        $this->setNeedLogin(false);
        parent::init();       
    }
    
    /**
     * 接口1：/api/city/detail
     * 获取城市信息,供城市中间页使用
     * @param integer cityId,城市ID
     * @param string device，用户的设备ID
     * @param integer page,页码
     * @param integer pageSize,页面大小
     * @return json
     */
    public function detailAction(){
        $cityId   = isset($_REQUEST['cityId'])?intval($_REQUEST['cityId']):'';
        $deviceId = isset($_REQUEST['device'])?trim($_REQUEST['device']):'';
        $page     = isset($_REQUEST['page'])?intval($_REQUEST['page']):1;
        $pageSize = isset($_REQUEST['pageSize'])?intval($_REQUEST['pageSize']):self::PAGE_SIZE;
        if(empty($cityId)){
           return $this->ajaxError(Base_RetCode::PARAM_ERROR,Base_RetCode::getMsg(Base_RetCode::PARAM_ERROR)); 
        }
        $ret = $this->_logicCity->getCityDetail($cityId,$deviceId,$page,$pageSize);
        return $this->ajax($ret);
    }  
    
    /**
     * 接口2：/api/city/list
     * 获取城市列表信息，切换城市时使用
     * @return json
     */
    public function listAction(){
        $ret = City_Api::getCityInfo();
        return $this->ajax($ret);
    }
    
    /**
     * 接口3：/api/city/topic
     * 获取城市话题,首页中使用
     * @param string device，用户的设备ID
     * @param integer page,页码
     * @param integer pageSize,页面大小
     * @return json
     */
    public function topicAction(){
        $filter    = isset($_REQUEST['device'])?trim($_REQUEST['device']):'';
        $city      = isset($_REQUEST['city'])?trim($_REQUEST['city']):'北京';
        $page      = isset($_REQUEST['page'])?intval($_REQUEST['page']):1;
        $pageSize  = isset($_REQUEST['pageSize'])?intval($_REQUEST['pageSize']):self::INDEX_TOPIC_NUM;
        $logicCity = new City_Logic_City();
        $ret       = $logicCity->getHotTopic($city,$page,$pageSize);
        return $this->ajax($ret);
    }
}
