<?php
/**
 * 添加反馈意见接口
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
        $ret       = $logic->addAdvise($this->userid,$strAdvise);
        $this->ajax($ret);
    }
    
    /**
     * 接口2：/api/advise/list
     * 查询反馈意见接口
     * @return json
     */
    public function listAction() {
        $page      = isset($_REQUEST['page'])?intval($_REQUEST['page']):1;
        $pageSize  = isset($_REQUEST['pageSize'])?intval($_REQUEST['pageSize']):self::PAGESIZE;
        $logic     = new Advise_Logic_Advise();
        $ret       = $logic->listAdvise($this->userid,$page,$pageSize);
        $this->ajax($ret);
    }
    
}
