<?php
/**
 * 景点详情接口
 * @author huwei
 *
 */
class DetailController extends Base_Controller_Api {
    
    const PAGESIZE = 6;
    
    public function init() {
        $this->setNeedLogin(false);
        parent::init();        
    }
    
    /**
     * 接口1：/sight/detail
     * 景点详情接口
     * @param integer sightId
     * @param integer page
     * @param integer pageSize
     * @return json
     */
    public function indexAction() {
        //$page       = $_POST['page'];
        //$pageSize   = $_POST['pageSize'];
        //$sightId    = $_POST['sightId'];
        $page = 1;
        $pageSize = 10;
        $sightId = 1;
        $logic      = new Sight_Logic_Sight();
        $ret        = $logic->getSightDetail($sightId,$page,$pageSize);
        $this->ajax($ret);
    }
    
}
