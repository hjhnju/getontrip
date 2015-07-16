<?php
/**
 */
class AddController extends Base_Controller_Api {
    
    public function init() {
        $this->setNeedLogin(true);
        parent::init();        
    }
    
    /**
     * 添加答案接口
     */
    public function indexAction() {
        $id     = $_POST['id'];
        $info   = $_POST['info']; 
        $logic  = new Answers_Logic_Answers();
        $ret    = $logic->addAnswer(id,$info);
        $this->ajax($ret);
    }
    
}
