<?php
/**
 */
class IndexController extends Base_Controller_Api {
    
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
        //$x         = $_POST['x'];
        //$y         = $_POST['y'];
        //$page      = $_POST['page'];
        $x = 100;
        $y = 100;
        $page = 1;      
        $pageSize = isset($_POST['size'])?$_POST['size']:self::PAGESIZE;             
        $logic = new Home_Logic_List();
        $ret = $logic->getNearSight($x,$y,$page,$pageSize);
        $this->ajax($ret);
    }     
}