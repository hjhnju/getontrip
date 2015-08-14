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
           
           //处理来源
           $sourceInfo = Source_Api::getSourceInfo($postInfo['from']);
           $postInfo['from_name'] = $sourceInfo['name'];

           $this->getView()->assign('post', $postInfo); 
       } 

       //判断是否来自移动端
       $isMobile = Base_Util_Mobile::isMobile();
       //判断是否来自app
       $isApp = isset($_REQUEST['fromapp'])? (bool)$_REQUEST['fromapp'] : false; 
       $this->getView()->assign('isMobile', $isMobile); 
       $this->getView()->assign('isApp', $isApp); 

       
    }
    

    /**
     *  预览页面
    */
    public function previewAction() {             
       $postid = isset($_REQUEST['id'])? intval($_REQUEST['id']) : 0;  
       $postInfo = Topic_Api::getTopicById($postid);
       if(!isset($postInfo['id'])){
          $this->getView()->assign('post', array()); 
       }else{
           //处理状态值  
           $postInfo["statusName"] = Topic_Type_Status::getTypeName($postInfo["status"]);  
           
           //处理来源
           $sourceInfo = Source_Api::getSourceInfo($postInfo['from']);
           $postInfo['from_name'] = $sourceInfo['name'];

           //处理背景图片
           if(!empty($postInfo["image"])){
             $imgParams = Base_Image::getImgParams($postInfo["image"]);
             $postInfo["img_hash"] = $imgParams['img_hash'];
             $postInfo["img_type"] = $imgParams['img_type'];
           }

           //处理正文图片
           $content = $postInfo['content'];  
           if($content != ""){
              $spider = Spider_Factory::getInstance("Filterimg",$content,Spider_Type_Source::STRING);
              $postInfo['content'] = $spider->getContentToDis(); 
           }

           $this->getView()->assign('post', $postInfo); 
       } 
       
       //判断是否来自移动端
       $isMobile = Base_Util_Mobile::isMobile();
       //判断是否来自app
       $isApp = isset($_REQUEST['isApp'])? (bool)$_REQUEST['isApp'] : false; 
       $this->getView()->assign('isMobile', $isMobile); 
       $this->getView()->assign('isApp', $isApp);   
    }
}
