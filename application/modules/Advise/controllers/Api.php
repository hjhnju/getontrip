<?php
/**
 * 添加反馈意见接口
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
     * 接口1：/api/advise/add
     * 添加反馈意见接口
     * @param string deviceId，设备ID
     * @param string advise，意见信息
     * @return json
     */
    public function addAction() {
        $deviceId   = isset($_REQUEST['deviceId'])?trim($_REQUEST['deviceId']):'';
        $strAdvise  = isset($_REQUEST['advise'])?trim($_REQUEST['advise']):'';
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
    public function listAction() {
        $deviceId  = isset($_REQUEST['deviceId'])?trim($_REQUEST['deviceId']):'';
        $page      = isset($_REQUEST['page'])?intval($_REQUEST['page']):1;
        $pageSize  = isset($_REQUEST['pageSize'])?intval($_REQUEST['pageSize']):self::PAGESIZE;
        if(empty($deviceId)){
            return $this->ajaxError(Base_RetCode::PARAM_ERROR,Base_RetCode::getMsg(Base_RetCode::PARAM_ERROR));
        }
        $logic     = new Advise_Logic_Advise();
        $ret       = $logic->listAdvise($deviceId,$page,$pageSize);
        $this->ajax($ret);
    }
    
}
