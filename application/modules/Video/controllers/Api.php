<?php
/**
 * 视频接口
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
     * 接口1：/api/video
     * 视频详情接口
     * @param integer page
     * @param integer pageSize
     * @param integer sightId,景点ID
     * @return json
     */
    public function indexAction() {
        $page       = isset($_REQUEST['page'])?intval($_REQUEST['page']):1;
        $pageSize   = isset($_REQUEST['pageSize'])?intval($_REQUEST['pageSize']):self::PAGESIZE;
        $sightId    = isset($_REQUEST['sightId'])?intval($_REQUEST['sightId']):'';
        if(empty($sightId)){
            return $this->ajaxError(Base_RetCode::PARAM_ERROR,Base_RetCode::getMsg(Base_RetCode::PARAM_ERROR));
        }
        $logic      = new Video_Logic_Video();
        $ret        = $logic->getVideoList($sightId,$page,$pageSize,array('status' => Book_Type_Status::PUBLISHED));
        $this->ajax($ret);
    }   
}
