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
        $page       = isset($_POST['page'])?intval($_POST['page']):1;
        $pageSize   = isset($_POST['pageSize'])?intval($_POST['pageSize']):self::PAGESIZE;
        $sightId    = isset($_POST['sightId'])?intval($_POST['sightId']):'';
        $strTags    = isset($_POST['tags'])?trim($_POST['tags']):'';
        $page = 1;
        $pageSize = 10;
        $sightId = 1;
        $logic      = new Sight_Logic_Sight();
        $ret        = $logic->getSightDetail($sightId,$page,$pageSize,$strTags);
        $this->ajax($ret);
    }
    
}
