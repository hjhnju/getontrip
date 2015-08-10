<?php
/**
 * 添加反馈意见接口
 * @author huwei
 *
 */
class ApiController extends Base_Controller_Api {
    
    public function init() {
        $this->setNeedLogin(true);
        parent::init();        
    }
    
    /**
     * 接口1：/api/advise/add
     * 添加反馈意见接口
     * @param string deviceId，设备ID
     * @param string advise，意见信息
     * @return json
     */
    public function addAction() {
        $deviceId   = isset($_POST['deviceId'])?trim($_POST['deviceId']):'';
        $strAdvise  = isset($_POST['advise'])?trim($_POST['advise']):'';
        if(empty($deviceId) || empty($strAdvise)){
            return $this->ajaxError(Base_RetCode::PARAM_ERROR,Base_RetCode::getMsg(Base_RetCode::PARAM_ERROR));
        }
        $logic     = new Advise_Logic_Advise();
        $ret       = $logic->addAdvise($deviceId, $strAdvise);
        $this->ajax($ret);
    }
    
    /**
     * 接口2：/api/advise/list
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
