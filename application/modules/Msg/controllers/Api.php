<?php
/**
 * 消息页数据接口
 * @author huwei
 *
 */
class ApiController extends Base_Controller_Api {
    
    const PAGESIZE = 6;
    
    public function init() {
        $this->setNeedLogin(true);
        parent::init();        
    }
    
    /**
     * 接口1：/api/msg/list
     * 查询消息
     * @param integer page，页码
     * @param integer pageSize，页面大小
     * @return json
     */
    public function listAction() {
        $page      = isset($_REQUEST['page'])?intval($_REQUEST['page']):1;
        $pageSize  = isset($_REQUEST['pageSize'])?intval($_REQUEST['pageSize']):self::PAGESIZE;                      
        $logic     = new Msg_Logic_Msg();
        $ret       = $logic->getList($this->userid,$page, $pageSize);
        return $this->ajax($ret);
    }  
    
    /**
     * 接口2：/api/1.0/msg/read
     * 阅读消息
     * @param integer mid，消息ID
     * @return json
     */
    public function readAction() {
        $mid   = isset($_REQUEST['mid'])?trim($_REQUEST['mid']):'';
        if(empty($mid)){
            return $this->ajaxError(Base_RetCode::PARAM_ERROR,Base_RetCode::getMsg(Base_RetCode::PARAM_ERROR));
        }
        $logic = new Msg_Logic_Msg();
        $ret   = $logic->setRead($mid);
        return $this->ajax(strval($ret));
    }
    
    /**
     * 接口3:/api/1.0/msg/del
     * 消息删除接口
     * @param integer mid,消息ID
     * @return json
     */
    public function delAction(){
        $mid   = isset($_REQUEST['mid'])?trim($_REQUEST['mid']):'';
        if(empty($mid)){
            return $this->ajaxError(Base_RetCode::PARAM_ERROR,Base_RetCode::getMsg(Base_RetCode::PARAM_ERROR));
        }
        $logic = new Msg_Logic_Msg();
        $ret   = $logic->del($mid);
        if($ret){
            return $this->ajax();
        }
        return $this->ajaxError();
    } 
}