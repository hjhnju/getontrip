<?php
/**
 * 新建编辑 
 * @author fanyy
 *
 */
class EditAction extends Yaf_Action_Abstract {
    public function execute() {

        $action  = isset($_REQUEST['action'])?$_REQUEST['action']:'add'; 
        
 
        //获取所有标签
        $tagList = Tag_Api::getTagList(1, PHP_INT_MAX);
        $tagList=$tagList['list'];
      
        $sightList=array();

        $postid = isset($_REQUEST['id']) ? $_REQUEST['id'] : '0';
       
        $postInfo=Theme_Api::queryThemeById($postid); 

        if(empty($postInfo)){
            $action='add';
        }else{

           //处理图片 去掉pic
           $postInfo["image"] = Base_Image::getImgNameBySrc($postInfo["image"]);
 
           //处理状态值  
           $postInfo["statusName"] = Theme_Type_Status::getTypeName($postInfo["status"]);  
           
           //处理所选景观
            $landscapeList=$postInfo['landscape']; 

             //处理图片名称 分割为hash 和 img_type 
            if(!empty($postInfo["image"])){
               $img=Base_Image::getImgParams($postInfo["image"]);
               $postInfo["img_hash"] = $img['img_hash'];
               $postInfo["img_type"] = $img['img_type'];
            } 
             
             $this->getView()->assign('post', $postInfo);

         }
         
        
        

        if($action==Admin_Type_Action::ACTION_VIEW){
            $this->getView()->assign('disabled', 'disabled');
        }  
    
        $this->getView()->assign('action', Admin_Type_Action::getTypeName($action));
         
        $this->getView()->assign('landscapeList', $landscapeList);
    }
}
