<?php
/**
 * 评论接口层
 * @author huwei
 *
 */
class Comment_Api{
    
    /**
     * 接口1：Comment_Api::getComments($page,$pageSize,$arrParam = array(),$type = '', $objId='')
     * 获取评论列表
     * @param integer $page
     * @param integer $pageSize
     * @param array   $arrParam
     * @param integer $type
     * @param integer $objId
     * @return array
     */
    public static function getComments($page,$pageSize,$arrParam = array(),$type = '', $objId = ''){
        $logicComment = new Comment_Logic_Comment();
        return $logicComment->getComments($page,$pageSize,$arrParam,$type,$objId);
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
     * 接口3：Comment_Api::addComment($objId,$deviceId,$toUserId,$content)
     * 添加评论信息
     * @param integer $objId
     * @param integer $upId,上层评论ID
     * @param string $deviceId
     * @param integer $toUserId
     * @param string $content
     * @return boolean
     */
    public function  addComment($objId,$upId,$deviceId,$toUserId,$content,$type){
        $logicComment = new Comment_Logic_Comment();
        return $logicComment->addComment($objId,$upId,$deviceId, $toUserId, $content,$type);
    }
    
    /**
     * 接口4：Comment_Api::delComment($id)
     * 删除评论
     * @param integer $id
     * @return boolean
     */
    public static function  delComment($id){
        $logicComment = new Comment_Logic_Comment();
        return $logicComment->delComment($id);
    }
    
    /**
     * 接口5：changeCommentStatus($id,$status)
     * 改变评论的状态
     * @param integer $id
     * @param integer $status
     * @return boolean
     */
    public static function changeCommentStatus($id,$status){
        $logicComment = new Comment_Logic_Comment();
        return $logicComment->changeCommentStatus($id, $status);
    }
}