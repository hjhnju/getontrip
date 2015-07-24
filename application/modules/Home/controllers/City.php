<?php
/**
 * 首页指定城市后数据
 * @author huwei
 *
 */
class CityController extends Base_Controller_Api {
    
    const PAGESIZE = 6;
    
    public function init() {
        $this->setNeedLogin(false);
        parent::init();        
    }
    
    /**
     * 接口1：/home/city
     * 首页数据获取接口
     * @param integer city，城市ID
     * @param integer page，页码
     * @param integer pageSize，页面大小
     * @return json
     */
    public function indexAction() {
        $page      = isset($_POST['page'])?intval($_POST['page']):1;
        $pageSize  = isset($_POST['size'])?$_POST['size']:self::PAGESIZE;
        $city      = isset($_POST['city'])?intval($_POST['city']):'';
        $logicCity = new City_Logic_City();
        $arr       = $logicCity->getCityLoc($city);
        $arr['x']  = 100;
        $arr['y']  = 100;
        if( empty($city)|| !isset($arr['x'])){
            return $this->ajaxError(Base_RetCode::PARAM_ERROR,Base_RetCode::getMsg(Base_RetCode::PARAM_ERROR));
        }                       
        $logic = new Home_Logic_List();
        $ret = $logic->getNearSight($arr['x'],$arr['y'],$page,$pageSize);
        return $this->ajax($ret);
    }          
}