<?php
/**
 * 特产接口
 * @author huwei
 *
 */
class ApiController extends Base_Controller_Api {
    
    const FIRST_PAGE_SIZE = 4;
    
    const DEFAULT_PAGE_SIZE = 8;
    
    public function init() {
        $this->setNeedLogin(false);
        parent::init();        
    }
    
    /**
     * 接口1:/api/1.0/specialty
     * 特产详情接口
     * @param integer id,特产ID
     * @return json
     */
    public function indexAction(){
        $id = isset($_REQUEST['id'])? intval($_REQUEST['id']) : 0;
        if(empty($id)){
            return $this->ajaxError(Base_RetCode::PARAM_ERROR,Base_RetCode::getMsg(Base_RetCode::PARAM_ERROR));
        }
        $logicSpecialty    = new Specialty_Logic_Specialty();
        $ret          = $logicSpecialty->getSpecialtyInfo($id, 1, self::FIRST_PAGE_SIZE);
        $this->ajax($ret);
    }
    
    /**
    * 接口2:/api/1.0/specialty/topic
    * 特产相关话题接口
    * @param integer id,特产ID
    * @param integer page
    * @param integer pageSize
    * @return json
    */
    public function topicAction(){
        $id       = isset($_REQUEST['id'])?intval($_REQUEST['id']) : '';
        $page     = isset($_REQUEST['page'])?intval($_REQUEST['page']):1;
        $pageSize = isset($_REQUEST['pageSize'])?intval($_REQUEST['pageSize']):self::DEFAULT_PAGE_SIZE;
        if(empty($id)){
            return $this->ajaxError(Base_RetCode::PARAM_ERROR,Base_RetCode::getMsg(Base_RetCode::PARAM_ERROR));
        }
        $logicSpecialty    = new Specialty_Logic_Specialty();
        $ret          = $logicSpecialty->getSpecialtyTopics($id, $page, $pageSize);
        $this->ajax($ret);
    }
    
    /**
     * 接口3:/api/1.0/specialty/product
     * 特产相关产品接口
     * @param integer id,特产ID
     * @param integer page
     * @param integer pageSize
     * @return json
     */
    public function productAction(){
        $id       = isset($_REQUEST['id'])?intval($_REQUEST['id']) : '';
        $page     = isset($_REQUEST['page'])?intval($_REQUEST['page']):1;
        $pageSize = isset($_REQUEST['pageSize'])?intval($_REQUEST['pageSize']):self::DEFAULT_PAGE_SIZE;
        if(empty($id)){
            return $this->ajaxError(Base_RetCode::PARAM_ERROR,Base_RetCode::getMsg(Base_RetCode::PARAM_ERROR));
        }
        $logicSpecialty    = new Specialty_Logic_Specialty();
        $ret          = $logicSpecialty->getSpecialtyProducts($id, $page, $pageSize);
        $this->ajax($ret);
    }
}
