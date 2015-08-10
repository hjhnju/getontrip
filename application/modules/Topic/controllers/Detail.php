<?php
/**
 * 话题详情页面
 * @author fyy
 */
class DetailController extends Base_Controller_Page {
    
     
   
    public function init() {
        $this->setNeedLogin(false);
        parent::init();
    }
    
    /**
     *  详情
     */
    public function indexAction() {             
       $postid = isset($_REQUEST['id'])? intval($_REQUEST['id']) : 0; 
        
       $postInfo = Topic_Api::getTopicById($postid);
	   if(!empty($postInfo)){
	       //处理状态值  
	       $postInfo["statusName"] = Topic_Type_Status::getTypeName($postInfo["status"]);  
	       $this->getView()->assign('post', $postInfo);  
       } 

       
    }
    

    /**
     *  
    */
    public function previewAction() {             
       $postid = isset($_REQUEST['id'])? intval($_REQUEST['id']) : 0;  
       $postInfo = Topic_Api::getTopicById($postid);
	   if(!empty($postInfo)){
	       //处理状态值  
	       $postInfo["statusName"] = Topic_Type_Status::getTypeName($postInfo["status"]);  
	       $this->getView()->assign('post', $postInfo);  
       } 
    }
}
