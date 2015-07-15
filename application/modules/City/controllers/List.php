<?php
/**
 */
class ListController extends Base_Controller_Page {
    
    const PAGE_SIZE = 10;
    
    protected $logic;
    
    public function init() {
        $this->setNeedLogin(false);
        parent::init();     
        $this->logic = new City_Logic_City();   
    }
    
    /**
     * 获取城市信息
     */
    public function indexAction(){
        $page     = isset($_POST['page'])?intval($_POST['page']):1;
        $pageSize = isset($_POST['pagesize'])?intval($_POST['pagesize']):self::PAGE_SIZE;
        $ret = $this->logic->getCityList($page, $pageSize);
        if($ret){
            return $this->ajax($ret);
        }
        return $this->ajaxError();
    }  
}
