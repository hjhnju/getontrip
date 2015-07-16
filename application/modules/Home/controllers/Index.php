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
     * 首页数据获取接口
     */
    public function indexAction() {
        //$x         = $_POST['x'];
        //$y         = $_POST['y'];
        //$page      = $_POST['page'];
        $pageSize = isset($_POST['size'])?$_POST['size']:self::PAGESIZE;
        $x = 100;
        $y = 100;
        $page = 1;                
        $logic = new Home_Logic_List();
        $ret = $logic->getNearSight($x,$y,$page,$pageSize);
        $this->ajax($ret);
    }       
    
}