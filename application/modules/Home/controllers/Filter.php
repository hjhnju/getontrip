<?php
/**
 * 首页筛选排序后数据接口
 * @author huwei
 *
 */
class FilterController extends Base_Controller_Api {
    
    const PAGESIZE = 6;
    
    public function init() {
        $this->setNeedLogin(false);
        parent::init();        
    }
    
    /**
     * 接口1：/home/filter
     * 通过条件过滤的数据获取接口
     * @param integer sight，景点ID，没有可以不传
     * @param integer order，顺序,1:人气，2：最近更新，默认可以不传
     * @param integer page，页码
     * @param integer pageSize，页面大小
     * @param string  tags，用逗号分割的标签ID字符串
     * @return json 
     */
    public function indexAction() {
        $page      = isset($_POST['page'])?intval($_POST['page']):1;
        $pageSize  = isset($_POST['pageSize'])?$_POST['pageSize']:self::PAGESIZE;
        $order     = isset($_POST['order'])?intval($_POST['order']):'';
        $sight     = isset($_POST['sight'])?intval($_POST['sight']):'';
        $strTags   = isset($_POST['tags'])?trim($_POST['tags']):''; 
        if(empty($x) || empty($y) || empty($sight)){
            return $this->ajaxError(Base_RetCode::PARAM_ERROR,Base_RetCode::getMsg(Base_RetCode::PARAM_ERROR));
        }
                  
        $logic  = new Home_Logic_List();
        $ret    = $logic->getFilterSight($page,$pageSize,$order,$sight,$strTags);
        return $this->ajaxError();
    } 
}