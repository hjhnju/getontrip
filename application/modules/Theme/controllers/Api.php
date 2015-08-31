<?php
/**
 * 主题详情信息接口
 * @author huwei
 *
 */
class ApiController extends Base_Controller_Page {
    
    const PAGE_SIZE = 6;
    
    protected $logic;
    
    public function init() {
        $this->setNeedLogin(false);
        parent::init();     
        $this->logic = new Theme_Logic_Theme();   
    }
    
    /**
     * 接口1：/api/theme/detail
     * 获取主题详情信息
     * @param integer id，主题 ID
     * @param double x，经度
     * @param double y，纬度
     * @param integer city,城市ID，如果不能给出经纬度可给出城市ID，默认是北京
     * @return json
     */
    public function detailAction(){
        $id        = isset($_REQUEST['id'])?intval($_REQUEST['id']):'';
        $x         = isset($_REQUEST['x'])?doubleval($_REQUEST['x']):'';
        $y         = isset($_REQUEST['y'])?doubleval($_REQUEST['y']):'';
        $city      = isset($_REQUEST['city'])?intval($_REQUEST['city']):2;
        if(empty($id)){
           return $this->ajaxError(Base_RetCode::PARAM_ERROR,Base_RetCode::getMsg(Base_RetCode::PARAM_ERROR));
        } 
        if(empty($x) || empty($y)){
            $logicCity = new City_Logic_City();
            $arr       = $logicCity->getCityLoc($city);
            $x         = $arr['x'];
            $y         = $arr['y'];
        }
        $ret = $this->logic->queryThemeById($id,$x,$y);
        if($ret){
            return $this->ajax($ret);
        }
        return $this->ajaxError();
    }  
    
    /**
     * 接口2：/api/theme/list
     * 获取主题列表信息
     * @param integer page
     * @param integer pageSize
     * @return json
     */
    public function listAction(){
        $page     = isset($_REQUEST['page'])?intval($_REQUEST['page']):1;
        $pageSize = isset($_REQUEST['pageSize'])?intval($_REQUEST['pageSize']):self::PAGE_SIZE;
        $ret      = $this->logic->getThemeList($page,$pageSize);
        return $this->ajax($ret);
    }
}
