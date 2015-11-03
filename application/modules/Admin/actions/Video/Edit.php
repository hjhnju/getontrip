<?php
/**
 * 编辑视频
 * @author fyy
 *
 */
class EditAction extends Yaf_Action_Abstract {
    
    public function execute() { 

        $typeArray=Video_Type_Type::$names;
        $typeArray=array_reverse($typeArray,true);
        $this->getView()->assign('typeArray', $typeArray); 

        $action = isset($_REQUEST['action'])?$_REQUEST['action']:'add';  

        $postid = isset($_REQUEST['id']) ? $_REQUEST['id'] : '';
        if($postid==''){
            $this->getView()->assign('post', '');
        }
        $postInfo  = Video_Api::getVideoInfo($postid); 
        if(!empty($postInfo)){    
            //处理状态值
            $postInfo["statusName"]=Video_Type_Status::getTypeName($postInfo['status']);

            //处理景点名称
            $sightInfo = Sight_Api::getSightById($postInfo['sight_id']);
            $postInfo['sight_name'] = $sightInfo['name'];
           
            $this->getView()->assign('post', $postInfo); 
        }
 
        if($action=="view"){ 
            $this->_view->assign('disabled', 'disabled');
        } 
        $this->getView()->assign('action', Admin_Type_Action::getTypeName($action));
        
      
    }
}
