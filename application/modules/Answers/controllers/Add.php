<?php
/**
 * 添加答案接口
 * @author huwei
 *
 */
class AddController extends Base_Controller_Api {
    
    public function init() {
        $this->setNeedLogin(true);
        parent::init();        
    }
    
    /**
     * 接口1：/answers/add
     * 添加答案接口
     * @param integer deviceId，设备ID
     * @param integer showname，是否匿名,0:不匿名，1:匿名
     * @param string info，答案信息
     * @return json
     */
    public function indexAction() {
        $deviceId  = isset($_POST['deviceId'])?intval($_POST['deviceId']):'';
        $showname  = isset($_POST['showname'])?intval($_POST['showname']):1;
        $info      = isset($_POST['info'])?trim($_POST['info']):''; 
        if(empty($deviceId) || empty(info)){
            return $this->ajaxError(Base_RetCode::PARAM_ERROR,Base_RetCode::getMsg(Base_RetCode::PARAM_ERROR));
        }
        $logic     = new Answers_Logic_Answers();
        $ret       = $logic->addAnswer($deviceId,$info,$showname);
        $this->ajax($ret);
    }
    
}
