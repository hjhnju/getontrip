<?php
/**
 * 城市列表搜索页
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
     * @param char filter,前缀字母
     * @param integer page,页码
     * @param integer pageSize,页面大小
     * @return json
     */
    public function indexAction(){
        $filter   = isset($_POST['filter'])?trim($_POST['filter']):'';
        $page     = isset($_POST['page'])?intval($_POST['page']):1;
        $pageSize = isset($_POST['pageSize'])?intval($_POST['pageSize']):self::PAGE_SIZE;
        $ret = City_Api::getCityInfo($page, $pageSize,$filter);
        if($ret){
            return $this->ajax($ret);
        }
        return $this->ajaxError();
    }  
}
