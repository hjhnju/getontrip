<?php
/**
 * 评论接口层
 * @author huwei
 *
 */
class Comment_Api{
    
    /**
     * 接口1：Comment_Api::getCommentList($topicId,$page,$pageSize)
     * 获取话题的评论列表
     * @param integer $topicId
     * @param integer $page
     * @param integer $pageSize
     * @return array
     */
    public static function getCommentList($topicId,$page,$pageSize){
        $logicComment = new Comment_Logic_Comment();
        return $logicComment->getCommentList($topicId, $page, $pageSize);
    }
    
    /**
     * 接口2：Comment_Api::getCommnetById($id)
     * 根据ID获取评论详情
     * @param integer $id
     * @return array
     */
    public static function getCommnetById($id){
        $logicComment = new Comment_Logic_Comment();
        return $logicComment->getCommentById($id);
    }
    
    /**
     * 接口3：Comment_Api::addComment($topicId,$deviceId,$toUserId,$content)
     * 添加评论信息
     * @param integer $topicId
     * @param integer $upId,上层评论ID
     * @param string $deviceId
     * @param integer $toUserId
     * @param string $content
     * @return boolean
     */
    public function  addComment($topicId,$upId,$deviceId,$toUserId,$content){
        $logicComment = new Comment_Logic_Comment();
        return $logicComment->addComment($topicId,$upId,$deviceId, $toUserId, $content);
    }
    
    /**
     * 接口4：Comment_Api::delComment($id)
     * @param integer $id
     * @return boolean
     */
    public static function  delComment($id){
        $logicComment = new Comment_Logic_Comment();
        return $logicComment->delComment($id);
    }
}