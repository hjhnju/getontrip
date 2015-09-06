<?php
/**
 * 编辑
 * @author fyy
 *
 */
class EditAction extends Yaf_Action_Abstract {
    
    public function execute() {
       $action  = isset($_REQUEST['action'])?$_REQUEST['action']:'add'; 
       if($action=='add'){
           
        }
        $postid = isset($_REQUEST['id']) ? $_REQUEST['id'] : '';
        if(empty($postid)){
            $this->getView()->assign('post', '');
        }else{
            $List=Msg_Api::queryMsg(1, 1,array('mid'=>$postid)); 
            $postInfo=$List['list'][0];
            if(!empty($postInfo)){  
                //处理类型值
               $postInfo["type_name"]=Msg_Type_Type::getTypeName($postInfo['type']);
               
                //处理状态值
               $postInfo["status_name"]=Msg_Type_Status::getTypeName($postInfo['status']);
               
               $this->getView()->assign('post', $postInfo); 
               
            }
        }
        
 
        if($action=="view"){ 
            $this->_view->assign('disabled', 'disabled');
        } 
        
        $this->getView()->assign('action', Admin_Type_Action::getTypeName($action));
    }
}
