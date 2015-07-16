<?php
class MyController extends Base_Controller_Api {
    
    public function init() {
        $this->setNeedLogin(true);
        parent::init();        
    }
    
    /**
     * 我的答案接口
     */
    public function indexAction() {
        $deviceId   = $_POST['id'];
        $page     = isset($_POST['page'])?intval($_POST['page']):1;
        $pageSize = isset($_POST['pagesize'])?intval($_POST['pagesize']):self::PAGE_SIZE;
        $logic      = new Answers_Logic_Answers();
        $ret        = $logic->getUserAnswers($deviceId,$page,$pageSize);
        $this->ajax($ret);
    }    
}
