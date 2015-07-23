<?php
/**
 * 城市中间页
 * @author huwei
 *
 */
class DetailController extends Base_Controller_Page {
    
    const PAGE_SIZE = 6;
    
    protected $_logicCity;
    
    public function init() {
        $this->_logicCity = new City_Logic_City();
        $this->setNeedLogin(false);
        parent::init();       
    }
    
    /**
     * 接口1：/city/detail
     * 获取城市信息
     * @param integer cityId,城市ID
     * @return json
     */
    public function indexAction(){
        $cityId   = isset($_POST['cityId'])?intval($_POST['cityId']):'';
        $page     = isset($_POST['page'])?intval($_POST['page']):1;
        $pageSize = isset($_POST['pageSize'])?intval($_POST['pageSize']):self::PAGE_SIZE;
        if(empty($cityId)){
           return $this->ajaxError(Base_RetCode::PARAM_ERROR,Base_RetCode::getMsg(Base_RetCode::PARAM_ERROR)); 
        }
        $ret = $this->_logicCity->getCityDetail($cityId,$page,$pageSize);
        if($ret){
            return $this->ajax($ret);
        }
        return $this->ajaxError();
    }  
}
