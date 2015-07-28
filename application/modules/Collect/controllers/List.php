<?php
/**
 * 收藏列表
 * @author huwei
 *
 */
class ListController extends Base_Controller_Page {
    
    protected $logic;
    
    public function init() {
        $this->setNeedLogin(false);
        parent::init();     
        $this->logic = new Collect_Logic_Collect();   
    }
    
    /**
     * 接口1：/collect/list
     * 获取收藏列表内容
     * @param integer type,1：话题;2：景点；3：主题
     * @param integer device,设备ID
     * @return json
     */
    public function indexAction() {
        $type      = isset($_POST['type'])?intval($_POST['type']):Collect_Type::TOPIC;
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
