<?php
/**
 * 消息页数据接口
 * @author huwei
 *
 */
class ApiController extends Base_Controller_Api {
    
    const PAGESIZE = 2;
    
    public function init() {
        $this->setNeedLogin(false);
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
        if(empty($deviceId)){
            return $this->ajaxError(Base_RetCode::PARAM_ERROR,Base_RetCode::getMsg(Base_RetCode::PARAM_ERROR));
        }                       
        $logic = new Msg_Logic_Msg();
        $ret = $logic->getList( $page, $pageSize);
        return $this->ajax($ret);
    }  
    
    /**
     * 接口2：/api/msg/read
     * 阅读消息
     * @param integer mid，消息ID
     * @return json
     */
    public function readAction() {
        $mid  = isset($_REQUEST['mid'])?trim($_REQUEST['mid']):'';
        if(empty($mid)){
            return $this->ajaxError(Base_RetCode::PARAM_ERROR,Base_RetCode::getMsg(Base_RetCode::PARAM_ERROR));
        }
        $logic = new Msg_Logic_Msg();
        $ret = $logic->setRead($mid);
        return $this->ajax(strval($ret));
    }
}