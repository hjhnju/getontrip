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
     * 接口1：/Api/book
     * 书籍列表接口
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
        $logic      = new Book_Logic_Book();
        $ret        = $logic->getBooks($sightId,$page,$pageSize,array('status' => Book_Type_Status::PUBLISHED));
        $this->ajax($ret);
    } 
    
    /**
     * 接口2:/Api/book/detail
     * 书籍详情接口
     * @param integer book,书籍ID
     * @param string deviceId，用户的设备ID（因为要统计UV）
     * @return json
     */
    public function detailAction(){
        $bookId     = isset($_REQUEST['book'])?intval($_REQUEST['book']):'';
        if(empty($bookId)){
            return $this->ajaxError(Base_RetCode::PARAM_ERROR,Base_RetCode::getMsg(Base_RetCode::PARAM_ERROR));
        }
        
        //增加访问统计
        $logicVisit = new Tongji_Logic_Visit();
        $logicVisit->addVisit(Tongji_Type_Visit::BOOK,$bookId);
        
        $logic    = new Book_Logic_Book();
        $ret      = $logic->getBookById($bookId);  
        $this->ajax($ret);
    }
}
