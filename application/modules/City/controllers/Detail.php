<?php
/**
 * 城市详情信息
 * @author huwei
 *
 */
class DetailController extends Base_Controller_Page {
    
    
    public function init() {
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
        $cityId     = isset($_POST['cityId'])?intval($_POST['cityId']):'';
        if(empty($cityId)){
           return $this->ajaxError(Base_RetCode::PARAM_ERROR,Base_RetCode::getMsg(Base_RetCode::PARAM_ERROR)); 
        }
        $ret = City_Api::getCityById($cityId);
        if($ret){
            return $this->ajax($ret);
        }
        return $this->ajaxError();
    }  
}
