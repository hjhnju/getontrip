<?php
/**
 * 编辑
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
        $postInfo=Admin_Api::getAdminById($postid);
     
        if(!empty($postInfo)){ 
            //处理角色名称
           $postInfo["role_name"]=Admin_Type_Role::getTypeName($postInfo['role']); 

           $this->getView()->assign('post', $postInfo); 
           
        }

        $roleArray=Admin_Type_Role::$names; 

        $this->getView()->assign('roleArray', $roleArray); 
        $this->getView()->assign('action', Admin_Type_Action::getTypeName($action));
    }
}
