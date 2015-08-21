<?php
/**
 * 发现页接口
 * @author huwei
 *
 */
class ApiController extends Base_Controller_Api {
    
    const PAGESIZE = 6;
    
    protected $logicFind;
    
    public function init() {
        $this->setNeedLogin(false);
        parent::init();  
        $this->logicFind = new Find_Logic_Find();      
    }
    
    /**
     * 接口1：/api/find
     * 推荐发现列表页
     * @param integer page
     * @param integer pageSize
     * @param double x
     * @param double y 
     * @param integer city，若获取不了经纬度则传城市ID，城市ID不传默认是北京
     * @return json
     */
    public function indexAction() {
        $x          = isset($_REQUEST['x'])?doubleval($_REQUEST['x']):'';
        $y          = isset($_REQUEST['y'])?doubleval($_REQUEST['y']):'';
        $city      = isset($_REQUEST['city'])?intval($_REQUEST['city']):2;
        $page      = isset($_REQUEST['page'])?intval($_REQUEST['page']):1;
        $pageSize  = isset($_REQUEST['pageSize'])?intval($_REQUEST['pageSize']):self::PAGESIZE;
        if(empty($x) || empty($y)){
            $logicCity = new City_Logic_City();
            $arr       = $logicCity->getCityLoc($city);
            $x         = $arr['x'];
            $y         = $arr['y'];
        }
        $ret        = $this->logicFind->listFind($x,$y,$page,$pageSize);
        $this->ajax($ret);
    }    
}
