<?php
/**
 */
class ListController extends Base_Controller_Page {
    
    protected $logic;
    
    public function init() {
        $this->setNeedLogin(false);
        parent::init();     
        $this->logic = new Collect_Logic_Collect();   
    }
    
    /**
     */
    public function indexAction() {
        $type      = isset($_POST['type'])?intval($_POST['type']):0;
        $device_id = isset($_POST['device'])?trim($_POST['device']):'';
        if(empty($type) || empty($device_id) ){
            return $this->ajaxError(Collect_RetCode::PARAM_ERROR,
                Collect_RetCode::getMsg(Collect_RetCode::PARAM_ERROR));
        }
        $ret = $this->logic->getCollect($type, $device_id);
        if($ret){
            return $this->ajax();
        }
        return $this->ajaxError();
    }  
}
