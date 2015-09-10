<?php
/**
 * 新建编辑 
 * @author fanyy
 *
 */
class EditAction extends Yaf_Action_Abstract {
    public function execute() {

        $action  = isset($_REQUEST['action'])?$_REQUEST['action']:'add';  
        $postid = isset($_REQUEST['id']) ? $_REQUEST['id'] : '0';
       
        $postInfo=Landscape_Api::queryLandscapeById($postid); 

        if(empty($postInfo)){
            $action='add';  

        }else{
 
           //处理状态值  
           $postInfo["statusName"] = Landscape_Type_Status::getTypeName($postInfo["status"]); 
           
            //获取城市名称
           $cityInfo=City_Api::getCityById($postInfo["city_id"]);
           $postInfo["city_name"]=$cityInfo["name"]; 

           //处理图片，去掉路径
           
           $this->getView()->assign('post', $postInfo);
           

         } 

        if($action==Admin_Type_Action::ACTION_VIEW){
            $this->getView()->assign('disabled', 'disabled');
        } 
          
        $this->getView()->assign('action', Admin_Type_Action::getTypeName($action));
      
    }
}
