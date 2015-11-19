<?php
/**
 * 搜索页接口
 * @author huwei
 *
 */
class ApiController extends Base_Controller_Api {
    
    const PAGESIZE = 4;
    
    const HOT_WORD_NUM = 10;
    
    public function init() {
        $this->setNeedLogin(false);
        parent::init();        
    }
        
    /**
     * 接口1：/api/search/label
     * 搜索列表页入口二
     * @param integer page
     * @param integer pageSize
     * @param integer order,搜索标签
     * @return json
     */
    public function labelAction() {
        $page       = isset($_REQUEST['page'])?intval($_REQUEST['page']):1;
        $pageSize   = isset($_REQUEST['pageSize'])?intval($_REQUEST['pageSize']):self::PAGESIZE;
        $labelId    = isset($_REQUEST['order'])?intval($_REQUEST['order']):'';         
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
        $pageSize   = isset($_REQUEST['pageSize'])?intval($_REQUEST['pageSize']):self::PAGESIZE;
        $query      = isset($_REQUEST['query'])?trim($_REQUEST['query']):'*';         
        $logic      = new Search_Logic_Search();
        $ret        = $logic->search($query, $page, $pageSize, $type);
        $this->ajaxDecode($ret);
    }
    
    /**
     * 接口3：/api/search/hotWord
     * 热门搜索词接口
     * @param integer size,条数，默认是10
     */
    public function hotWordAction(){
        $size       = isset($_REQUEST['size'])?intval($_REQUEST['size']):self::HOT_WORD_NUM;
        $logic      = new Search_Logic_Word();
        $arrContent = $logic->getSearchHotWord($size);
        return $this->ajax($arrContent);
    }
    
}
