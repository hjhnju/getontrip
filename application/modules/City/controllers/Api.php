<?php
/**
 * 城市信息
 * @author huwei
 *
 */
class ApiController extends Base_Controller_Page {
    
    const PAGE_SIZE       = 8;
    
    const INDEX_TOPIC_NUM = 4;
    
    const DEFAULT_CITY    = '北京';
    
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
     * @param integer page,页码
     * @param integer pageSize,页面大小
     * @return json
     */
    public function detailAction(){
        $cityId   = isset($_REQUEST['cityId'])?intval($_REQUEST['cityId']):'';
        $page     = isset($_REQUEST['page'])?intval($_REQUEST['page']):1;
        $pageSize = isset($_REQUEST['pageSize'])?intval($_REQUEST['pageSize']):self::PAGE_SIZE;
        if(empty($cityId)){
           return $this->ajaxError(Base_RetCode::PARAM_ERROR,Base_RetCode::getMsg(Base_RetCode::PARAM_ERROR)); 
        }
        $ret = $this->_logicCity->getCityDetail($cityId,$page,$pageSize);
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
     * 获取城市话题,定位了城市后刷新话题时使用
     * @param string device，用户的设备ID
     * @param integer city,城市ID
     * @param integer page,页码
     * @param integer pageSize,页面大小
     * @return json
     */
    public function topicAction(){
        $city      = isset($_REQUEST['city'])?trim($_REQUEST['city']):self::DEFAULT_CITY;
        $page      = isset($_REQUEST['page'])?intval($_REQUEST['page']):1;
        $pageSize  = isset($_REQUEST['pageSize'])?intval($_REQUEST['pageSize']):self::INDEX_TOPIC_NUM;
        if(empty($city)){
            return $this->ajaxError(Base_RetCode::PARAM_ERROR,Base_RetCode::getMsg(Base_RetCode::PARAM_ERROR));
        }
        $ret       = $this->_logicCity->getHotTopic($city,$page,$pageSize);
        return $this->ajax($ret);
    }
    
    /**
     * 接口4：/api/city/locate
     * 获取城市信息，判断是否开启
     * @param string city，城市名称可以是中文或英文
     * @return json
     */
    public function locateAction(){
        $city   = isset($_REQUEST['city'])?trim($_REQUEST['city']):self::DEFAULT_CITY;
        if(empty($city)){
            return $this->ajaxError(Base_RetCode::PARAM_ERROR,Base_RetCode::getMsg(Base_RetCode::PARAM_ERROR));
        }
        $ret  = $this->_logicCity->getCityFromName($city);
        return $this->ajax($ret);
    }
}
