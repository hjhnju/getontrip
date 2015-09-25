<?php
/**
 * 搜索页接口
 * @author huwei
 *
 */
class ApiController extends Base_Controller_Api {
    
    const PAGESIZE = 4;
    
    public function init() {
        $this->setNeedLogin(false);
        parent::init();        
    }
        
    /**
     * 接口1：/api/search/label
     * 搜索列表页入口二
     * @param integer page
     * @param integer pageSize
     * @param integer label,搜索标签
     * @return json
     */
    public function labelAction() {
        $page       = isset($_REQUEST['page'])?intval($_REQUEST['page']):1;
        $pageSize   = isset($_REQUEST['pageSize'])?intval($_REQUEST['page']):self::PAGESIZE;
        $labelId    = isset($_REQUEST['label'])?intval($_REQUEST['label']):'';
         
        $logic      = new Search_Logic_Search();
        $ret        = $logic->label($labelId, $page, $pageSize);
        $this->ajax($ret);
    }
    
    /**
     * 接口2：/api/search/
     * 搜索信息接口
     * @param integer type,搜索类型 1:景点,2:城市,3:内容,默认可以不传,查看更多搜索内容时要传
     * @param integer page
     * @param integer pageSize
     * @param string  query，查询词
     * @return json
     */
    public function indexAction() {
        $type       = isset($_REQUEST['type'])?intval($_REQUEST['type']):'';
        $page       = isset($_REQUEST['page'])?intval($_REQUEST['page']):1;
        $pageSize   = isset($_REQUEST['pageSize'])?intval($_REQUEST['page']):self::PAGESIZE;
        $query      = isset($_REQUEST['query'])?strval($_REQUEST['query']):'*';
         
        $logic      = new Search_Logic_Search();
        $ret        = $logic->search($query, $page, $pageSize, $type);
        $this->ajax($ret);
    }
    
}
