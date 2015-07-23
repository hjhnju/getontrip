<?php
/**
 * 评论页接口
 * @author huwei
 *
 */
class AddController extends Base_Controller_Api {
    
    const PAGESIZE = 6;
    
    public function init() {
        $this->setNeedLogin(false);
        parent::init();        
    }
    
    /**
     * 接口1：/comment/add
     * 评论列表页
     * @param integer page
     * @param integer pageSize
     * @return json
     */
    public function indexAction() {
        $topicId    = isset($_POST['topicId'])?intval($_POST['topicId']):'';
        $deviceId   = isset($_POST['deviceId'])?intval($_POST['deviceId']):'';
        $toUserId   = isset($_POST['toUserId'])?intval($_POST['toUserId']):'';
        $content    = isset($_POST['content'])?intval($_POST['content']):'';
               
        if(empty($topicId) || empty($deviceId)|| empty($toUserId) ||empty($content)){
            return $this->ajaxError(Base_RetCode::PARAM_ERROR,Base_RetCode::getMsg(Base_RetCode::PARAM_ERROR));
        }
        $logic      = new Comment_Logic_Comment();
        $ret        = $logic->addComment($topicId,$deviceId,$toUserId,$content);
        $this->ajax($ret);
    }    
}
