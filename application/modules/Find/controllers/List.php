<?php
/**
 * 发现页接口
 * @author huwei
 *
 */
class ListController extends Base_Controller_Api {
    
    const PAGESIZE = 6;
    
    public function init() {
        $this->setNeedLogin(false);
        parent::init();        
    }
    
    /**
     * 接口1：/find/list
     * 推荐发现列表页
     * @param integer page
     * @param integer pageSize
     * @return json
     */
    public function indexAction() {
        $page       = isset($_POST['page'])?intval($_POST['page']):1;
        $pageSize   = isset($_POST['pageSize'])?intval($_POST['pageSize']):self::PAGESIZE;
        $x          = isset($_POST['x'])?doubleval($_POST['x']):'';
        $y          = isset($_POST['y'])?doubleval($_POST['y']):'';
        $logic      = new Topic_Logic_Topic();
        $ret        = $logic->getNewTopic('','',$page,$pageSize);
        $this->ajax($ret);
    }    
}
