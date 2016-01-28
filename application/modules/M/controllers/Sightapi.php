<?php
/**
 * 景点页接口
 * @author huwei
 *
 */
class SightapiController extends Base_Controller_Api {
    
    const PAGESIZE = 6;
    
    public function init() {
        $this->setNeedLogin(false);
        parent::init();        
    }        
    
    /**
     * 接口1:/m/sightapi/nearSight
     * 特产列表接口
     * @param integer page
     * @param integer pageSize
     * @param double x
     * @param double y
     * @return json
     */
    public function nearSightAction(){
        $page       = isset($_REQUEST['page'])?intval($_REQUEST['page']):1;
        $pageSize   = isset($_REQUEST['pageSize'])?intval($_REQUEST['pageSize']):self::PAGESIZE;
        $x          = isset($_REQUEST['x'])?doubleval($_REQUEST['x']):'';   
        $y          = isset($_REQUEST['y'])?doubleval($_REQUEST['y']):'';
        $logic      = new Search_Logic_Search();
        $ret        = $logic->getNearSight($page, $pageSize, $x, $y);
        $this->ajax($ret);
    }
}
