<?php
/**
 * 查询反馈意见接口
 * @author huwei
 *
 */
class ListController extends Base_Controller_Api {
    
    public function init() {
        $this->setNeedLogin(true);
        parent::init();        
    }
    
    /**
     * 接口1：/advise/list
     * 查询反馈意见接口
     * @param string deviceId，设备ID
     * @return json
     */
    public function indexAction() {
        $deviceId   = isset($_POST['deviceId'])?trim($_POST['deviceId']):'';
        if(empty($deviceId)){
            return $this->ajaxError(Base_RetCode::PARAM_ERROR,Base_RetCode::getMsg(Base_RetCode::PARAM_ERROR));
        }
        $logic     = new Advise_Logic_Advise();
        $ret       = $logic->listAdvise($deviceId);
        $this->ajax($ret);
    }
    
}
