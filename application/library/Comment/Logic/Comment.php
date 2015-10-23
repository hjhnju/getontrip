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
    public function  addComment($objId,$upId,$userId,$toUserId,$content,$type){
        $objComment = new Comment_Object_Comment();    
        $logicUser  = new User_Logic_User();
        $objComment->fromUserId = $userId;
        $objComment->upId       = $upId;
        $objComment->toUserId   = $toUserId;
        $objComment->objId      = $objId;
        $objComment->content    = $content;
        $objComment->status     = Comment_Type_Status::PUBLISHED;
        $ret = $objComment->save();
        
        //发送回复消息
        if(!empty($upId)){
            $logicTopic = new Topic_Logic_Topic();
            $topic      = $logicTopic->getTopicById($objId);
            $arrInfo    = array(
                'user_id'   => $userId,
                'topicId'   => $topic['id'],
                'commentId' => $upId,               
            );
            Msg_Api::sendmsg(Msg_Type_Type::REPLY,$topic['image'],$toUserId,$arrInfo,$userId);
        }
        
        $redis = Base_Redis::getInstance();
        $redis->hDel(Comment_Keys::getCommentKey(),Comment_Keys::getLateKey($objId, '*'));
        $redis->hDel(Comment_Keys::getCommentKey(),Comment_Keys::getTotalKey($objId));
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
        $redis->hDel(Comment_Keys::getCommentKey(),Comment_Keys::getLateKey($objComment->objId, '*'));
        $redis->hDel(Comment_Keys::getCommentKey(),Comment_Keys::getTotalKey($objComment->objId));
        return $ret;
    }
    
    /**
     * 评论列表，前端使用
     * @param integer $objId
     * @param integer $page
     * @param integer $pageSize
     * @return array
     */
    public function getCommentList($objId,$page,$pageSize,$type = Comment_Type_Type::TOPIC){
        $logicUser    = new User_Logic_User();
        $listComment  = new Comment_List_Comment();
        $listComment->setFilter(array('obj_id' => $objId,'up_id' => 0,'type' => $type,'status' => Comment_Type_Status::PUBLISHED));
        $listComment->setFields(array('id','from_user_id','to_user_id','content','create_time'));
        $listComment->setPage($page);
        $listComment->setPagesize($pageSize);
        $listComment->setOrder("create_time desc");
        $ret = $listComment->toArray();
        foreach ($ret['list'] as $key => $val){
            $ret['list'][$key]['id']          = strval($val['id']);
            $ret['list'][$key]['from_user_id']          = strval($val['from_user_id']);
            $ret['list'][$key]['from_name']   = $logicUser->getUserName($val['from_user_id']);
            $ret['list'][$key]['to_name']     = empty($val['to_user_id'])?'':$logicUser->getUserName($val['to_user_id']);
            $ret['list'][$key]['avatar']      = $logicUser->getUserAvatar($val['from_user_id']);
            $ret['list'][$key]['create_time'] = Base_Util_String::getTimeAgoString($val['create_time']);
            unset($ret['list'][$key]['to_user_id']);
            $listSubComment = new Comment_List_Comment();
            $listSubComment->setFilter(array('up_id' => $val['id'],'status' => Comment_Type_Status::PUBLISHED));
            $listSubComment->setFields(array('id','from_user_id','to_user_id','content'));
            $listSubComment->setPagesize(PHP_INT_MAX);
            $listSubComment->setOrder("create_time asc");
            $arrSubComment = $listSubComment->toArray();
            foreach ($arrSubComment['list'] as $index => $data){
                $arrSubComment['list'][$index]['id']           = strval($data['id']);
                $arrSubComment['list'][$index]['from_user_id'] = strval($data['from_user_id']);
                $arrSubComment['list'][$index]['from_name']    = $logicUser->getUserName($data['from_user_id']);
                $arrSubComment['list'][$index]['to_name']      = $logicUser->getUserName($data['to_user_id']);
                unset($arrSubComment['list'][$index]['to_user_id']);
            }
            $ret['list'][$key]['subComment'] = $arrSubComment['list'];
        }
        return $ret;
    }
    
    /**
     * 获取评论列表，后端使用
     * @param integer $objId
     * @param integer $page
     * @param integer $pageSize
     * @return array
     */
    public function getComments($page,$pageSize,$arrParam = array(),$type = '',$objId = ''){
        $logicUser    = new User_Logic_User();
        $listComment  = new Comment_List_Comment();
        $arrFilter    = array_merge($arrParam,array('up_id' => 0));
        if(!empty($type)){
            $arrFilter = array_merge($arrFilter,array('type' => $type));
        }
        if(!empty($objId)){
            $arrFilter = array_merge($arrFilter,array('obj_id' => $objId));
        }
        $listComment->setFilter($arrFilter);
        $listComment->setPage($page);
        $listComment->setPagesize($pageSize);
        $listComment->setOrder("create_time asc");
        $ret = $listComment->toArray();
        foreach ($ret['list'] as $key => $val){
            $ret['list'][$key]['from_name']   = $logicUser->getUserName($val['from_user_id']);
            $ret['list'][$key]['to_name']     = $logicUser->getUserName($val['to_user_id']);
            $listSubComment = new Comment_List_Comment();
            $arrParams = array_merge($arrParam,array('up_id' => $val['id']));
            $listSubComment->setFilter($arrParams);
            $listSubComment->setPagesize(PHP_INT_MAX);
            $listSubComment->setOrder("create_time asc");
            $arrSubComment = $listSubComment->toArray();
            foreach ($arrSubComment['list'] as $index => $data){
                $arrSubComment['list'][$index]['from_name'] = $logicUser->getUserName($data['from_user_id']);
                $arrSubComment['list'][$index]['to_name']   = $logicUser->getUserName($data['to_user_id']);
            }
            $ret['list'][$key]['subComment'] = $arrSubComment['list'];
        }
        return $ret;
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
     * 获取最近的评论数
     * @param integer $objId
     * @param string  $during
     * @return integer
     */
    public function getLateCommentNum($objId,$during='',$type = Comment_Type_Type::TOPIC){
        if(empty($during)){
            $from = 0;
        }else{
            $from = strtotime($during.' days ago');
        }
        $redis = Base_Redis::getInstance();
        $ret = $redis->hGet(Comment_Keys::getCommentKey(),Comment_Keys::getLateKey($objId,$during));
        if(!empty($ret)){
            return $ret;
        }
        $list   = new Comment_List_Comment();
        $filter = "`obj_id` = $objId and `create_time` >= $during and `up_id` = 0 and `type` = ".$type;
        $list->setPagesize(PHP_INT_MAX);
        $list->setFilterString($filter);
        $arrRet = $list->toArray();
        $redis->hSet(Comment_Keys::getCommentKey(),Comment_Keys::getLateKey($objId,$during),$arrRet['total']);
        return $arrRet['total'];
    }
    
    /**
     * 获取话题总的评论数
     * @param integer $objId
     * @param string  $type
     * @return integer
     */
    public function getTotalCommentNum($objId,$type = Comment_Type_Type::TOPIC){        
        $redis = Base_Redis::getInstance();
        $ret = $redis->hGet(Comment_Keys::getCommentKey(),Comment_Keys::getTotalKey($objId));
        if(!empty($ret)){
            return $ret;
        }
        $list   = new Comment_List_Comment();
        $list->setPagesize(PHP_INT_MAX);
        $list->setFilter(array('obj_id' => $objId,'up_id' => 0,'type' => $type));
        $arrRet = $list->toArray();
        $redis->hSet(Comment_Keys::getCommentKey(),Comment_Keys::getTotalKey($objId),$arrRet['total']);
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
        $redis->hDel(Comment_Keys::getCommentKey(),Comment_Keys::getLateKey($objComment->objId, '*'));
        $redis->hDel(Comment_Keys::getCommentKey(),Comment_Keys::getTotalKey($objComment->objId));
        $objComment->status = Comment_Type_Status::DELETED;
        return $objComment->save();
    }
}