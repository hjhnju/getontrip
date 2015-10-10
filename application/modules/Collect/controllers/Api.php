<?php
/**
 * 添加收藏接口
 * @author huwei
 *
 */
class ApiController extends Base_Controller_Page {
    
    const PAGESIZE = 6;
    
    protected $logic;
    
    public function init() {
        $this->setNeedLogin(false);
        parent::init();
        $this->logic = new Collect_Logic_Collect();        
    }
    
    /**
     * 接口1：/api/collect/add
     * 添加收藏接口
     * @param integer type,1：话题;2：景点；3:城市; 4:景点
     * @param string  device，设备ID
     * @param integer objid，收藏对象的ID
     * @return json
     */
    public function addAction() {
        $type      = isset($_REQUEST['type'])?intval($_REQUEST['type']):Collect_Type::SIGHT;
        $device_id = isset($_REQUEST['device'])?trim($_REQUEST['device']):'';
        $obj_id    = isset($_REQUEST['objid'])?intval($_REQUEST['objid']):'';
        if(empty($device_id) || empty($obj_id)){
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
     * 接口2：/api/collect/del
     * 取消收藏接口
     * @param integer type,1：话题;2：景点；3:城市,4:景点
     * @param string  device，设备ID
     * @param integer objid，收藏对象的ID
     * @return json
     */
    public function delAction() {
        $type      = isset($_REQUEST['type'])?intval($_REQUEST['type']):Collect_Type::SIGHT;
        $device_id = isset($_REQUEST['device'])?trim($_REQUEST['device']):'';
        $obj_id  = isset($_REQUEST['objid'])?intval($_REQUEST['objid']):'';
        if(empty($device_id) || empty($obj_id)){
            return $this->ajaxError(Collect_RetCode::PARAM_ERROR,
                Collect_RetCode::getMsg(Collect_RetCode::PARAM_ERROR));
        }
        $ret = $this->logic->delCollect($type, $device_id, $obj_id);
        if($ret){
            return $this->ajax();
        }
        return $this->ajaxError();
    }

    /**
     * 接口3：/api/collect/list
     * 获取收藏列表内容
     * @param integer type,1：话题;2：景点；3：城市
     * @param string  device,设备ID
     * @param integer page，页码
     * @param integer pageSize，页面大小
     * @return json
     */
    public function listAction() {
        $type      = isset($_REQUEST['type'])?intval($_REQUEST['type']):Collect_Type::TOPIC;
        $device_id = isset($_REQUEST['device'])?trim($_REQUEST['device']):'';
        $page      = isset($_REQUEST['page'])?intval($_REQUEST['page']):1;
        $pageSize  = isset($_REQUEST['pageSize'])?intval($_REQUEST['pageSize']):self::PAGESIZE;
        if(empty($type) || empty($device_id) ){
            return $this->ajaxError(Collect_RetCode::PARAM_ERROR,
                Collect_RetCode::getMsg(Collect_RetCode::PARAM_ERROR));
        }
        $ret = $this->logic->getCollect($type, $device_id,$page,$pageSize);
        if($ret){
            return $this->ajax($ret);
        }
        return $this->ajaxError();
    }
}
