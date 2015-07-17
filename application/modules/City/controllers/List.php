<?php
/**
 * 城市列表
 * @author huwei
 *
 */
class ListController extends Base_Controller_Page {
    
    const PAGE_SIZE = 10;
    
    public function init() {
        $this->setNeedLogin(false);
        parent::init();       
    }
    
    /**
     * 接口1：/city/list
     * 获取城市列表信息
     * @param integer page
     * @param integer pageSize
     * @return json
     */
    public function indexAction(){
        $page     = isset($_POST['page'])?intval($_POST['page']):1;
        $pageSize = isset($_POST['pageSize'])?intval($_POST['pageSize']):self::PAGE_SIZE;
        $ret = City_Api::getCityInfo($page, $pageSize);
        if($ret){
            return $this->ajax($ret);
        }
        return $this->ajaxError();
    }  
}
