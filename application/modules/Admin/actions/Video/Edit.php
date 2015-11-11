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
        else{ 
           $postInfo  = Video_Api::getVideoInfo($postid); 
        }
        
        $sightSelectedList=array();
        if(!empty($postInfo)){    
            //处理状态值
            $postInfo["statusName"]=Video_Type_Status::getTypeName($postInfo['status']);
 
            //处理所选景点 
            if (isset($postInfo['sights'])) {
                $sightSelectedList = $postInfo['sights'];
            }  
           
            $this->getView()->assign('post', $postInfo); 
        }
 
        if($action=="view"){ 
            $this->_view->assign('disabled', 'disabled');
        } 

        $this->getView()->assign('action', Admin_Type_Action::getTypeName($action));
        $this->_view->assign('sightList', $sightSelectedList);
        
      
    }
}
