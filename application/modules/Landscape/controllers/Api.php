<?php
/**
 * 景观页接口
 * @author huwei
 *
 */
class ApiController extends Base_Controller_Api {
    
    public function init() {
        $this->setNeedLogin(false);
        parent::init();        
    }
    
    /**
     * 接口1：/api/landscape
     * 景观详情信息接口
     * @param integer id
     * @param double  x,经度
     * @param double  y，纬度
     * @return json
     */
    public function indexAction() {
        $id    = isset($_REQUEST['id'])?intval($_REQUEST['id']):'';
        $x     = isset($_REQUEST['x'])?doubleval($_REQUEST['x']):'';
        $y     = isset($_REQUEST['y'])?doubleval($_REQUEST['y']):'';
        if(empty($id)||empty($x)||empty($y)){
            return $this->ajaxError(Base_RetCode::PARAM_ERROR,Base_RetCode::getMsg(Base_RetCode::PARAM_ERROR));
        }
        $logic      = new Landscape_Logic_Landscape();
        $ret        = $logic->queryLandscapeById($id,$x,$y);
        $this->ajax($ret);
    }    
}