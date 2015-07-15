<?php
/**
 */
class FilterController extends Base_Controller_Api {
    
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
        $order     = isset($_POST['order'])?intval($_POST['order']):-1;
        $city      = isset($_POST['city'])?intval($_POST['city']):-1;
        $sight     = isset($_POST['sight'])?intval($_POST['sight']):-1;
        $strTags   = isset($_POST['tags'])?trim($_POST['tags']):'';       
        $pageSize  = isset($_POST['size'])?$_POST['size']:self::PAGESIZE;             
        $logic     = new Home_Logic_List();
        $ret       = $logic->getFilterSight($x,$y,$page,$pageSize);
        $this->ajax($ret);
    }     
}