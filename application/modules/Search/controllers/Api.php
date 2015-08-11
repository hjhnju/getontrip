<?php
/**
 * 搜索页接口
 * @author huwei
 *
 */
class ApiController extends Base_Controller_Api {
    
    const PAGESIZE = 5;
    
    public function init() {
        $this->setNeedLogin(false);
        parent::init();        
    }
    
    /**
     * 接口1：/api/search/
     * 搜索信息接口
     * @param integer page
     * @param integer pageSize
     * @param string  query，查询词
     * @param double  x,经度
     * @param double  y，纬度
     * @return json
     */
    public function indexAction() {
        $page       = isset($_REQUEST['page'])?intval($_REQUEST['page']):1;
        $pageSize   = isset($_REQUEST['pageSize'])?intval($_REQUEST['page']):self::PAGESIZE;
        $query      = isset($_REQUEST['query'])?intval($_REQUEST['query']):'';
        $x          = isset($_REQUEST['x'])?(doubleval($_REQUEST['x'])):'';
        $y          = isset($_REQUEST['y'])?(doubleval($_REQUEST['y'])):'';
        if(empty($query) || empty($x) || empty($y)){
            return $this->ajaxError(Base_RetCode::PARAM_ERROR,Base_RetCode::getMsg(Base_RetCode::PARAM_ERROR));
        }
        $logic      = new Search_Logic_Search();
        $ret        = $logic->search($query, $page, $pageSize, $x, $y);
        $this->ajax($ret);
    }    
}
