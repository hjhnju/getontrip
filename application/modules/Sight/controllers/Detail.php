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
     * assign至前端邀请url
     * inviteUrl 用户的专属邀请链接
     * userinfo 左上角信息
     */
    public function indexAction() {
        $sightId         = $_POST['sight'];           
        $pageSize = isset($_POST['size'])?$_POST['size']:self::PAGESIZE;  
        $logic = new Sight_Logic_Sight();       
        $ret = $logic->getSightWithTopic($sightId);
        $this->ajax($ret);
    }  
    
    
    
}
