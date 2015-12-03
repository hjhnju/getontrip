<?php
/**
 * 视频接口
 * @author huwei
 *
 */
class ApiController extends Base_Controller_Api {
    
    public function init() {
        $this->setNeedLogin(false);
        parent::init();        
    }
    
    /**
     * 接口1:/api/1.0/video
     * 书籍详情接口
     * @param integer book,书籍ID
     * @return json
     */
    public function indexAction(){
        $id = isset($_REQUEST['id'])? intval($_REQUEST['id']) : 0;
        if(empty($id)){
            return $this->ajaxError(Base_RetCode::PARAM_ERROR,Base_RetCode::getMsg(Base_RetCode::PARAM_ERROR));
        }
        $objVideo    = new Video_Object_Video();
        $objVideo->fetch(array('id' => $id));
        if(empty($objVideo->id)){
            return $this->ajaxError();
        }
        $logicCollect = new Collect_Logic_Collect();
        $ret = $logicCollect->checkCollect(Collect_Type::VIDEO, $id);
        $arrVideo['id']          = strval($objVideo->id);
        $arrVideo['collected']    = strval($ret);
        $this->ajax($arrVideo);
    }
}
