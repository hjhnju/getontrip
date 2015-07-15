<?php
/**
 */
class TestController extends Base_Controller_Api {
    
    public function init() {
        $this->setNeedLogin(false);
        parent::init();
        
    }
    
    /**
     * assign至前端邀请url
     * inviteUrl 用户的专属邀请链接
     * userinfo 左上角信息
     */
    public function indexAction() {             
        $logic = new Sight_Logic_Sight();
        $red = $logic->getSightInfo(1);
        $this->ajax($red);
    }  
}
