<?php
/**
 * 视频详情页面
 * 2015-11-4
 * @author fyy
 */
class DetailController extends Base_Controller_Page {
     
    public function init() {
        $this->setNeedLogin(false);
        parent::init();
    }
    
    /**
     *  详情
     */
    public function indexAction() {             
       $id = isset($_REQUEST['id'])? intval($_REQUEST['id']) : 0; 
       if(empty($id)){
            return $this->ajaxError(Base_RetCode::PARAM_ERROR,Base_RetCode::getMsg(Base_RetCode::PARAM_ERROR));
       }
       
       $objVideo    = new Video_Object_Video();
       $objVideo->fetch(array('id' => $id));
           
       //增加访问统计
       $logicVisit = new Tongji_Logic_Visit();
       $logicVisit->addVisit(Tongji_Type_Visit::VIDEO,$id);  
       $this->redirect($objVideo->url);
    }
}
