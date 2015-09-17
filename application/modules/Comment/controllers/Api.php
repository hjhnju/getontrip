<?php
/**
 * 评论页接口
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
     * 接口1：/api/comment/add
     * 添加评论页
     * @param integer topicId,话题ID
     * @param integer upId,上层评论ID，如果是顶级评论，则可以不传
     * @param string  deviceId,设备ID
     * @param integer toUserId,回复给的人，如果是评论，则可以不传
     * @param string content,回复的内容
     * @param integer type,评论类型,1:话题,2:书籍,3:视频,4:百科
     * @return json
     */
    public function addAction() {
        $topicId    = isset($_REQUEST['topicId'])?intval($_REQUEST['topicId']):'';
        $upId       = isset($_REQUEST['upId'])?intval($_REQUEST['upId']):0;
        $deviceId   = isset($_REQUEST['deviceId'])?trim($_REQUEST['deviceId']):'';
        $toUserId   = isset($_REQUEST['toUserId'])?intval($_REQUEST['toUserId']):'';
        $content    = isset($_REQUEST['content'])?trim($_REQUEST['content']):'';    
        $type       = isset($_REQUEST['type'])?intval($_REQUEST['type']):Comment_Type_Type::TOPIC;           
        if(empty($topicId) || empty($deviceId)|| empty($toUserId) ||empty($content)){
            return $this->ajaxError(Base_RetCode::PARAM_ERROR,Base_RetCode::getMsg(Base_RetCode::PARAM_ERROR));
        }
        $logic      = new Comment_Logic_Comment();
        $ret        = $logic->addComment($topicId,$upId,$deviceId,$toUserId,$content,$type);
        $this->ajax($ret);
    }   

    /**
     * 接口2：/api/comment/list
     * 评论列表页
     * @param integer topicId，话题ID
     * @param integer page
     * @param integer pageSize
     * @param integer type,评论类型,1:话题,2:书籍,3:视频,4:百科
     * @return json
     */
    public function listAction() {
        $topicId    = isset($_REQUEST['topicId'])?intval($_REQUEST['topicId']):'';
        $page       = isset($_REQUEST['page'])?intval($_REQUEST['page']):1;
        $pageSize   = isset($_REQUEST['pageSize'])?intval($_REQUEST['pageSize']):self::PAGESIZE;
        $type       = isset($_REQUEST['type'])?intval($_REQUEST['type']):Comment_Type_Type::TOPIC;
        if(empty($topicId)){
            return $this->ajaxError(Base_RetCode::PARAM_ERROR,Base_RetCode::getMsg(Base_RetCode::PARAM_ERROR));
        }
        $logic      = new Comment_Logic_Comment();
        $ret        = $logic->getCommentList($topicId, $page, $pageSize, $type);
        $this->ajax($ret['list']);
    }
}
