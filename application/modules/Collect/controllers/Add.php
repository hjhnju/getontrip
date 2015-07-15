<?php
/**
 */
class AddController extends Base_Controller_Page {
    
    protected $logic;
    
    public function init() {
        $this->setNeedLogin(false);
        parent::init();
        $this->logic = new Collect_Logic_Collect();        
    }
    
    /**
     * 添加收藏接口
     */
    public function indexAction() {
        $type      = isset($_POST['type'])?intval($_POST['type']):0;
        $device_id = isset($_POST['device'])?trim($_POST['device']):'';
        $obj_id  = isset($_POST['objid'])?trim($_POST['objid']):'';
        if(empty($type) || empty($device_id) || empty($sight_id)){
            return $this->ajaxError(Collect_RetCode::PARAM_ERROR,
                Collect_RetCode::getMsg(Collect_RetCode::PARAM_ERROR));
        }
        $ret = $this->logic->addCollect($type, $device_id, $obj_id);
        if($ret){
           return $this->ajax(); 
        }
        return $this->ajaxError();
    }  
}
