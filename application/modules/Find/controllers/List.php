<?php
/**
 */
class ListController extends Base_Controller_Api {
    
    const PAGESIZE = 6;
    
    public function init() {
        $this->setNeedLogin(false);
        parent::init();        
    }
    
    /**
     * 推荐发现列表页
     */
    public function indexAction() {
        //$page       = $_POST['page'];
        //$pageSize   = $_POST['pageSize'];
        //$sight      = $_POST['topic'];
        $page = 1;
        $pageSize = 10;
        $logic      = new Topic_Logic_Topic();
        $ret        = $logic->getHotTopic(0,'7 day ago',$page*$pageSize);
        $ret        = array_slice($ret,($page-1)*$pageSize,$pageSize);
        $this->ajax($ret);
    }
    
}
