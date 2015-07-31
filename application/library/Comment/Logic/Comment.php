<?php
/**
 * 话题评论逻辑层
 * @author huwei
 *
 */
class Comment_Logic_Comment  extends Base_Logic{
    
    const ANONYMOUS = "匿名用户";
    
    public function __construct(){
    
    }
    
    /**
     * 获取我的评论数据
     * @param integer $deviceId
     * @param integer $page
     * @param integer $pageSize
     * @return array
     */
    public function getUserComment($deviceId,$page,$pageSize){
        $logicUser   = new User_Logic_User();
        $userId      = $logicUser->getUserId($deviceId);
        $listComment = new Comment_List_Comment();
        $listComment->setFilter(array('from_user_id' => $userId));
        $listComment->setPage($page);
        $listComment->setPagesize($pageSize);
        return $listComment->toArray();
    }
    
    /**
     * 添加评论信息
     * @param integer $id
     * @param array $info
     */
    public function  addComment($topicId,$deviceId,$toUserId,$content){
        $objComment = new Comment_Object_Comment();    
        $logicUser  = new User_Logic_User();
        $objComment->fromUserId = $logicUser->getUserId($deviceId);
        $objComment->toUserId   = $toUserId;
        $objComment->topicId    = $topicId;
        $objComment->content    = $content;
        return $objComment->save();
    }
    
    /**
     * 获取话题的评论列表
     * @param integer $topicId
     * @param integer $page
     * @param integer $pageSize
     * @return array
     */
    public function getCommentList($topicId,$page,$pageSize){
        $logicUser    = new User_Logic_User();
        $listComment  = new Comment_List_Comment();
        $listComment->setFilter(array('topic_id' => $topicId));
        $listComment->setPage($page);
        $listComment->setPagesize($pageSize);
        $listComment->setOrder("create_time desc");
        $ret = $listComment->toArray();
        foreach ($ret['list'] as $key => $val){
            $ret['list'][$key]['from_name'] = $logicUser->getUserName($val['from_user_id']);
            $ret['list'][$key]['to_name'] = $logicUser->getUserName($val['to_user_id']);
        }
        return $listComment->toArray();
    }
    
    /**
     * 根据ID获取评论详情
     * @param integer $id
     * @return array
     */
    public function getCommentById($id){
        $objComment = new Comment_Object_Comment();
        $objComment->fetch(array('id' => $id));
        $ret =  $objComment->toArray();
        if(!empty($ret)){
            $logicUser    = new User_Logic_User();
            $ret['from_name'] = $logicUser->getUserName($ret['from_user_id']);
            $ret['to_name']   = $logicUser->getUserName($ret['to_user_id']);
        }
    }
    
    /**
     * 获取话题最近的评论数
     * @param integer $topicId
     * @param string  $during
     * @return integer
     */
    public function getCommentNum($topicId,$during=''){
        if(empty($during)){
            $from = 0;
        }else{
            $from = strtotime($during);
        }
        $redis = Base_Redis::getInstance();
        $ret = $redis->zRangeByScore(Topic_Keys::getTopicCommentKey($topicId),$from,time());
        return count($ret);
    }
    
    /**
     * 删除评论
     * @param integer $id
     * @return boolean
     */
    public function  delComment($id){
        $objComment = new Comment_Object_Comment();
        $objComment->fetch(array('id' => $id));
        return $objComment->remove();
    }
}