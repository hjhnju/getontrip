<?php
/**
 */
class DetailController extends Base_Controller_Api {
    
    public function init() {
        $this->setNeedLogin(false);
        parent::init();        
    }
    
    /**
     * 答案详情接口
     */
    public function indexAction() {
        $answerId   = $_POST['id'];
        $logic      = new Answers_Logic_Answers();
        $ret        = $logic->getAnswerDetail($answerId);
        $this->ajax($ret);
    }
    
}
