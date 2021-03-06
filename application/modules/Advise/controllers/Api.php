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
     * @param string advise，意见信息
     * @return json
     */
    public function addAction() {
        $strAdvise  = isset($_REQUEST['advise'])?trim($_REQUEST['advise']):'';
        if(empty($strAdvise)){
            return $this->ajaxError(Base_RetCode::PARAM_ERROR,Base_RetCode::getMsg(Base_RetCode::PARAM_ERROR));
        }
        $logic     = new Advise_Logic_Advise();
        $ret       = $logic->addAdvise($strAdvise);
        $this->ajax(strval($ret));
    }
    
    /**
     * 接口2：/api/advise/list
     * @param integer page
     * @param integer pageSize
     * 查询反馈意见接口
     * @return json
     */
    public function listAction() {
        $page      = isset($_REQUEST['page'])?intval($_REQUEST['page']):1;
        $pageSize  = isset($_REQUEST['pageSize'])?intval($_REQUEST['pageSize']):self::PAGESIZE;
        $logic     = new Advise_Logic_Advise();
        $ret       = $logic->listAdvise($page,$pageSize);
        $this->ajax($ret);
    }
    
   /**
    * 接口3：/api/1.0/advise/report
    * 举报
    * @param integer commentid，评论ID
    * @return json
    */
    public function reportAction(){
        $commentId = isset($_REQUEST['commentid'])?intval($_REQUEST['commentid']):'';
        $type      = isset($_REQUEST['type'])?intval($_REQUEST['type']):Report_Type_Type::COMMENT;
        if(empty($commentId)){
            return $this->ajaxError(Base_RetCode::PARAM_ERROR,Base_RetCode::getMsg(Base_RetCode::PARAM_ERROR));
        }
        $userid = User_Api::getCurrentUser();
        if(empty($userid)){
            return $this->ajaxError(Base_RetCode::SESSION_NOT_LOGIN,Base_RetCode::getMsg(Base_RetCode::SESSION_NOT_LOGIN));
        }
        $logic = new Report_Logic_Report();
        $ret   = $logic->add($commentId, $userid, $type);
        if($ret){
            return $this->ajaxError($ret,Advise_RetCode::getMsg($ret));
        }
        return $this->ajax();
    }    
}
