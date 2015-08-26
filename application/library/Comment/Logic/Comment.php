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
     * 添加评论信息,同时更新下redis
     * @param integer $id
     * @param array $info
     */
    public function  addComment($topicId,$upId,$deviceId,$toUserId,$content){
        $objComment = new Comment_Object_Comment();    
        $logicUser  = new User_Logic_User();
        $objComment->fromUserId = $logicUser->getUserId($deviceId);
        $objComment->upId       = $upId;
        $objComment->toUserId   = $toUserId;
        $objComment->topicId    = $topicId;
        $objComment->content    = $content;
        $objComment->status     = Comment_Type_Status::PUBLISHED;
        $ret = $objComment->save();
        
        $redis = Base_Redis::getInstance();
        $redis->hDel(Comment_Keys::getCommentKey(),Comment_Keys::getLateKey($topicId, '*'));
        $redis->hDel(Comment_Keys::getCommentKey(),Comment_Keys::getTotalKey($topicId));
        return $ret;
    }
    
    /**
     * 改变评论的状态
     * @param integer $id
     * @param integer $status
     * @return boolean
     */
    public function changeCommentStatus($id,$status){
        $objComment = new Comment_Object_Comment();
        $objComment->fetch(array('id' => $id));
        $objComment->status = $status;
        $ret = $objComment->save();
        
        $redis = Base_Redis::getInstance();
        $redis->hDel(Comment_Keys::getCommentKey(),Comment_Keys::getLateKey($objComment->topicId, '*'));
        $redis->hDel(Comment_Keys::getCommentKey(),Comment_Keys::getTotalKey($objComment->topicId));
        return $ret;
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
        $listComment->setFilter(array('topic_id' => $topicId,'up_id' => 0,'status' => Comment_Type_Status::PUBLISHED));
        $listComment->setFields(array('id','from_user_id','to_user_id','content','create_time','up_id'));
        $listComment->setPage($page);
        $listComment->setPagesize($pageSize);
        $listComment->setOrder("create_time asc");
        $ret = $listComment->toArray();
        foreach ($ret['list'] as $key => $val){
            $ret['list'][$key]['from_name'] = $logicUser->getUserName($val['from_user_id']);
            $ret['list'][$key]['to_name']   = $logicUser->getUserName($val['to_user_id']);
            unset($ret['list'][$key]['from_user_id']);
            unset($ret['list'][$key]['to_user_id']);
            $listSubComment = new Comment_List_Comment();
            $listSubComment->setFilter(array('up_id' => $val['id']));
            $listSubComment->setPagesize(PHP_INT_MAX);
            $listSubComment->setOrder("create_time asc");
            $arrSubComment = $listSubComment->toArray();
            foreach ($arrSubComment as $index => $data){
                $arrSubComment['list'][$index]['from_name'] = $logicUser->getUserName($data['from_user_id']);
                $arrSubComment['list'][$index]['to_name']   = $logicUser->getUserName($data['to_user_id']);
                unset($arrSubComment['list'][$index]['from_user_id']);
                unset($arrSubComment['list'][$index]['to_user_id']);
            }
            $ret['list'][$key]['subComment'] = $arrSubComment['list'];
        }
        return $ret['list'];
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
    public function getLateCommentNum($topicId,$during=''){
        if(empty($during)){
            $from = 0;
        }else{
            $from = strtotime($during.' days ago');
        }
        $redis = Base_Redis::getInstance();
        $ret = $redis->hGet(Comment_Keys::getCommentKey(),Comment_Keys::getLateKey($topicId,$during));
        if(!empty($ret)){
            return $ret;
        }
        $list   = new Comment_List_Comment();
        $filter = "`topic_id` = $topicId and `create_time` >= $during and `up_id` = 0";
        $list->setPagesize(PHP_INT_MAX);
        $list->setFilterString($filter);
        $arrRet = $list->toArray();
        $redis->hSet(Comment_Keys::getCommentKey(),Comment_Keys::getLateKey($topicId,$during),$arrRet['total']);
        return $arrRet['total'];
    }
    
    /**
     * 获取话题总的评论数
     * @param integer $topicId
     * @param string  $during
     * @return integer
     */
    public function getTotalCommentNum($topicId){        
        $redis = Base_Redis::getInstance();
        $ret = $redis->hGet(Comment_Keys::getCommentKey(),Comment_Keys::getTotalKey($topicId));
        if(!empty($ret)){
            return $ret;
        }
        $list   = new Comment_List_Comment();
        $list->setPagesize(PHP_INT_MAX);
        $list->setFilter(array('topic_id' => $topicId,'up_id' => 0));
        $arrRet = $list->toArray();
        $redis->hSet(Comment_Keys::getCommentKey(),Comment_Keys::getTotalKey($topicId),$arrRet['total']);
        return $arrRet['total'];
    }
    
    /**
     * 删除评论
     * @param integer $id
     * @return boolean
     */
    public function  delComment($id){
        $objComment = new Comment_Object_Comment();
        $objComment->fetch(array('id' => $id));
        $redis = Base_Redis::getInstance();
        $redis->hDel(Comment_Keys::getCommentKey(),Comment_Keys::getLateKey($objComment->topicId, '*'));
        $redis->hDel(Comment_Keys::getCommentKey(),Comment_Keys::getTotalKey($objComment->topicId));
        return $objComment->remove();
    }
}