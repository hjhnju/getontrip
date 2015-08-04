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
     * @param string tags:逗号隔开的id串，如："1,2"
     * @param integer order,次序，1：人气最高，2：最近更新，默认可以不传
     * @return json
     */
    public function indexAction() {
        $page       = isset($_POST['page'])?intval($_POST['page']):1;
        $pageSize   = isset($_POST['pageSize'])?intval($_POST['pageSize']):self::PAGESIZE;
        $sightId    = isset($_POST['sightId'])?intval($_POST['sightId']):'';
        $strTags    = isset($_POST['tags'])?trim($_POST['tags']):'';
        $intOrder   = isset($_POST['order'])?intval($_POST['order']):2;
        $sightId = 1;
        $logic      = new Sight_Logic_Sight();
        $ret        = $logic->getSightDetail($sightId,$page,$pageSize,$intOrder,$strTags);
        $this->ajax($ret);
    }
    
}
