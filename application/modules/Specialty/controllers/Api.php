<?php
/**
 * 美食接口
 * @author huwei
 *
 */
class ApiController extends Base_Controller_Api {
    
    public function init() {
        $this->setNeedLogin(false);
        parent::init();        
    }
    
    /**
     * 接口1:/api/1.0/specialty
     * 美食详情接口
     * @param integer id,美食ID
     * @return json
     */
    public function indexAction(){
        $id = isset($_REQUEST['id'])? intval($_REQUEST['id']) : 0;
        if(empty($id)){
            return $this->ajaxError(Base_RetCode::PARAM_ERROR,Base_RetCode::getMsg(Base_RetCode::PARAM_ERROR));
        }
        $objSpecialty    = new Specialty_Object_Specialty();
        $objSpecialty->fetch(array('id' => $id));
        if(empty($objSpecialty->id)){
            return $this->ajaxError();
        }
        $this->ajax();
    }
}
