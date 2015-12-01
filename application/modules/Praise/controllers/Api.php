<?php
/**
 * 点赞接口
 * @author huwei
 *
 */
class ApiController extends Base_Controller_Api {
    
    const PAGESIZE = 6;
    
    protected $logic;
    
    public function init() {
        $this->setNeedLogin(true);
        parent::init();
        $this->logic     = new Praise_Logic_Praise();     
    }
    
    /**
     * 接口1：/api/1.0/praise/add
     * 点赞接口
     * @param integer type,1:话题
     * @param integer objid，收藏对象的ID
     * @return json
     */
    public function addAction() {
        $type      = isset($_REQUEST['type'])?intval($_REQUEST['type']):Praise_Type_Type::TOPIC;
        $obj_id    = isset($_REQUEST['objid'])?intval($_REQUEST['objid']):'';
        if(empty($obj_id)){
            return $this->ajaxError(Base_RetCode::PARAM_ERROR,
                Base_RetCode::getMsg(Base_RetCode::PARAM_ERROR));
        }
        $ret = $this->logic->addPraise($type, $this->userid, $obj_id);
        if($ret){
            return $this->ajax();
        }
        return $this->ajaxError();
    } 
    
    /**
     * 接口2：/api/1.0/praise/del
     * 取消点赞接口
     * @param integer type,2：景点；3:城市,4:话题,5:书籍
     * @param integer objid，收藏对象的ID
     * @return json
     */
    public function delAction() {
        $type      = isset($_REQUEST['type'])?intval($_REQUEST['type']):Praise_Type_Type::TOPIC;
        $obj_id    = isset($_REQUEST['objid'])?intval($_REQUEST['objid']):'';
        if(empty($obj_id)){
            return $this->ajaxError(Base_RetCode::PARAM_ERROR,
                Base_RetCode::getMsg(Base_RetCode::PARAM_ERROR));
        }
        $ret = $this->logic->delPraise($type, $this->userid, $obj_id);
        if($ret){
            return $this->ajax();
        }
        return $this->ajaxError();
    }
}
