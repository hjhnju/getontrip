<?php
/**
 * 城市信息
 * @author huwei
 *
 */
class ApiController extends Base_Controller_Page {
    
    const PAGE_SIZE = 8;
    
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
        if($ret){
            return $this->ajax($ret);
        }
        return $this->ajaxError();
    }  
    
    /**
     * 接口1：/api/city/list
     * 获取城市列表信息，切换城市时使用
     * @param char filter,前缀字母
     * @param integer page,页码
     * @param integer pageSize,页面大小
     * @return json
     */
    public function listAction(){
        $filter   = isset($_REQUEST['filter'])?trim($_REQUEST['filter']):'';
        $page     = isset($_REQUEST['page'])?intval($_REQUEST['page']):1;
        $pageSize = isset($_REQUEST['pageSize'])?intval($_REQUEST['pageSize']):self::PAGE_SIZE;
        $ret = City_Api::getCityInfo($page, $pageSize,$filter);
        if($ret){
            return $this->ajax($ret);
        }
        return $this->ajaxError();
    }
}
