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
     * 接口1:/api/video
     * 书籍详情接口
     * @param integer book,书籍ID
     * @return json
     */
    public function indexAction(){
    }
}
