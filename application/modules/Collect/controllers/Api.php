<?php
/**
 * 添加收藏接口
 * @author huwei
 *
 */
class ApiController extends Base_Controller_Page {
    
    protected $logic;
    
    public function init() {
        $this->setNeedLogin(false);
        parent::init();
        $this->logic = new Collect_Logic_Collect();        
    }
    
    /**
     * 接口1：/api/collect/add
     * 添加收藏接口
     * @param integer type,1：话题;2：景点；3:主题
     * @param string  device，设备ID
     * @param integer objid，收藏对象的ID
     * @return json
     */
    public function addAction() {
        $type      = isset($_POST['type'])?intval($_POST['type']):Collect_Type::SIGHT;
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

    /**
     * 接口2：/api/collect/list
     * 获取收藏列表内容
     * @param integer type,1：话题;2：景点；3：主题
     * @param string  device,设备ID
     * @return json
     */
    public function listAction() {
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
