<?php
/**
 * 编辑景点词条
 * @author fyy
 *
 */
class EditAction extends Yaf_Action_Abstract {
    
    public function execute() {
       $action  = isset($_REQUEST['action'])?$_REQUEST['action']:'add'; 
       $postid = isset($_REQUEST['id']) ? $_REQUEST['id'] : '';
        if($postid==''){
            $this->getView()->assign('post', '');
        }
        $postInfo=Keyword_Api::queryById($postid);
     
        if(isset($postInfo)){  
           //获取景点名称
           $sightInfo  = Sight_Api::getSightById($postInfo['sight_id']); 
           $postInfo["sight_name"]=$sightInfo["name"]; 
           $this->getView()->assign('post', $postInfo);
        }
 
        if($action=="view"){ 
            $this->_view->assign('disabled', 'disabled');
        } 
        
        $this->getView()->assign('action', Admin_Type_Action::getTypeName($action));
    }
}
