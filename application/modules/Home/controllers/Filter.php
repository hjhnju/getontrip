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
     * @param integer city，城市ID,没有可以不传
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
        $city      = isset($_POST['city'])?intval($_POST['city']):'';
        $sight     = isset($_POST['sight'])?intval($_POST['sight']):'';
        $strTags   = isset($_POST['tags'])?trim($_POST['tags']):''; 
        if(empty($city)){
            return $this->ajaxError(Base_RetCode::PARAM_ERROR,Base_RetCode::getMsg(Base_RetCode::PARAM_ERROR));
        }
        $logicCity  = new City_Logic_City();
        $arr        = $logicCity->getCityLoc($city);
        if(isset($arr['x'])){            
            $logic  = new Home_Logic_List();
            $ret    = $logic->getFilterSight($arr['x'],$arr['y'],$page,$pageSize,$order,$sight,$strTags);
            return $this->ajax($ret);
        }else{
            return $this->ajaxError();
        }
    }     
}