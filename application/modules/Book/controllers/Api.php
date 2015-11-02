<?php
/**
 * 书籍接口
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
     * 接口1:/api/book
     * 书籍详情接口
     * @param integer book,书籍ID
     * @return json
     */
    public function indexAction(){
        $bookId     = isset($_REQUEST['book'])?intval($_REQUEST['book']):'';
        if(empty($bookId)){
            return $this->ajaxError(Base_RetCode::PARAM_ERROR,Base_RetCode::getMsg(Base_RetCode::PARAM_ERROR));
        }
        
        //增加访问统计
        $logicVisit = new Tongji_Logic_Visit();
        $logicVisit->addVisit(Tongji_Type_Visit::BOOK,$bookId);
        
        $logic    = new Book_Logic_Book();
        $ret      = $logic->getBookById($bookId);  
        $this->ajaxDecode($ret);
    }
}
