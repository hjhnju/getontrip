<?php
/**
 * 首页数据接口
 * @author huwei
 *
 */
class ApiController extends Base_Controller_Api {
    
    const PAGESIZE = 6;
    
    public function init() {
        $this->setNeedLogin(false);
        parent::init();        
    }
    
    /**
     * 接口1：/api/home
     * 首页数据获取接口
     * @param double x，经度
     * @param double y，纬度
     * @param integer page，页码
     * @param integer pageSize，页面大小
     * @return json
     */
    public function indexAction() {
        $x         = isset($_POST['x'])?doubleval($_POST['x']):'';
        $y         = isset($_POST['y'])?doubleval($_POST['y']):'';
        $page      = isset($_POST['page'])?intval($_POST['page']):1;
        $pageSize  = isset($_POST['size'])?$_POST['size']:self::PAGESIZE;
        $x = 100;
        $y = 100;
        if(empty($x) || empty($y)){
            return $this->ajaxError(Base_RetCode::PARAM_ERROR,Base_RetCode::getMsg(Base_RetCode::PARAM_ERROR));
        }                       
        $logic = new Home_Logic_List();
        $ret = $logic->getNearSight($x,$y,$page,$pageSize);
        return $this->ajax($ret);
    }   

    /**
     * 接口2：/api/home/filter
     * 通过条件过滤的数据获取接口
     * @param integer sight，景点ID，没有可以不传
     * @param integer order，顺序,1:人气，2：最近更新，默认可以不传
     * @param integer page，页码
     * @param integer pageSize，页面大小
     * @param string  tags，用逗号分割的标签ID字符串
     * @return json
     */
    public function filterAction() {
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
    
    /**
     * 接口3：/api/home/city
     * 首页数据获取接口
     * @param integer city，城市ID
     * @param integer page，页码
     * @param integer pageSize，页面大小
     * @return json
     */
    public function cityAction() {
        $page      = isset($_POST['page'])?intval($_POST['page']):1;
        $pageSize  = isset($_POST['size'])?$_POST['size']:self::PAGESIZE;
        $city      = isset($_POST['city'])?intval($_POST['city']):'';
        $logicCity = new City_Logic_City();
        $arr       = $logicCity->getCityLoc($city);
        $arr['x']  = 100;
        $arr['y']  = 100;
        $city      = 2;
        if( empty($city)|| !isset($arr['x'])){
            return $this->ajaxError(Base_RetCode::PARAM_ERROR,Base_RetCode::getMsg(Base_RetCode::PARAM_ERROR));
        }
        $logic = new Home_Logic_List();
        $ret = $logic->getNearSight($arr['x'],$arr['y'],$page,$pageSize);
        return $this->ajax($ret);
    }
}