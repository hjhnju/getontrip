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
     * 首页数据获取接口
     * @param double x，经度
     * @param double y，纬度
     * @param integer page，页码
     * @param integer pageSize，页面大小
     * @param integer city,城市ID，如果不能给出经纬度可给出城市ID，默认是北京
     * @return json
     */
    public function indexAction() {
        $x         = isset($_REQUEST['x'])?doubleval($_REQUEST['x']):'';
        $y         = isset($_REQUEST['y'])?doubleval($_REQUEST['y']):'';
        $city      = isset($_REQUEST['city'])?intval($_REQUEST['city']):2;
        $page      = isset($_REQUEST['page'])?intval($_REQUEST['page']):1;
        $pageSize  = isset($_REQUEST['pageSize'])?intval($_REQUEST['pageSize']):self::PAGESIZE;
        if(empty($x) || empty($y)){
            $_SESSION[Home_Keys::SESSION_USER_CITY] = $city;
            $logicCity = new City_Logic_City();
            $arr       = $logicCity->getCityLoc($city);
            $x         = $arr['x'];
            $y         = $arr['y'];
        }                       
        $logic = new Home_Logic_List();
        $ret = $logic->getNearSight($x,$y,$page,$pageSize);
        return $this->ajax($ret);
    }  
}