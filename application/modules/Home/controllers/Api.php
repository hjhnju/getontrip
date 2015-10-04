<?php
/**
 * 首页数据接口
 * @author huwei
 *
 */
class ApiController extends Base_Controller_Api {
    
    const PAGESIZE = 2;
    
    public function init() {
        $this->setNeedLogin(false);
        parent::init();        
    }
    
    /**
     * 接口1：/api/home
     * 城市中间页首页接口
     * @param string deviceId，设备ID
     * @param string city,城市名称，如果不能给出城市名称，默认是北京
     * @return json
     */
    public function indexAction() {
        $city      = isset($_REQUEST['city'])?trim($_REQUEST['city']):'北京';
        $deviceId  = isset($_REQUEST['deviceId'])?trim($_REQUEST['deviceId']):'';
                      
        $logic = new Home_Logic_List();
        $ret = $logic->getHomeData($city,$deviceId);
        return $this->ajax($ret);
    }  
}