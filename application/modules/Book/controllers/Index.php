<?php
/**
 * 书籍接口
 * @author huwei
 *
 */
class IndexController extends Base_Controller_Api {
    
    const PAGESIZE = 6;
    
    public function init() {
        $this->setNeedLogin(false);
        parent::init();        
    }
    
    /**
     * 书籍详情接口
     */
    public function indexAction() {
        $page       = isset($_POST['page'])?intval($_POST['page']):1;
        $pageSize   = isset($_POST['pageSize'])?intval($_POST['page']):self::PAGESIZE;
        $sightId    = isset($_POST['sightId'])?intval($_POST['sightId']):'';
         
        if(empty($sightId)){
            return $this->ajaxError(Base_RetCode::PARAM_ERROR,Base_RetCode::getMsg(Base_RetCode::PARAM_ERROR));
        }
        $logic      = new Book_Logic_Book();
        $ret        = $logic->getBooks($sightId,$page,$pageSize);
        $this->ajax($ret);
    }   
}