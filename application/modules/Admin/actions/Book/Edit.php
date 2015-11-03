<?php
/**
 * 编辑书籍
 * @author fyy
 *
 */
class EditAction extends Yaf_Action_Abstract {
    
    public function execute() { 

        $action = isset($_REQUEST['action'])?$_REQUEST['action']:'add';  

        $postid = isset($_REQUEST['id']) ? $_REQUEST['id'] : '';
        if($postid==''){
            $this->getView()->assign('post', '');
        }
        $postInfo  = Book_Api::getBookInfo($postid); 
         if(!empty($postInfo)){    
             //处理状态值
             $postInfo["status_name"]=Book_Type_Status::getTypeName($postInfo['status']);
           
             $this->getView()->assign('post', $postInfo); 
        }
 
        if($action=="view"){ 
            $this->_view->assign('disabled', 'disabled');
        } 
        $this->getView()->assign('action', Admin_Type_Action::getTypeName($action));
        
      
    }
}
