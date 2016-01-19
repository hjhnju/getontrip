<?php
/**
 * 美食接口
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
     * 接口1:/api/1.0/food
     * 美食详情接口
     * @param integer id,美食ID
     * @return json
     */
    public function indexAction(){
        $id = isset($_REQUEST['id'])? intval($_REQUEST['id']) : 0;
        if(empty($id)){
            return $this->ajaxError(Base_RetCode::PARAM_ERROR,Base_RetCode::getMsg(Base_RetCode::PARAM_ERROR));
        }
        $logicFood    = new Food_Logic_Food();
        $ret          = $logicFood->getFoodInfo($id, 1, self::FIRST_PAGE_SIZE);
        $this->ajax($ret);
    }
    
    /**
    * 接口2:/api/1.0/food/topic
    * 美食相关话题接口
    * @param integer id,美食ID
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
        $logicFood    = new Food_Logic_Food();
        $ret          = $logicFood->getFoodTopics($id, $page, $pageSize);
        $this->ajax($ret);
    }
    
    /**
     * 接口3:/api/1.0/food/shop
     * 美食店铺接口
     * @param integer id,美食ID
     * @param integer page
     * @param integer pageSize
     * @return json
     */
    public function shopAction(){
        $id       = isset($_REQUEST['id'])?intval($_REQUEST['id']) : '';
        $page     = isset($_REQUEST['page'])?intval($_REQUEST['page']):1;
        $pageSize = isset($_REQUEST['pageSize'])?intval($_REQUEST['pageSize']):self::DEFAULT_PAGE_SIZE;
        if(empty($id)){
            return $this->ajaxError(Base_RetCode::PARAM_ERROR,Base_RetCode::getMsg(Base_RetCode::PARAM_ERROR));
        }
        $logicFood    = new Food_Logic_Food();
        $ret          = $logicFood->getFoodShops($id, $page, $pageSize);
        $this->ajax($ret);
    }
}
