<?php
/**
 */
class MyController extends Base_Controller_Api {
    
    const PAGESIZE = 6;
    
    public function init() {
        $this->setNeedLogin(false);
        parent::init();        
    }
    
    /**
     * 我的话题信息接口
     */
    public function indexAction() {
        //$page       = $_POST['page'];
        //$pageSize   = $_POST['pageSize'];
        //$deviceId      = $_POST['id'];
        $page = 1;
        $pageSize = 10;
        $deviceId = 1;
        $logic      = new Topic_Logic_Topic();
        $ret        = $logic->getUserTopic($deviceId, $page, $pageSize);
        $this->ajax($ret);
    }    
}
