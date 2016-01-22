<?php
/**
 * 编辑景点词条
 * @author fyy
 *
 */
class EditAction extends Yaf_Action_Abstract {
    
    public function execute() {
       $action  = isset($_REQUEST['action'])?$_REQUEST['action']:'add'; 
       if($action=='add'){
          //处理传递过来的景点
          $sight_id  = isset($_REQUEST['sight_id'])?intval($_REQUEST['sight_id']):'';
          if($sight_id!=''){
             $sight=Sight_Api::getSightById($sight_id); 
             $this->getView()->assign('sight', $sight);
          } 
        }
        $postid = isset($_REQUEST['id']) ? $_REQUEST['id'] : '0';
        if($postid==''){
            $this->getView()->assign('post', '');
        }
        $postInfo=Keyword_Api::queryById($postid);
     
        if(!empty($postInfo)){  
           $level = intval($postInfo['level']);
           if($level== Keyword_Type_Level::SIGHT){
               $sightInfo  = Sight_Api::getSightById($postInfo['sight_id']);
               $postInfo["sight_name"]=isset($sightInfo["name"])?$sightInfo['name']:'';
           }elseif($level == Keyword_Type_Level::CITY){
               $cityInfo   = City_Api::getCityById($postInfo['sight_id']);
               $postInfo["sight_name"]=isset($cityInfo["name"])?$cityInfo['name']:'';
           }elseif($level == Keyword_Type_Level::LANDSCAPE){
               $sightInfo  = Sight_Api::getSightById($postInfo['sight_id']);
               $postInfo["sight_name"]=isset($sightInfo["name"])?$sightInfo['name']:'';
           }else{
               $keywordInfo   = Keyword_Api::queryById($postInfo['sight_id']);
               $postInfo["sight_name"]=isset($keywordInfo["name"])?$keywordInfo["name"]:'';
           }
            //处理状态值
           $postInfo["status_name"]=Keyword_Type_Status::getTypeName($postInfo['status']);
           
           $this->getView()->assign('post', $postInfo); 
           
        }
 
        if($action=="view"){ 
            $this->_view->assign('disabled', 'disabled');
        } 
        
        $this->getView()->assign('action', Admin_Type_Action::getTypeName($action));
    }
}
