<?php
/**
 */
class DetailController extends Base_Controller_Api {
    
    const PAGESIZE = 6;
    
    public function init() {
        $this->setNeedLogin(false);
        parent::init();        
    }
    
    /**
     * 话题详情接口
     */
    public function indexAction() {
        //$page       = $_POST['page'];
        //$pageSize   = $_POST['pageSize'];
        //$sight      = $_POST['topic'];
        $page = 1;
        $pageSize = 10;
        $topic = 1;
        $logic      = new Topic_Logic_Topic();
        $ret        = $logic->getTopicDetail($topic,$page,$pageSize);
        $this->ajax($ret);
    }
    
}
