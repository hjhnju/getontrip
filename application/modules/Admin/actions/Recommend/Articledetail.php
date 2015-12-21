<?php
/**
 * 文章详情
 * @author fyy
 *
 */
class ArticledetailAction extends Yaf_Action_Abstract {
     
    public function execute() {             
       $postid = isset($_REQUEST['id'])? intval($_REQUEST['id']) : 0; 
       
       if(empty($postid)){
            return $this->ajaxError(Base_RetCode::PARAM_ERROR,Base_RetCode::getMsg(Base_RetCode::PARAM_ERROR));
       }

       
       $postInfo    = Recommend_Api::getArticleDetail($postid); 
       //$postInfo = Topic_Api::getTopicById($postid);
       if(!isset($postInfo['id'])){
          $this->getView()->assign('post', array()); 
       }else{ 
         
           $this->getView()->assign('title', $postInfo['title']); 
           $this->getView()->assign('post', $postInfo); 
       } 
       
       //判断是否来自移动端
       $isMobile = Base_Util_Mobile::isMobile();
       //判断是否来自app
       $isapp = isset($_REQUEST['isapp'])?$_REQUEST['isapp'] : 0; 
       $this->getView()->assign('isMobile', $isMobile); 
       $this->getView()->assign('isapp', $isapp); 
       //$this->getView()->assign('isfromApp', $isapp); 

       
    }
}