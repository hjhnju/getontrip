<?php
/**
 * 首页数据接口
 * @author huwei
 *
 */
class ApiController extends Base_Controller_Api {
    
    const PAGESIZE = 2;
    
    const DEFAULT_CITY = 2;
    
    public function init() {
        $this->setNeedLogin(false);
        parent::init();        
    }
    
    /**
     * 接口1：/api/home
     * 城市中间页首页接口
     * @param integer city,城市ID
     * @return json
     */
    public function indexAction() {
        $city      = isset($_REQUEST['city'])?trim($_REQUEST['city']):self::DEFAULT_CITY;                     
        $logic = new Home_Logic_List();
        $ret = $logic->getHomeData($city);
        return $this->ajax($ret);
    } 
}