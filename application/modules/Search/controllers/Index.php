<?php
/**
 * 搜索页接口
 * @author huwei
 *
 */
class IndexController extends Base_Controller_Api {
    
    const PAGESIZE = 5;
    
    public function init() {
        $this->setNeedLogin(false);
        parent::init();        
    }
    
    /**
     * 接口1：/search/
     * 搜索信息接口
     * @param integer page
     * @param integer pageSize
     * @param string  query
     * @param double  x
     * @param double  y
     * @return json
     */
    public function indexAction() {
        $page       = isset($_POST['page'])?intval($_POST['page']):1;
        $pageSize   = isset($_POST['pageSize'])?intval($_POST['page']):self::PAGESIZE;
        $query      = isset($_POST['query'])?intval($_POST['query']):'';
        $x          = isset($_POST['x'])?(doubleval($_POST['x'])):'';
        $y          = isset($_POST['y'])?(doubleval($_POST['y'])):'';
        $query = "标";
        $x = 100;
        $y = 100;
        if(empty($query) || empty($x) || empty($y)){
            return $this->ajaxError(Base_RetCode::PARAM_ERROR,Base_RetCode::getMsg(Base_RetCode::PARAM_ERROR));
        }
        $logic      = new Search_Logic_Search();
        $ret        = $logic->search($query, $page, $pageSize, $x, $y);
        $this->ajax($ret);
    }    
}
