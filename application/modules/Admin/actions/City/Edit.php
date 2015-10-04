<?php
/**
 * 新建编辑 城市
 * @author fanyy
 *
 */
class EditAction extends Yaf_Action_Abstract {
    public function execute() {

        $action  = isset($_REQUEST['action'])?$_REQUEST['action']:'add'; 
         
        $postid = isset($_REQUEST['id']) ? $_REQUEST['id'] : '0';
       
        $postInfo=City_Api::getCityById($postid); 

        if(empty($postInfo)){
            $action='add'; 

        }else { 
            if(empty($postInfo["status"])){
                $postInfo["x"]=0;
                $postInfo["y"]=0; 
            }  
           $this->getView()->assign('post', $postInfo); 

        } 
        if($action==Admin_Type_Action::ACTION_VIEW){
            $this->getView()->assign('disabled', 'disabled');
        } 
         
        $this->getView()->assign('action', Admin_Type_Action::getTypeName($action)); 
    }
}
