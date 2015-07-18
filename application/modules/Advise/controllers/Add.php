<?php
/**
 * 添加反馈意见接口
 * @author huwei
 *
 */
class AddController extends Base_Controller_Api {
    
    public function init() {
        $this->setNeedLogin(true);
        parent::init();        
    }
    
    /**
     * 接口1：/advise/add
     * 添加反馈意见接口
     * @param integer deviceId，设备ID
     * @param string info，意见信息
     * @return json
     */
    public function indexAction() {
        $deviceId   = isset($_POST['deviceId'])?intval($_POST['deviceId']):'';
        $strAdvise  = isset($_POST['advise'])?intval($_POST['advise']):'';
        if(empty($deviceId) || empty($strAdvise)){
            return $this->ajaxError(Base_RetCode::PARAM_ERROR,Base_RetCode::getMsg(Base_RetCode::PARAM_ERROR));
        }
        $logic     = new Advise_Logic_Advise();
        $ret       = $logic->addAdvise($deviceId, $strAdvise);
        $this->ajax($ret);
    }
    
}
