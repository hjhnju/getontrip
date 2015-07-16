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
     * 通过条件过滤的数据获取接口
     */
    public function indexAction() {
        //$x         = $_POST['x'];
        //$y         = $_POST['y'];
        //$page      = $_POST['page'];
        $pageSize  = isset($_POST['size'])?$_POST['size']:self::PAGESIZE;
        $x = 100;
        $y = 100;
        $page = 1;
        $order     = isset($_POST['order'])?intval($_POST['order']):-1;
        $city      = isset($_POST['city'])?intval($_POST['city']):-1;
        $sight     = isset($_POST['sight'])?intval($_POST['sight']):-1;
        $strTags   = isset($_POST['tags'])?trim($_POST['tags']):'';                   
        $logic     = new Home_Logic_List();
        $ret       = $logic->getFilterSight($x,$y,$page,$pageSize,$order,$city,$sight,$strTags);
        $this->ajax($ret);
    }     
    
    /**
     * 切换城市后获取首页数据
     */
    public function cityAction() {
        //$cityId  = $_POST['id'];
        //$x       = $_POST['x'];
        //$y       = $_POST['y'];
        //$page    = $_POST['page'];
        $pageSize  = isset($_POST['size'])?$_POST['size']:self::PAGESIZE;
        $x = 100;
        $y = 100;
        $page = 1;
        $order     = isset($_POST['order'])?intval($_POST['order']):-1;
        $cityId    = isset($_POST['city'])?intval($_POST['city']):-1;
        $sight     = isset($_POST['sight'])?intval($_POST['sight']):-1;
        $strTags   = isset($_POST['tags'])?trim($_POST['tags']):''; 
        $logicCity = new City_Logic_City();
        $arr = $logicCity->getCityLoc($cityId);
        if(empty($arr)){
           return $this->ajaxError(); 
        }
        $logic = new Home_Logic_List();
        $ret = $logic->getFilterSight($x,$y,$page,$pageSize,$order,$city,$sight,$strTags);
        $this->ajax($ret);
    }
}